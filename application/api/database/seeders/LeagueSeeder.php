<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $league = League::query()->create([
            'name' => 'GB Championship ' . Carbon::now()->year,
        ]);

        $teams = Team::all();
        $teams->each(fn(Team $team) => $league->leagueTeams()->firstOrCreate(['team_id' => $team->id]));

        $this->prepareMatches($teams)
            ->map(fn(array $game) => $league->games()->create($game));
    }

    /**
     * Preparing matches sets for a league
     *
     * @param Collection $teams
     * @return Collection
     */
    private function prepareMatches(Collection $teams): Collection
    {
        $matches = collect();

        $teams->each(function (Team $team) use ($teams, &$matches) {
            $teams->filter(fn(Team $target) => $target->id !== $team->id)
                ->each(fn(Team $target) => $matches->push(collect([$team, $target])));
        });

        if ($matches->count() % 2 !== 0) {
            throw new RuntimeException('Teams count should be paired (2, 4, 6, etc.)');
        }

        $totalWeeks = $matches->count() / 2;
        $maxWeekPairs = $teams->count() / 2;

        $currentWeek = 1;
        $games = collect();

        while ($currentWeek <= $totalWeeks) {
            $weekGames = collect();

            while ($weekGames->count() < $maxWeekPairs) {
                /** @var Collection $match */
                $match = $matches
                    ->filter(function (Collection $match) use ($weekGames) {
                        if ($weekGames->isEmpty()) {
                            return true;
                        }

                        [$homeTeam, $guestTeam] = $match;

                        return $weekGames
                            ->filter(function (Collection $weekGame) use ($homeTeam, $guestTeam) {
                                return $weekGame
                                        ->pluck('id')
                                        ->diff([$homeTeam->id, $guestTeam->id])
                                        ->count() !== 2;
                            })
                            ->isEmpty();
                    })
                    ->random(1)
                    ->first;

                $weekGames->push($match->first());

                [$homeTeam, $guestTeam] = $match->toArray();

                $games->push([
                    'home_team_id' => $homeTeam->id,
                    'guest_team_id' => $guestTeam->id,
                    'week' => $currentWeek,
                ]);
            }

            $this->filterMatches($matches, $games);

            $currentWeek++;
        }

        return $games;
    }

    /**
     * @param $matches
     * @param Collection $games
     * @return void
     */
    private function filterMatches(&$matches, Collection $games): void
    {
        $matches = $matches->filter(function (Collection $teams) use ($games) {
            [$homeTeam, $guestTeam] = $teams;

            return $games
                ->filter(
                    fn(array $game) => $homeTeam->id === $game['home_team_id'] &&
                        $guestTeam->id === $game['guest_team_id']
                )
                ->isEmpty();
        });
    }
}
