<?php

namespace App\Livewire\Students;

use App\Actions\FollowUser;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Profile')]
class Show extends Component
{
    public User $student;

    public string $tab = 'gallery';

    public function mount(User $user): void
    {
        $this->student = $user;
    }

    public function follow(FollowUser $followUser): void
    {
        abort_unless(auth()->check(), 403);
        abort_if(auth()->user()->is($this->student), 403);

        $followUser->handle(auth()->user(), $this->student);
    }

    public function showGallery(): void
    {
        $this->tab = 'gallery';
    }

    public function showFeed(): void
    {
        $this->tab = 'feed';
    }

    public function render(): View
    {
        $this->student->load(['profile.talents']);
        $this->student->loadCount(['followers', 'following']);

        $posts = $this->student->portfolioItems()
            ->published()
            ->with(['user:id,name', 'talent:id,name,slug,theme'])
            ->withCount(['likes', 'comments'])
            ->latest('published_at')
            ->get();

        return view('livewire.students.show', [
            'posts' => $posts,
            'highlights' => $this->student->statuses()->active()->latest()->get(),
            'isOwnProfile' => auth()->user()?->is($this->student) ?? false,
            'isFollowing' => auth()->check()
                && auth()->user()->following()->where('following_id', $this->student->id)->exists(),
        ]);
    }
}
