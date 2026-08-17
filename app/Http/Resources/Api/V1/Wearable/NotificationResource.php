<?php

namespace App\Http\Resources\Api\V1\Wearable;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @mixin DatabaseNotification
 */
class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->data;

        return [
            'id' => $this->id,
            'title' => $data['title'] ?? 'Notification',
            'type' => $data['type'] ?? class_basename($this->type),
            'action' => $data['action'] ?? null,
            'timestamp' => $this->created_at?->toIso8601String(),
            'read' => $this->read_at !== null,
        ];
    }
}
