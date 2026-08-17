<?php

namespace Database\Factories;

use App\Enums\CollaborationStatus;
use App\Models\Collaboration;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collaboration>
 */
class CollaborationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'talent_id' => Talent::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => CollaborationStatus::Open,
            'credit_notes' => null,
        ];
    }
}
