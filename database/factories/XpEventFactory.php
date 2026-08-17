<?php

namespace Database\Factories;

use App\Enums\XpEventType;
use App\Models\User;
use App\Models\XpEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<XpEvent>
 */
class XpEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => XpEventType::PortfolioPublished,
            'points' => 25,
            'source_id' => null,
            'source_type' => null,
        ];
    }
}
