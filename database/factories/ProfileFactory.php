<?php

namespace Database\Factories;

use App\Enums\ExperienceLevel;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'headline' => fake()->sentence(6),
            'bio' => fake()->paragraph(),
            'faculty' => fake()->randomElement(['Arts', 'Engineering', 'Business', 'Science', 'Humanities']),
            'department' => fake()->word(),
            'experience_level' => fake()->randomElement(ExperienceLevel::cases()),
            'avatar_path' => null,
        ];
    }
}
