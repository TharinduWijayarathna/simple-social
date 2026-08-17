<?php

namespace App\Livewire\Students;

use App\Actions\FollowUser;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('People')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function follow(int $userId, FollowUser $followUser): void
    {
        abort_unless(auth()->check(), 403);

        $student = User::query()->students()->findOrFail($userId);

        abort_if(auth()->user()->is($student), 403);

        $followUser->handle(auth()->user(), $student);
    }

    public function render(): View
    {
        $students = User::query()
            ->students()
            ->when(auth()->check(), fn ($query) => $query->where('id', '!=', auth()->id()))
            ->when($this->search !== '', function ($query): void {
                $term = '%'.$this->search.'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhereHas('profile', function ($profile) use ($term): void {
                            $profile->where('headline', 'like', $term)
                                ->orWhere('location', 'like', $term);
                        });
                });
            })
            ->with([
                'profile.talents',
                'portfolioItems' => fn ($query) => $query->published()->with('talent:id,name,slug,theme')->latest('published_at'),
            ])
            ->withCount('followers')
            ->withExists([
                'followers as followed_by_viewer' => fn ($query) => $query->where('follower_id', auth()->id()),
                'statuses as has_active_status' => fn ($query) => $query->active(),
            ])
            ->latest()
            ->paginate(12);

        return view('livewire.students.index', [
            'students' => $students,
            'explore' => $this->search === ''
                ? PortfolioItem::query()
                    ->published()
                    ->with(['user:id,name', 'talent:id,name,slug,theme'])
                    ->latest('published_at')
                    ->limit(18)
                    ->get()
                : collect(),
        ]);
    }
}
