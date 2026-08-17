<?php

namespace App\Notifications;

use App\Models\Achievement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchievementUnlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Achievement $achievement) {}

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
            ->subject('Achievement unlocked: '.$this->achievement->name)
            ->line($this->achievement->description)
            ->action('View achievements', url('/leaderboard'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Badge unlocked: '.$this->achievement->name,
            'type' => 'achievement',
            'action' => 'dismiss',
            'haptic' => true,
            'actor_id' => null,
            'subject_id' => $this->achievement->id,
            'subject_type' => $this->achievement->getMorphClass(),
        ];
    }
}
