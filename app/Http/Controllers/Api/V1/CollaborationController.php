<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RespondToCollaborationRequest;
use App\Enums\CollaborationRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCollaborationRequest;
use App\Http\Resources\Api\V1\CollaborationResource;
use App\Models\Collaboration;
use App\Models\CollaborationRequest;
use App\Notifications\CollaborationRequestedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CollaborationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $collaborations = Collaboration::query()
            ->with(['owner:id,name,role,xp,current_rank,previous_rank', 'talent:id,name,slug'])
            ->withCount('members')
            ->latest()
            ->paginate(20);

        return CollaborationResource::collection($collaborations);
    }

    public function store(StoreCollaborationRequest $request): JsonResponse
    {
        $collaboration = $request->user()->ownedCollaborations()->create(
            $request->safe()->only(['title', 'description', 'talent_id', 'credit_notes']),
        );

        $collaboration->members()->create([
            'user_id' => $request->user()->id,
            'member_role' => 'owner',
        ]);

        return CollaborationResource::make($collaboration->load(['owner', 'talent']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Collaboration $collaboration): CollaborationResource
    {
        $this->authorize('view', $collaboration);

        return CollaborationResource::make(
            $collaboration->load(['owner:id,name,role,xp,current_rank,previous_rank', 'talent:id,name,slug'])->loadCount('members'),
        );
    }

    public function requestToJoin(Request $request, Collaboration $collaboration): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $collaborationRequest = $collaboration->requests()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'message' => $validated['message'] ?? null,
                'status' => CollaborationRequestStatus::Pending,
            ],
        );

        $collaboration->owner->notify(
            (new CollaborationRequestedNotification($request->user(), $collaborationRequest->load('collaboration')))->afterCommit(),
        );

        return response()->json([
            'id' => $collaborationRequest->id,
            'status' => $collaborationRequest->status->value,
        ], 201);
    }

    public function respond(Request $request, CollaborationRequest $collaborationRequest, RespondToCollaborationRequest $respond): JsonResponse
    {
        $collaborationRequest->loadMissing('collaboration');

        $validated = $request->validate([
            'accept' => ['required', 'boolean'],
        ]);

        $this->authorize('respondToRequests', $collaborationRequest->collaboration);

        $updated = $respond->handle($request->user(), $collaborationRequest, $validated['accept']);

        return response()->json([
            'status' => $updated->status->value,
        ]);
    }
}
