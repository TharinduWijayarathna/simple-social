<?php

namespace App\Notifications;

use App\Models\CollaborationRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborationRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $actor,
        public CollaborationRequest $collaborationRequest,
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
            ->subject('New collaboration request')
            ->line($this->actor->name.' wants to join "'.$this->collaborationRequest->collaboration->title.'".')
            ->action('Review request', url('/collaborations/'.$this->collaborationRequest->collaboration_id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->actor->name.' requested to collaborate',
            'type' => 'collaboration_request',
            'action' => 'accept',
            'actor_id' => $this->actor->id,
            'subject_id' => $this->collaborationRequest->id,
            'subject_type' => $this->collaborationRequest->getMorphClass(),
        ];
    }
}
