<?php

namespace Database\Factories;

use App\Enums\PortfolioMediaType;
use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioItem>
 */
class PortfolioItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'talent_id' => Talent::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'media_type' => PortfolioMediaType::Image,
            'file_path' => 'portfolio/'.fake()->uuid().'.jpg',
            'thumbnail_path' => 'portfolio/thumbs/'.fake()->uuid().'.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10_000, 2_000_000),
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'published_at' => null,
        ]);
    }
}
