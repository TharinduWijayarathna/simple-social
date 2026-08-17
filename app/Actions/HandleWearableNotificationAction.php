<?php

namespace App\Actions;

use App\Enums\WearableNotificationAction;
use App\Models\CollaborationRequest;
use App\Models\PortfolioItem;
use App\Models\User;
use App\Notifications\CollaborationRequestedNotification;
use App\Notifications\PortfolioLikedNotification;
use Illuminate\Notifications\DatabaseNotification;
use InvalidArgumentException;

class HandleWearableNotificationAction
{
    public function __construct(
        private ToggleLike $toggleLike,
        private RespondToCollaborationRequest $respondToCollaborationRequest,
    ) {}

    public function handle(User $user, DatabaseNotification $notification, WearableNotificationAction $action): DatabaseNotification
    {
        match ($action) {
            WearableNotificationAction::Dismiss => $notification->markAsRead(),
            WearableNotificationAction::Like => $this->likeFromNotification($user, $notification),
            WearableNotificationAction::Accept => $this->respondToRequest($user, $notification, true),
            WearableNotificationAction::Decline => $this->respondToRequest($user, $notification, false),
            WearableNotificationAction::Rsvp => throw new InvalidArgumentException('RSVP must be performed against an event.'),
        };

        $notification->markAsRead();

        return $notification;
    }

    private function likeFromNotification(User $user, DatabaseNotification $notification): void
    {
        if ($notification->type !== PortfolioLikedNotification::class) {
            throw new InvalidArgumentException('This notification cannot be liked.');
        }

        $portfolioItemId = $notification->data['subject_id'] ?? null;

        if ($portfolioItemId === null) {
            throw new InvalidArgumentException('Notification is missing a portfolio item.');
        }

        $portfolioItem = PortfolioItem::query()->findOrFail($portfolioItemId);

        $this->toggleLike->handle($user, $portfolioItem);
    }

    private function respondToRequest(User $user, DatabaseNotification $notification, bool $accept): void
    {
        if ($notification->type !== CollaborationRequestedNotification::class) {
            throw new InvalidArgumentException('This notification is not a collaboration request.');
        }

        $requestId = $notification->data['subject_id'] ?? null;

        if ($requestId === null) {
            throw new InvalidArgumentException('Notification is missing a collaboration request.');
        }

        $collaborationRequest = CollaborationRequest::query()->findOrFail($requestId);

        $this->respondToCollaborationRequest->handle($user, $collaborationRequest, $accept);
    }
}
