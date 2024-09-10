<?php

namespace App\DataProcessors\Game;

use App\Enums\Games\GameResultsEnum;
use App\Enums\Games\GameStatusesEnum;
use App\Models\Game;
use App\Models\LeagueTeam;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GameResultsProcessor
{
    public function __construct(private Game $game)
    {
        if (!$this->game->statusIs(GameStatusesEnum::FINISHED)) {
            Log::info('game.info', compact('game'));
            throw new RuntimeException('Game has not been finished yet');
        }
    }

    /**
     * @param string $side
     * @return void
     */
    public function process(string $side): void
    {
        $keys = [
            'home' => [
                'entity' => 'leagueHomeTeam',
                'score' => 'home'
            ],
            'guest' => [
                'entity' => 'leagueGuestTeam',
                'score' => 'guest'
            ]
        ];

        $teamMeta = $keys[$side];
        $opponent = $side === 'home' ? 'guest' : 'home';

        /** @var LeagueTeam $leagueTeam */
        $leagueTeam = $this->game->{$teamMeta['entity']};
        $score = $this->game->scores;

        $statistics = $leagueTeam->statistics;

        $statistics['games']++;
        $statistics['goals'] += $score[$side];

        $key = match ($score[$side] <=> $score[$opponent]) {
            1 => GameResultsEnum::WINS,
            0 => GameResultsEnum::DRAW,
            -1 => GameResultsEnum::LOSE
        };

        $pts = match ($key) {
            GameResultsEnum::WINS => 3,
            GameResultsEnum::DRAW => 1,
            default => 0
        };

        $statistics[$key->value]++;
        $statistics['pts'] += $pts;

        $leagueTeam->update(compact('statistics'));
    }
}
