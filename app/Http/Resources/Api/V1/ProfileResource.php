<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Profile
 */
class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'faculty' => $this->faculty,
            'department' => $this->department,
            'experience_level' => $this->experience_level->value,
            'avatar_url' => $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null,
            'talents' => TalentResource::collection($this->whenLoaded('talents')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
