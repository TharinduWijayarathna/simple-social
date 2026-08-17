<?php

namespace Database\Factories;

use App\Enums\EventApplicationStatus;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventApplication>
 */
class EventApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'status' => EventApplicationStatus::Pending,
            'message' => fake()->sentence(),
        ];
    }

    public function rsvpYes(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => EventApplicationStatus::RsvpYes,
        ]);
    }
}
