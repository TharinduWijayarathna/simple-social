<?php

namespace App\Enums;

enum CollaborationStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
