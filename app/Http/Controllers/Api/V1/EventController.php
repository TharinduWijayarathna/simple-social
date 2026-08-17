<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RsvpToEvent;
use App\Enums\EventApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEventRequest;
use App\Http\Resources\Api\V1\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::query()
            ->published()
            ->upcoming()
            ->with(['organizer:id,name,role,xp,current_rank,previous_rank', 'talent:id,name,slug'])
            ->paginate(20);

        return EventResource::collection($events);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = $request->user()->organizedEvents()->create($request->validated());

        return EventResource::make($event->load(['organizer', 'talent']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Event $event): EventResource
    {
        $this->authorize('view', $event);

        return EventResource::make($event->load(['organizer:id,name,role,xp,current_rank,previous_rank', 'talent:id,name,slug']));
    }

    public function apply(Request $request, Event $event): JsonResponse
    {
        $this->authorize('apply', $event);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $application = $event->applications()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'status' => EventApplicationStatus::Pending,
                'message' => $validated['message'] ?? null,
            ],
        );

        return response()->json([
            'id' => $application->id,
            'status' => $application->status->value,
        ], 201);
    }

    public function rsvp(Request $request, Event $event, RsvpToEvent $rsvpToEvent): JsonResponse
    {
        $this->authorize('rsvp', $event);

        $validated = $request->validate([
            'attending' => ['sometimes', 'boolean'],
        ]);

        $application = $rsvpToEvent->handle(
            $request->user(),
            $event,
            $validated['attending'] ?? true,
        );

        return response()->json([
            'status' => $application->status->value,
        ]);
    }
}
