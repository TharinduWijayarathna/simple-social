<?php

namespace Database\Factories;

use App\Models\PortfolioItem;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rateable_id' => PortfolioItem::factory(),
            'rateable_type' => (new PortfolioItem)->getMorphClass(),
            'score' => fake()->numberBetween(1, 5),
        ];
    }
}
