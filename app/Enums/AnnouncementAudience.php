<?php

namespace App\Enums;

enum AnnouncementAudience: string
{
    case Everyone = 'everyone';
    case Batch = 'batch';
    case Faculty = 'faculty';
    case Department = 'department';
    case Program = 'program';

    public function label(): string
    {
        return match ($this) {
            self::Everyone => 'All students',
            self::Batch => 'A specific batch',
            self::Faculty => 'A specific faculty',
            self::Department => 'A specific department',
            self::Program => 'A specific programme',
        };
    }
}
