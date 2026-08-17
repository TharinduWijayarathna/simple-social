<?php

namespace App\Enums;

enum PortfolioMediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
}
