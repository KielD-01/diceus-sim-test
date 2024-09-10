<?php

namespace App\Services;

use App\Enums\Games\GameStatusesEnum;
use App\Models\Game;
use App\Models\GameLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class GameLogService
{
    /**
     * @param Game $game
     * @param string|null $action
     * @param int $time
     * @param bool $force
     * @return Model|GameLog
     * @throws Exception
     */
    public function logActionFor(Game $game, ?string $action = null, int $time = 0, bool $force = false): Model|GameLog
    {
        if (!$force && !$game->statusIs(GameStatusesEnum::PLAYING)) {
            Log::info('game.status', ['status' => $game->status->value]);
            $action = match ($game->status->value) {
                GameStatusesEnum::SCHEDULED->value => 'Game has not started yet',
                GameStatusesEnum::FINISHED->value => 'Game has been already finished',
                default => $game->status->value,
            };

            throw new \RuntimeException(
                sprintf('Can\'t log action for this game. %s', $action)
            );
        }

        return $game->logs()->create(
            compact(
                'action',
                'time'
            )
        );
    }
}
