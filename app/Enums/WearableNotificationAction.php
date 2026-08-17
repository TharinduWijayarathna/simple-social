<?php

namespace App\Enums;

enum WearableNotificationAction: string
{
    case Accept = 'accept';
    case Decline = 'decline';
    case Like = 'like';
    case Dismiss = 'dismiss';
    case Rsvp = 'rsvp';
}
