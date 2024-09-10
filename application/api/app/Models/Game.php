<?php

namespace App\Models;

use App\Enums\Games\GameStatusesEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * * Data structure
 * @property int $id
 * @property int $league_id
 * @property int $home_team_id
 * @property int $guest_team_id
 * @property int $week
 * @property object|array $scores
 * @property GameStatusesEnum $status
 * @property int $game_time
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * * Magic (dynamic) Attrs
 * @property string $vs
 *
 * * Model Relationships
 * @property League|BelongsTo $league
 * @property Team|BelongsTo $homeTeam
 * @property LeagueTeam|BelongsTo $leagueHomeTeam
 * @property LeagueTeam|BelongsTo $leagueGuestTeam
 * @property Team|BelongsTo $guestTeam
 * @property GameLog|HasMany $logs
 */
class Game extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'scores' => 'json',
        'status' => GameStatusesEnum::class
    ];

    /**
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * @return BelongsTo
     */
    public function leagueHomeTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class, 'home_team_id', 'team_id')
            ->where('league_id', $this->league_id);
    }

    /**
     * @return BelongsTo
     */
    public function leagueGuestTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class, 'guest_team_id', 'team_id')
            ->where('league_id', $this->league_id);
    }

    /**
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * @return BelongsTo
     */
    public function guestTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'guest_team_id');
    }

    /**
     * @return Collection
     */
    public function teams(): Collection
    {
        return Team::query()
            ->whereIn('id', [$this->home_team_id, $this->guest_team_id])
            ->get();
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(GameLog::class);
    }

    public function statusIs(GameStatusesEnum $gameStatus): bool
    {
        return $this->status === $gameStatus;
    }

    /**
     * @return Attribute
     */
    public function vs(): Attribute
    {
        return Attribute::make(
            get: fn() => sprintf(
                '%s - %s',
                $this->homeTeam->name,
                $this->guestTeam->name
            )
        );
    }
}
