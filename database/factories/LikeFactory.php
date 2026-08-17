<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Like>
 */
class LikeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'likeable_id' => PortfolioItem::factory(),
            'likeable_type' => (new PortfolioItem)->getMorphClass(),
        ];
    }
}
