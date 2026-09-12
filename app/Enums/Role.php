<?php

namespace App\Enums;

enum Role: string
{
    case Student = 'student';
    case Campus = 'campus';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Campus => 'Campus',
            self::SuperAdmin => 'Super admin',
        };
    }
}
