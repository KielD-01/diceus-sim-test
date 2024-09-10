<?php

namespace App\Observers;

use App\Enums\Games\GameStatusesEnum;
use App\Models\Game;
use App\Services\GameLogService;
use App\Services\GameService;
use App\Services\LeagueService;
use Exception;

class GameObserver
{
    public function created(Game $game): void
    {
        $game->updateQuietly([
            'scores' => [
                'home' => 0,
                'guest' => 0,
            ]
        ]);
    }

    /**
     * @param Game $game
     * @return void
     * @throws Exception
     */
    public function updated(Game $game): void
    {
        if ($game->isDirty('status')) {
            if (!in_array($game->status, [GameStatusesEnum::PLAYING, GameStatusesEnum::FINISHED], true)) {
                return;
            }

            /** @var GameLogService $gameLogService */
            $gameLogService = resolve(GameLogService::class);

            $gameLogService->logActionFor(
                $game,
                sprintf(
                    'The match between `<b>%s</b>` %s!',
                    $game->vs,
                    match ($game->status) {
                        GameStatusesEnum::PLAYING => 'has started',
                        GameStatusesEnum::FINISHED => 'has finished',
                    }
                ),
                90,
                force: true
            );

            if ($game->status === GameStatusesEnum::FINISHED) {
                /** @var GameService $gameService */
                $gameService = resolve(GameService::class);
                /** @var LeagueService $leagueService */
                $leagueService = resolve(LeagueService::class);

                $gameService->recordScoreFrom($game);
                $leagueService->setLeague(true);
            }
        }
    }
}
