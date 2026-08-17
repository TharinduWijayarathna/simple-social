<?php

namespace Database\Factories;

use App\Enums\CollaborationRequestStatus;
use App\Models\Collaboration;
use App\Models\CollaborationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollaborationRequest>
 */
class CollaborationRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_id' => Collaboration::factory(),
            'user_id' => User::factory(),
            'message' => fake()->sentence(),
            'status' => CollaborationRequestStatus::Pending,
        ];
    }
}
