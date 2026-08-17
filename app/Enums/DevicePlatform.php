<?php

namespace App\Enums;

enum DevicePlatform: string
{
    case WearOs = 'wearos';
    case WatchOs = 'watchos';
    case Android = 'android';
    case Ios = 'ios';
    case Web = 'web';
}
