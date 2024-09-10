<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $game_id
 * @property int $time
 * @property string $action
 * @property int $team_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Game|BelongsTo $game
 * @property Team|BelongsTo $team
 */
class GameLog extends Model
{
    protected $guarded = [
        'id'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
