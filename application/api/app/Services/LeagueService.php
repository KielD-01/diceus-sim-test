<?php

namespace App\Services;

use App\Enums\Games\GameStatusesEnum;
use App\Http\Resources\DataResource;
use App\Models\League;
use Illuminate\Support\Facades\Cache;

class LeagueService
{
    /**
     * Get an Active League
     *
     * @return League|null
     */
    public function getActiveLeague(): ?League
    {
        return League::query()->where('active', true)->first();
    }

    /**
     * @param bool $refresh
     * @return DataResource
     */
    public function setLeague(bool $refresh = false): DataResource
    {
        if ($refresh || ($league = Cache::get('league')) === null) {
            $league = new DataResource(
                $this->getActiveLeague()
            );

            if (!app()->environment('local')) {
                Cache::forever('league', $league);
            }
        }

        return $league;
    }

    /**
     * @param League $league
     * @return bool
     */
    public function progress(League $league): bool
    {
        $hasUnfinishedGames = $league
                ->games()
                ->whereIn('status', [
                    GameStatusesEnum::PLAYING,
                    GameStatusesEnum::SCHEDULED
                ])
                ->count() > 0;

        if ($hasUnfinishedGames) {
            return false;
        }

        return $league->update(['week' => $league->week + 1]);
    }
}
