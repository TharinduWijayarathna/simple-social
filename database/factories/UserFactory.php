<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => Role::Student,
            'status' => UserStatus::Approved,
            'xp' => 0,
            'current_rank' => null,
            'previous_rank' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::Student,
        ]);
    }

    public function campusAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::CampusAdmin,
        ]);
    }

    public function organizer(): static
    {
        return $this->campusAdmin();
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::SuperAdmin,
        ]);
    }

    public function admin(): static
    {
        return $this->superAdmin();
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => UserStatus::Pending,
        ]);
    }

    public function withXp(int $xp): static
    {
        return $this->state(fn (array $attributes): array => [
            'xp' => $xp,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->profile()->firstOrCreate([]);
        });
    }
}
