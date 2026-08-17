<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()?->is($this->resource) || $request->user()?->isAdmin(), $this->email),
            'role' => $this->role->value,
            'xp' => $this->xp,
            'current_rank' => $this->current_rank,
            'rank_change' => $this->rankChange(),
            'initials' => $this->initials(),
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
        ];
    }
}
