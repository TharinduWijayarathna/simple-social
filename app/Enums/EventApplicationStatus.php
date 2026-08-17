<?php

namespace App\Enums;

enum EventApplicationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case RsvpYes = 'rsvp_yes';
    case RsvpNo = 'rsvp_no';
}
