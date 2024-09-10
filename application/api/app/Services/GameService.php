<?php

namespace App\Services;

use App\DataProcessors\Game\GameResultsProcessor;
use App\Enums\Games\GameStatusesEnum;
use App\Jobs\PlayGameJob;
use App\Models\Game;
use App\Models\Team;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GameService
{

    /**
     * Creates a game between teams
     *
     * @param Team $home
     * @param Team $guest
     * @return Game|Model
     */
    public function create(Team $home, Team $guest): Model|Game
    {
        return Game::query()->create([
            'home_id' => $home->id,
            'guest_id' => $guest->id,
        ]);
    }

    /**
     * @param Game $game
     * @param GameStatusesEnum $gameStatus
     * @return Game
     */
    public function setGameStatus(Game $game, GameStatusesEnum $gameStatus): Game
    {
        $game->update(['status' => $gameStatus]);

        return $game->refresh();
    }

    /**
     * @param Game $game
     * @param Team $team
     * @param int $time
     * @return void
     * @throws Exception
     */
    public function score(Game $game, Team $team, int $time = 0): void
    {
        /** @var GameLogService $gameLogService */
        $gameLogService = resolve(GameLogService::class);
        $teamSide = $game->home_team_id === $team->id ? 'home' : 'guest';

        $scores = $game->scores;
        $scores[$teamSide]++;

        $game->scores = $scores;
        $game->save();

        $gameLogService->logActionFor(
            $game,
            sprintf('%s has scored a goal!', $team->name),
            $time
        );
    }

    /**
     * @param Collection $games
     * @return void
     */
    public function play(Collection $games): void
    {
        $games->each(fn (Game $game) => PlayGameJob::dispatch($game));
    }

    /**
     * @param Game $game
     * @return void
     */
    public function recordScoreFrom(Game $game): void
    {
        $processor = new GameResultsProcessor($game);

        $processor->process('home');
        $processor->process('guest');
    }
}
