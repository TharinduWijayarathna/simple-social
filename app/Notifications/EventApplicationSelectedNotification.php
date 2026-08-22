<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventApplicationSelectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event,
        public ?string $talentName = null
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $roleText = $this->talentName ? " as {$this->talentName}" : '';

        return (new MailMessage)
            ->subject("🎉 You've been chosen for: {$this->event->title}")
            ->greeting("Congratulations, {$notifiable->name}!")
            ->line("You have been chosen by campus organizers for {$this->event->title}{$roleText}.")
            ->line("Event Date: {$this->event->starts_at->format('l, F j, Y \a\t g:ia')}")
            ->line("Location: {$this->event->location}")
            ->lineIf($this->event->contact_email, "Contact Email: {$this->event->contact_email}")
            ->lineIf($this->event->contact_phone, "Contact Phone: {$this->event->contact_phone}")
            ->lineIf($this->event->contact_instructions, "Instructions: {$this->event->contact_instructions}")
            ->action('View Event & Contact Campus', url("/events/{$this->event->id}"));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "🎉 You've been chosen for: {$this->event->title}",
            'type' => 'event_application_selected',
            'action' => 'view_event',
            'actor_id' => $this->event->organizer_id,
            'subject_id' => $this->event->id,
            'subject_type' => $this->event->getMorphClass(),
            'message' => "You were selected by campus for {$this->event->title}!",
            'contact_email' => $this->event->contact_email,
            'contact_phone' => $this->event->contact_phone,
            'contact_instructions' => $this->event->contact_instructions,
        ];
    }
}
