<?php

namespace App\Enums;

enum Role: string
{
    case Student = 'student';
    case CampusAdmin = 'campus_admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::CampusAdmin => 'Campus admin',
            self::SuperAdmin => 'Super admin',
        };
    }
}
