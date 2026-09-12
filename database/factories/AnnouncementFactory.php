<?php

namespace Database\Factories;

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campus_id' => User::factory()->campus(),
            'title' => fake()->sentence(5),
            'body' => fake()->paragraphs(2, true),
            'priority' => fake()->randomElement(AnnouncementPriority::cases()),
            'audience' => AnnouncementAudience::Everyone,
            'audience_value' => null,
            'starts_at' => null,
            'expires_at' => now()->addWeek(),
            'published_at' => now(),
            'is_pinned' => false,
            'link_url' => null,
            'link_label' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['published_at' => null]);
    }

    public function pinned(): static
    {
        return $this->state(fn (): array => ['is_pinned' => true]);
    }
}
