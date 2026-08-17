<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Event
 */
class EventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'application_deadline' => $this->application_deadline,
            'capacity' => $this->capacity,
            'is_published' => $this->is_published,
            'organizer' => UserResource::make($this->whenLoaded('organizer')),
            'talent' => TalentResource::make($this->whenLoaded('talent')),
        ];
    }
}
