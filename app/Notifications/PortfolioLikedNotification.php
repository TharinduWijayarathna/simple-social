<?php

namespace App\Notifications;

use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortfolioLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $actor,
        public PortfolioItem $portfolioItem,
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
        return (new MailMessage)
            ->subject('Your work was liked')
            ->line($this->actor->name.' liked "'.$this->portfolioItem->title.'".')
            ->action('View portfolio', url('/portfolio/'.$this->portfolioItem->id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->actor->name.' liked your work',
            'type' => 'like',
            'action' => 'like',
            'actor_id' => $this->actor->id,
            'subject_id' => $this->portfolioItem->id,
            'subject_type' => $this->portfolioItem->getMorphClass(),
        ];
    }
}
