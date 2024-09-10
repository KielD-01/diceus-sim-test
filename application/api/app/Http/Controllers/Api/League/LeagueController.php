<?php

namespace App\Http\Controllers\Api\League;

use App\Helpers\JsonResponseHelper;
use App\Http\Controllers\Api\ApiController;
use App\Models\League;
use App\Services\GameService;
use App\Services\LeagueService;
use Illuminate\Http\JsonResponse;

class LeagueController extends ApiController
{
    private LeagueService $leagueService;
    private GameService $gameService;

    public function __construct()
    {
        parent::__construct();
        $this->gameService = resolve(GameService::class);
        $this->leagueService = resolve(LeagueService::class);
    }

    /**
     * @param League $league
     * @return JsonResponse
     */
    public function play(League $league): JsonResponse
    {
        $this->gameService->play($league->games);

        return JsonResponseHelper::success($this->leagueService->setLeague(true));
    }

    /**
     * @param League $league
     * @return JsonResponse
     */
    public function nextWeek(League $league): JsonResponse
    {
        if (!($result = $this->leagueService->progress($league))) {
            return JsonResponseHelper::error([
                'League Games are not yet finished'
            ]);
        }

        $this->leagueService->setLeague(true);

        return JsonResponseHelper::success([
            'success' => $result
        ]);
    }
}
