<?php

namespace App\Jobs;

use AllowDynamicProperties;
use App\Enums\Games\GameStatusesEnum;
use App\Models\Game;
use App\Models\Team;
use App\Services\GameLogService;
use App\Services\GameService;
use App\Services\LeagueService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

#[AllowDynamicProperties] class PlayGameJob implements ShouldQueue
{
    use Queueable;

    private array $possibilities = [
        'goal' => 7,
        'penalty' => 3,
        'pass' => 70,
        'nothing' => 20
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Game $game,
    )
    {
        $this->onQueue('league-game');
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        /** @var GameService $gameService */
        $gameService = resolve(GameService::class);

        /** @var LeagueService $leagueService */
        $leagueService = resolve(LeagueService::class);

        /** @var GameLogService $gameLogService */
        $gameLogService = resolve(GameLogService::class);

        $ticks = $this->game->game_time;
        $time = 0;
        $teams = $this->game->teams();

        $this->game = $gameService->setGameStatus($this->game, GameStatusesEnum::PLAYING);

        while ($time < $ticks) {
            $team = $teams->random(1)->first();
            $this->pullActionFor($gameService, $team, $time);

            $time++;
        }

        $gameLogService->logActionFor(
            $this->game,
            sprintf(
                'The match `%s` has finished! Score %d - %d',
                $this->game->vs,
                $this->game->scores['home'],
                $this->game->scores['guest']
            ),
            time: $time
        );

        $this->game = $gameService->setGameStatus($this->game, GameStatusesEnum::FINISHED);

        $leagueService->setLeague();
    }

    /**
     * @param GameService $gameService
     * @param Team $team
     * @param int $time
     * @return void
     * @throws Exception
     */
    private function pullActionFor(GameService $gameService, Team $team, int $time): void
    {
        $actions = collect();

        foreach ($this->possibilities as $type => $chance) {
            foreach (range(1, $chance) as $i) {
                $actions->push($type);
            }
        }

        $actions = $actions->shuffle();

        [$action] = $actions->random(1)->toArray();

        switch ($action) {
            case 'goal':
            case 'penalty':
                $gameService->score($this->game, $team, $time);
                break;
        }
    }

}
