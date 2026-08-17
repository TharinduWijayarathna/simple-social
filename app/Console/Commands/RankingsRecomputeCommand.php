<?php

namespace App\Console\Commands;

use App\Actions\RecomputeRankings;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rankings:recompute')]
#[Description('Recompute student leaderboard ranks from XP totals')]
class RankingsRecomputeCommand extends Command
{
    public function handle(RecomputeRankings $recomputeRankings): int
    {
        $count = $recomputeRankings->handle();

        $this->info("Updated rankings for {$count} students.");

        return self::SUCCESS;
    }
}
