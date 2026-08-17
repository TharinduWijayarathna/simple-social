<?php

namespace App\Enums;

enum CollaborationRequestStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
}
