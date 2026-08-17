<?php

namespace App\Actions;

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\User;

class IssueDeviceToken
{
    /**
     * @return array{device: Device, plain_text_token: string}
     */
    public function handle(User $user, string $name, DeviceType $type, DevicePlatform $platform, ?string $pushToken = null): array
    {
        return Device::issue($user, $name, $type, $platform, $pushToken);
    }
}
