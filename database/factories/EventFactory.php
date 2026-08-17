<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = now()->addDays(fake()->numberBetween(2, 30));

        return [
            'organizer_id' => User::factory()->organizer(),
            'talent_id' => Talent::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHours(3),
            'application_deadline' => $startsAt->subDay(),
            'capacity' => 50,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }
}
