<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * * Data structure
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $week
 * @property int $winner_team_id
 * @property bool $active
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * * Model Relationships
 *
 * @property Team|HasOne $winnerTeam
 * @property Team[]|Collection|HasManyThrough $teams
 * @property LeagueTeam[]|Collection|HasMany $leagueTeams
 * @property Game[]|Collection|HasMany $games
 * @property Game[]|Collection|HasMany $leagueGames
 */
class League extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * @return HasOne
     */
    public function winnerTeam(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    /**
     * @return HasMany
     */
    public function leagueTeams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class);
    }

    /**
     * @return HasManyThrough
     */
    public function teams(): HasManyThrough
    {
        return $this->hasManyThrough(
            Team::class,
            LeagueTeam::class,
            'league_id',
            'id',
            'id',
            'team_id'
        );
    }

    /**
     * @return HasMany
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class)
            ->where('week', $this->week);
    }

    /**
     * @return HasMany
     */
    public function leagueGames(): HasMany
    {
        return $this->hasMany(Game::class, 'league_id');
    }

}
