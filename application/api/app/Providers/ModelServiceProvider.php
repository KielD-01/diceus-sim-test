<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;
use App\Observers\GameObserver;
use App\Observers\LeagueObserver;
use App\Observers\LeagueTeamObserver;
use App\Observers\TeamObserver;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Game::observe(GameObserver::class);
        Team::observe(TeamObserver::class);
        League::observe(LeagueObserver::class);
        LeagueTeam::observe(LeagueTeamObserver::class);
    }
}
