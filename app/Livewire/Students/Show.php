<?php

namespace App\Livewire\Students;

use App\Actions\FollowUser;
use App\Enums\TalentTheme;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Student')]
class Show extends Component
{
    public User $student;

    public function mount(User $user): void
    {
        $this->student = $user;
    }

    public function follow(FollowUser $followUser): void
    {
        $followUser->handle(auth()->user(), $this->student);
    }

    public function render(): View
    {
        $this->student->load([
            'profile.talents',
            'portfolioItems' => fn ($query) => $query->published()->with('talent:id,name,slug,theme')->latest('published_at')->limit(24),
        ]);

        $theme = $this->student->profile?->primaryTalent()?->theme ?? TalentTheme::Gallery;

        return view('livewire.students.show', [
            'theme' => $theme,
            'isFollowing' => auth()->user()->following()->where('following_id', $this->student->id)->exists(),
        ]);
    }
}
