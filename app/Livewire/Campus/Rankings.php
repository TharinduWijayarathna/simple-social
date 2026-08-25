<?php

namespace App\Livewire\Campus;

use App\Models\CampusRanking;
use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::campus-panel')]
#[Title('Talent Rankings')]
class Rankings extends Component
{
    public ?int $talent_id = null;

    public string $title = '';

    public bool $showForm = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);
    }

    public function updatedTalentId(?int $value): void
    {
        if ($value) {
            $talent = Talent::query()->find($value);
            $this->title = $talent?->name ?? '';
        }
    }

    public function openForm(): void
    {
        $this->showForm = true;
        $this->talent_id = null;
        $this->title = '';
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->reset(['talent_id', 'title']);
    }

    public function save(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $validated = $this->validate([
            'talent_id' => ['required', 'integer', 'exists:talents,id'],
            'title' => ['required', 'string', 'max:100'],
        ]);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        CampusRanking::query()->updateOrCreate(
            ['campus_id' => $campusId, 'talent_id' => $validated['talent_id']],
            ['title' => $validated['title'], 'is_active' => true]
        );

        $this->cancelForm();
    }

    public function toggle(int $rankingId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        $ranking = CampusRanking::query()
            ->where('campus_id', $campusId)
            ->findOrFail($rankingId);

        $ranking->update(['is_active' => ! $ranking->is_active]);
    }

    public function delete(int $rankingId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        CampusRanking::query()
            ->where('campus_id', $campusId)
            ->findOrFail($rankingId)
            ->delete();
    }

    public function render(): View
    {
        $campusId = auth()->user()->campus_id ?? auth()->id();

        $rankings = CampusRanking::query()
            ->where('campus_id', $campusId)
            ->with('talent:id,name,category')
            ->latest()
            ->get();

        $rankingsWithLeaders = $rankings->map(function (CampusRanking $ranking) use ($campusId) {
            $talentId = $ranking->talent_id;

            $leaders = User::query()
                ->where('campus_id', $campusId)
                ->where('role', 'student')
                ->where('status', 'approved')
                ->whereHas('profile', function ($query) use ($talentId) {
                    $query->where('primary_talent_id', $talentId);
                })
                ->addSelect([
                    'talent_likes_total' => DB::table('likes')
                        ->selectRaw('COALESCE(SUM(1), 0)')
                        ->join('portfolio_items', function ($join) {
                            $join->on('likes.likeable_id', '=', 'portfolio_items.id')
                                ->where('likes.likeable_type', '=', PortfolioItem::class)
                                ->whereNotNull('portfolio_items.published_at')
                                ->where('portfolio_items.published_at', '<=', now());
                        })
                        ->whereColumn('portfolio_items.user_id', 'users.id'),
                ])
                ->with(['profile' => fn ($q) => $q->select('id', 'user_id', 'avatar_path', 'headline', 'batch', 'program')])
                ->orderByDesc('talent_likes_total')
                ->get();

            return [
                'ranking' => $ranking,
                'leaders' => $leaders,
            ];
        });

        $talents = Talent::query()
            ->forCampus($campusId)
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'category']);

        return view('livewire.campus.rankings', [
            'rankingsWithLeaders' => $rankingsWithLeaders,
            'talents' => $talents,
        ]);
    }
}
