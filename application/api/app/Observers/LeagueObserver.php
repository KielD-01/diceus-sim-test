<?php

namespace App\Observers;

use App\Models\League;
use Illuminate\Support\Str;

class LeagueObserver
{
    public function creating(League $league): void
    {
        $league->slug = Str::slug($league->name);
    }
}
