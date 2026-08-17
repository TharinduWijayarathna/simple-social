<?php

namespace App\Http\Controllers\Api\V1\Wearable;

use App\Actions\RsvpToEvent;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function upcoming(Request $request): JsonResponse
    {
        $events = Event::query()
            ->published()
            ->upcoming()
            ->limit(5)
            ->get(['id', 'title', 'starts_at', 'location']);

        return response()->json([
            'events' => $events->map(fn (Event $event): array => [
                'id' => $event->id,
                'title' => $event->title,
                'starts_at' => $event->starts_at->toIso8601String(),
                'location' => $event->location,
            ])->values(),
        ]);
    }

    public function rsvp(Request $request, Event $event, RsvpToEvent $rsvpToEvent): JsonResponse
    {
        $this->authorize('rsvp', $event);

        $application = $rsvpToEvent->handle($request->user(), $event, true);

        return response()->json([
            'status' => $application->status->value,
        ]);
    }
}
