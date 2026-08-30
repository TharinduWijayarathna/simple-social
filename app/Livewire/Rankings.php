<?php

namespace App\Livewire;

use App\Models\CampusRanking;
use App\Models\User;
use App\Support\CampusRankingLeaders;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Rankings')]
class Rankings extends Component
{
    /** Re-render triggered by PostCard after a like is toggled. */
    #[On('like-toggled')]
    public function refresh(): void
    {
        // render() is automatically called after this method returns
    }

    public function render(): View
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $campusId = (int) $currentUser->campus_id;

        $rankingsWithLeaders = CampusRanking::query()
            ->where('campus_id', $campusId)
            ->where('is_active', true)
            ->with('talent:id,name,category')
            ->orderBy('title')
            ->get()
            ->map(fn (CampusRanking $ranking): array => [
                'ranking' => $ranking,
                'leaders' => CampusRankingLeaders::for($ranking, $campusId, 10),
            ]);

        return view('livewire.rankings', [
            'rankingsWithLeaders' => $rankingsWithLeaders,
        ]);
    }
}
