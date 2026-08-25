<?php

namespace App\Livewire\Campus;

use App\Models\CampusRanking;
use App\Models\Talent;
use Illuminate\Contracts\View\View;
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

        $campusId = auth()->id();

        CampusRanking::query()->updateOrCreate(
            ['campus_id' => $campusId, 'talent_id' => $validated['talent_id']],
            ['title' => $validated['title'], 'is_active' => true]
        );

        $this->cancelForm();
    }

    public function toggle(int $rankingId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $ranking = CampusRanking::query()
            ->where('campus_id', auth()->id())
            ->findOrFail($rankingId);

        $ranking->update(['is_active' => ! $ranking->is_active]);
    }

    public function delete(int $rankingId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        CampusRanking::query()
            ->where('campus_id', auth()->id())
            ->findOrFail($rankingId)
            ->delete();
    }

    public function render(): View
    {
        $campusId = auth()->id();

        $rankings = CampusRanking::query()
            ->where('campus_id', $campusId)
            ->with('talent:id,name,category')
            ->latest()
            ->get();

        $talents = Talent::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'category']);

        return view('livewire.campus.rankings', [
            'rankings' => $rankings,
            'talents' => $talents,
        ]);
    }
}
