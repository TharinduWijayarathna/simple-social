<?php

namespace App\Enums;

enum AnnouncementPriority: string
{
    case Standard = 'standard';
    case Important = 'important';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Important => 'Important',
            self::Urgent => 'Urgent',
        };
    }
}
