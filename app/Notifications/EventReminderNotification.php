<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Event $event) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upcoming: '.$this->event->title)
            ->line($this->event->title.' starts '.$this->event->starts_at->diffForHumans().'.')
            ->action('View event', url('/events/'.$this->event->id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Upcoming: '.$this->event->title,
            'type' => 'event_reminder',
            'action' => 'rsvp',
            'actor_id' => null,
            'subject_id' => $this->event->id,
            'subject_type' => $this->event->getMorphClass(),
        ];
    }
}
