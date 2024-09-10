<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $active
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Team extends Model
{
    protected $guarded = [
        'id'
    ];
}
