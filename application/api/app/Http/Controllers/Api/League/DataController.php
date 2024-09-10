<?php

namespace App\Http\Controllers\Api\League;

use App\Helpers\JsonResponseHelper;
use App\Http\Controllers\Api\ApiController;
use App\Services\LeagueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataController extends ApiController
{
    private LeagueService $leagueService;

    public function __construct()
    {
        parent::__construct();
        $this->leagueService = resolve(LeagueService::class);
    }

    /**
     * @return JsonResponse
     */
    public function league(): JsonResponse
    {
        return JsonResponseHelper::success(
            $this->leagueService->setLeague()
        );
    }

    /**
     * @return StreamedResponse
     */
    public function dataStream(): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            while (true) {
                $data = json_encode(
                    $this->leagueService->setLeague(),
                    JSON_THROW_ON_ERROR
                );

                echo "data: $data\n\n";

                ob_flush();
                flush();
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
