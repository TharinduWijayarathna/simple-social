<?php

namespace Database\Factories;

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Pixel Watch',
            'type' => DeviceType::Wearable,
            'platform' => DevicePlatform::WearOs,
            'token_hash' => hash('sha256', Str::random(40)),
            'push_token' => Str::random(32),
            'last_seen_at' => now(),
            'revoked_at' => null,
        ];
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes): array => [
            'revoked_at' => now(),
        ]);
    }
}
