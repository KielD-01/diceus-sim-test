<?php

namespace App\Observers;

use App\Models\LeagueTeam;

class LeagueTeamObserver
{
    /**
     * @param LeagueTeam $leagueTeam
     * @return void
     */
    public function created(LeagueTeam $leagueTeam) : void
    {
        $leagueTeam->updateQuietly([
            'statistics' => [
                'pts' => 0,
                'games' => 0,
                'wins' => 0,
                'losses' => 0,
                'draws' => 0,
                'goals' => 0
            ]
        ]);
    }
}
