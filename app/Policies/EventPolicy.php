<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Event $event): bool
    {
        if ($event->is_published) {
            return true;
        }

        return $user !== null && ($user->is($event->organizer) || $user->isAdmin());
    }

    public function create(User $user): bool
    {
        return $user->canOrganizeEvents();
    }

    public function update(User $user, Event $event): bool
    {
        return $user->is($event->organizer) || $user->isAdmin();
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function rsvp(User $user, Event $event): bool
    {
        return $event->is_published && ! $user->is($event->organizer);
    }

    public function apply(User $user, Event $event): bool
    {
        return $this->rsvp($user, $event);
    }
}
