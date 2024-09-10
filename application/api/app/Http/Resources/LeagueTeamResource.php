<?php

namespace App\Http\Resources;

use App\Models\LeagueTeam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LeagueTeam
 */
class LeagueTeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->team->name,
            'stats' => $this->statistics,
            'hash' => md5($this->league_id . $this->team->id),
        ];
    }
}
