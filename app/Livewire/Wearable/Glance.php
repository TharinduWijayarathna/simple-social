<?php

namespace App\Livewire\Wearable;

use App\Actions\HandleWearableNotificationAction;
use App\Actions\RsvpToEvent;
use App\Enums\EventApplicationStatus;
use App\Enums\WearableNotificationAction;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::wearable')]
#[Title('Watch glance')]
class Glance extends Component
{
    public function likeNotification(string $notificationId, HandleWearableNotificationAction $handle): void
    {
        $notification = auth()->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $handle->handle(auth()->user(), $notification, WearableNotificationAction::Like);
    }

    public function acceptNotification(string $notificationId, HandleWearableNotificationAction $handle): void
    {
        $notification = auth()->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $handle->handle(auth()->user(), $notification, WearableNotificationAction::Accept);
    }

    public function declineNotification(string $notificationId, HandleWearableNotificationAction $handle): void
    {
        $notification = auth()->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $handle->handle(auth()->user(), $notification, WearableNotificationAction::Decline);
    }

    public function dismissNotification(string $notificationId, HandleWearableNotificationAction $handle): void
    {
        $notification = auth()->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $handle->handle(auth()->user(), $notification, WearableNotificationAction::Dismiss);
    }

    public function rsvp(int $eventId, RsvpToEvent $rsvpToEvent): void
    {
        $event = Event::query()->findOrFail($eventId);
        $rsvpToEvent->handle(auth()->user(), $event, true);
    }

    public function render(): View
    {
        $user = auth()->user()->load(['profile.talents']);

        $nextEvent = Event::query()
            ->published()
            ->upcoming()
            ->whereHas(
                'applications',
                fn ($query) => $query->whereBelongsTo($user)->where('status', EventApplicationStatus::RsvpYes),
            )
            ->first(['id', 'title', 'starts_at']);

        return view('livewire.wearable.glance', [
            'user' => $user,
            'nextEvent' => $nextEvent,
            'topFive' => User::query()->students()->orderByDesc('xp')->limit(5)->get(['id', 'name', 'xp', 'current_rank']),
            'latestItem' => PortfolioItem::query()->published()->whereBelongsTo($user)->latest('published_at')->first(['id', 'title']),
            'notifications' => $user->unreadNotifications()->latest()->limit(5)->get(),
        ]);
    }
}
