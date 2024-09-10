<?php

namespace App\Http\Resources;

use AllowDynamicProperties;
use App\Enums\Games\GameStatusesEnum;
use App\Models\Game;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Services\LeagueService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property LeagueService $leagueService
 * @mixin League
 */
#[AllowDynamicProperties] class DataResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->leagueService = resolve(LeagueService::class);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $league = $this->leagueService->getActiveLeague();
        $league?->load('leagueTeams.team');

        $games = $league?->games;

        return [
            'league' => $this->when(
                !is_null($league), [
                    'id' => $league->id,
                    'teams' => $this->when(
                        $league?->leagueTeams->isNotEmpty(),
                        LeagueTeamResource::collection(
                            $league->leagueTeams
                                ->sortByDesc(fn(LeagueTeam $a) => $a->statistics['pts'])
                                ->sortByDesc(fn(LeagueTeam $a) => $a->statistics['goals'])
                        ),
                        []
                    ),
                    'games' => $this->when(
                        $games->isNotEmpty(),
                        GameResource::collection($league->games),
                        []
                    ),
                    'name' => $league?->name ?? null,
                    'winner' => $league?->winner?->name ?? null,
                    'week' => $this->week,
                    'maxWeek' => $league->leagueGames->max('week'),
                    'meta' => [
                        'weekGames' => $games->count(fn(Game $game) => $game->week === $this->week),
                        'weekGamesPlayed' => $games
                            ->filter(
                                fn(Game $game) => $game->statusIs(GameStatusesEnum::FINISHED)
                            )
                            ->count(),
                        'ongoingGames' => $games
                            ->filter(
                                fn(Game $game) => $game->statusIs(GameStatusesEnum::PLAYING)
                            )
                            ->count() > 0,
                    ]
                ]
            )
        ];
    }
}
