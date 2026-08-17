<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),
            'reportable_id' => PortfolioItem::factory(),
            'reportable_type' => (new PortfolioItem)->getMorphClass(),
            'reason' => 'inappropriate',
            'details' => fake()->sentence(),
            'status' => ReportStatus::Pending,
            'moderator_notes' => null,
        ];
    }
}
