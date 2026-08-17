<?php

namespace App\Enums;

enum TalentTheme: string
{
    case Gallery = 'gallery';
    case Darkroom = 'darkroom';
    case Vinyl = 'vinyl';
    case Stage = 'stage';
    case Grid = 'grid';
    case Social = 'social';
    case Cinema = 'cinema';
    case Editorial = 'editorial';

    public function label(): string
    {
        return match ($this) {
            self::Gallery => 'Exhibition',
            self::Darkroom => 'Darkroom',
            self::Vinyl => 'Listening room',
            self::Stage => 'Stage',
            self::Grid => 'Studio grid',
            self::Social => 'Studio',
            self::Cinema => 'Screening',
            self::Editorial => 'Lookbook',
        };
    }

    public function feedAspectClass(): string
    {
        return match ($this) {
            self::Cinema => 'aspect-video',
            self::Vinyl, self::Social => 'aspect-square',
            self::Editorial => 'aspect-[3/4]',
            default => 'aspect-[4/5]',
        };
    }
}
