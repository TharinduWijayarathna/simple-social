<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Collaboration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Collaboration
 */
class CollaborationResource extends JsonResource
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
            'status' => $this->status->value,
            'credit_notes' => $this->credit_notes,
            'owner' => UserResource::make($this->whenLoaded('owner')),
            'talent' => TalentResource::make($this->whenLoaded('talent')),
            'members_count' => $this->whenCounted('members'),
        ];
    }
}
