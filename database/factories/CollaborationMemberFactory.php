<?php

namespace Database\Factories;

use App\Models\Collaboration;
use App\Models\CollaborationMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollaborationMember>
 */
class CollaborationMemberFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_id' => Collaboration::factory(),
            'user_id' => User::factory(),
            'member_role' => 'member',
            'credit' => fake()->jobTitle(),
        ];
    }
}
