<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Talent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Talent
 */
class TalentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_favorite' => $this->whenPivotLoaded('profile_talent', fn (): bool => (bool) $this->pivot->is_favorite),
        ];
    }
}
