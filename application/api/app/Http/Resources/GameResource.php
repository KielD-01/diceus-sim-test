<?php

namespace App\Http\Resources;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Game
 */
class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'match' => $this->vs,
            'scores' => $this->scores,
            'status' => $this->status,
            'hash' => md5($this->id)
        ];
    }
}
