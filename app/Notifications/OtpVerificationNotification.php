<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OtpVerificationNotification extends Notification
{
    public function __construct(public string $code) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your VibeCraft verification code')
            ->greeting('Verify your email')
            ->line('Use the code below to verify your email address and complete your registration.')
            ->line(new HtmlString('<div style="font-size:28px;font-weight:700;letter-spacing:6px;text-align:center;margin:24px 0;">'.$this->code.'</div>'))
            ->line('This code expires in 10 minutes.')
            ->line('If you did not request this, you can safely ignore this email.');
    }
}
