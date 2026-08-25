<?php

namespace App\Livewire\Portfolio;

use App\Models\PortfolioItem;
use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('Portfolio')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public ?int $talent_id = null;

    public function render(): View
    {
        $items = PortfolioItem::query()
            ->published()
            ->with(['user:id,name', 'talent:id,name,slug,theme'])
            ->withCount('likes')
            ->when($this->talent_id, fn ($query) => $query->where('talent_id', $this->talent_id))
            ->latest('published_at')
            ->paginate(12);

        $campusId = auth()->check() ? auth()->user()->campus_id : null;

        return view('livewire.portfolio.index', [
            'items' => $items,
            'talents' => Talent::query()->forCampus($campusId)->orderBy('name')->get(),
        ]);
    }
}
