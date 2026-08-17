<?php

namespace App\Actions;

use App\Enums\CollaborationRequestStatus;
use App\Enums\CollaborationStatus;
use App\Enums\XpEventType;
use App\Models\CollaborationMember;
use App\Models\CollaborationRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class RespondToCollaborationRequest
{
    public function __construct(private AwardXp $awardXp) {}

    public function handle(User $actor, CollaborationRequest $request, bool $accept): CollaborationRequest
    {
        $request->loadMissing('collaboration');

        if ($request->collaboration->owner_id !== $actor->id && ! $actor->isAdmin()) {
            throw new AuthorizationException('Only the collaboration owner can respond to requests.');
        }

        if ($request->status !== CollaborationRequestStatus::Pending) {
            return $request;
        }

        return DB::transaction(function () use ($request, $accept): CollaborationRequest {
            $request->status = $accept
                ? CollaborationRequestStatus::Accepted
                : CollaborationRequestStatus::Declined;
            $request->save();

            if (! $accept) {
                return $request;
            }

            CollaborationMember::query()->firstOrCreate(
                [
                    'collaboration_id' => $request->collaboration_id,
                    'user_id' => $request->user_id,
                ],
                [
                    'member_role' => 'member',
                ],
            );

            $request->collaboration->update([
                'status' => CollaborationStatus::InProgress,
            ]);

            $this->awardXp->handle($request->user, XpEventType::CollaborationAccepted, $request->collaboration);

            return $request;
        });
    }
}
