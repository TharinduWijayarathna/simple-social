<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserFollowedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $actor) {}

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
            ->subject('You have a new follower')
            ->line($this->actor->name.' started following you.')
            ->action('View profile', url('/students/'.$this->actor->id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->actor->name.' followed you',
            'type' => 'follow',
            'action' => null,
            'actor_id' => $this->actor->id,
            'subject_id' => $this->actor->id,
            'subject_type' => $this->actor->getMorphClass(),
        ];
    }
}
