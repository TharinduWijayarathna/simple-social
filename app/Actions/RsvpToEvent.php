<?php

namespace App\Actions;

use App\Enums\EventApplicationStatus;
use App\Enums\XpEventType;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class RsvpToEvent
{
    public function __construct(private AwardXp $awardXp) {}

    public function handle(User $user, Event $event, bool $attending = true): EventApplication
    {
        if (! $event->is_published) {
            throw new AuthorizationException('This event is not open for RSVP.');
        }

        $status = $attending
            ? EventApplicationStatus::RsvpYes
            : EventApplicationStatus::RsvpNo;

        $application = EventApplication::query()->firstOrNew([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        $wasAttending = $application->exists && $application->status === EventApplicationStatus::RsvpYes;

        $application->status = $status;
        $application->save();

        if ($attending && ! $wasAttending) {
            $this->awardXp->handle($user, XpEventType::EventRsvp, $event);
        }

        return $application;
    }
}
