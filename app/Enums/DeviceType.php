<?php

namespace App\Enums;

enum DeviceType: string
{
    case Wearable = 'wearable';
    case Mobile = 'mobile';
    case Web = 'web';
}
