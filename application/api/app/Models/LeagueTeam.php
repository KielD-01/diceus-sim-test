<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Data structure
 * @property int $id
 * @property int $league_id
 * @property int $team_id
 * @property object|array $statistics
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * * Model Relationships
 * @property League|BelongsTo $league
 * @property Team|BelongsTo $team
 */
class LeagueTeam extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'statistics' => 'json'
    ];

    /**
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }
}
