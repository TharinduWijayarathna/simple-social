<?php

namespace App\Http\Resources\Api\V1;

use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PortfolioItem
 */
class PortfolioItemResource extends JsonResource
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
            'media_type' => $this->media_type->value,
            'file_url' => $this->fileUrl(),
            'thumbnail_url' => $this->thumbnailUrl(),
            'published_at' => $this->published_at,
            'likes_count' => $this->whenCounted('likes'),
            'comments_count' => $this->whenCounted('comments'),
            'user' => UserResource::make($this->whenLoaded('user')),
            'talent' => TalentResource::make($this->whenLoaded('talent')),
        ];
    }
}
