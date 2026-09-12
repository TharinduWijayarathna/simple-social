<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Banner extends Component
{
    public function dismiss(int $announcementId): void
    {
        $user = auth()->user();
        abort_unless($user->isStudent(), 403);

        $announcement = Announcement::query()
            ->active()
            ->visibleTo($user)
            ->findOrFail($announcementId);

        $this->authorize('view', $announcement);
        $announcement->recipients()->syncWithoutDetaching([
            $user->id => ['read_at' => now(), 'dismissed_at' => now()],
        ]);
    }

    public function render(): View
    {
        $user = auth()->user();
        $announcement = null;

        if ($user?->isStudent()) {
            $announcement = Announcement::query()
                ->active()
                ->visibleTo($user)
                ->whereDoesntHave('recipients', fn (Builder $query) => $query
                    ->where('users.id', $user->id)
                    ->whereNotNull('announcement_user.dismissed_at'))
                ->orderByDesc('is_pinned')
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 ELSE 3 END")
                ->latest('published_at')
                ->first();
        }

        return view('livewire.announcements.banner', ['announcement' => $announcement]);
    }
}
