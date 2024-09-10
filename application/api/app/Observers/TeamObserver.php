<?php

namespace App\Observers;

use App\Models\Team;
use Illuminate\Support\Str;

class TeamObserver
{

    public function creating(Team $team): void
    {
        $team->slug = Str::slug($team->name);
    }
}
