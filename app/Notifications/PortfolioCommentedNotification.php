<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortfolioCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $actor,
        public PortfolioItem $portfolioItem,
        public Comment $comment,
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
            ->subject('New comment on your work')
            ->line($this->actor->name.' commented on "'.$this->portfolioItem->title.'".')
            ->action('View on VibeCraft', url('/portfolio/'.$this->portfolioItem->id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->actor->name.' commented on your work',
            'type' => 'comment',
            'action' => null,
            'actor_id' => $this->actor->id,
            'subject_id' => $this->portfolioItem->id,
            'subject_type' => $this->portfolioItem->getMorphClass(),
        ];
    }
}
