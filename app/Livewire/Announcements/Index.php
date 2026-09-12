<?php

namespace App\Livewire\Announcements;

use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('Campus Announcements')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $filter = 'all';

    public function mount(): void
    {
        abort_unless(auth()->user()->isStudent(), 403);

        if (! in_array($this->filter, ['all', 'unread', 'important', 'dismissed'], true)) {
            $this->filter = 'all';
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function markAsRead(int $announcementId): void
    {
        $announcement = $this->visibleAnnouncement($announcementId);
        $this->saveEngagement($announcement, ['read_at' => now()]);
    }

    public function dismiss(int $announcementId): void
    {
        $announcement = $this->visibleAnnouncement($announcementId);
        $this->saveEngagement($announcement, ['read_at' => now(), 'dismissed_at' => now()]);
    }

    public function restore(int $announcementId): void
    {
        $announcement = $this->visibleAnnouncement($announcementId);
        $this->saveEngagement($announcement, ['dismissed_at' => null]);
    }

    public function render(): View
    {
        $user = $this->student();
        $search = trim($this->search);

        $announcements = Announcement::query()
            ->active()
            ->visibleTo($user)
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->where('title', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%")))
            ->when($this->filter === 'unread', fn (Builder $query) => $query->whereDoesntHave('recipients', fn (Builder $query) => $query
                ->where('users.id', $user->id)
                ->whereNotNull('announcement_user.read_at')))
            ->when($this->filter === 'important', fn (Builder $query) => $query
                ->whereIn('priority', [AnnouncementPriority::Important, AnnouncementPriority::Urgent])
                ->whereDoesntHave('recipients', fn (Builder $query) => $query
                    ->where('users.id', $user->id)
                    ->whereNotNull('announcement_user.dismissed_at')))
            ->when($this->filter === 'dismissed', fn (Builder $query) => $query->whereHas('recipients', fn (Builder $query) => $query
                ->where('users.id', $user->id)
                ->whereNotNull('announcement_user.dismissed_at')))
            ->when(in_array($this->filter, ['all', 'unread'], true), fn (Builder $query) => $query->whereDoesntHave('recipients', fn (Builder $query) => $query
                ->where('users.id', $user->id)
                ->whereNotNull('announcement_user.dismissed_at')))
            ->with(['campus:id,name,campus_name', 'recipients' => fn ($query) => $query->where('users.id', $user->id)])
            ->orderByDesc('is_pinned')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 ELSE 3 END")
            ->latest('published_at')
            ->paginate(10);

        $unreadCount = Announcement::query()
            ->active()
            ->visibleTo($user)
            ->whereDoesntHave('recipients', fn (Builder $query) => $query
                ->where('users.id', $user->id)
                ->whereNotNull('announcement_user.read_at'))
            ->count();

        return view('livewire.announcements.index', [
            'announcements' => $announcements,
            'unreadCount' => $unreadCount,
        ]);
    }

    /** @param array{read_at?: mixed, dismissed_at?: mixed} $values */
    private function saveEngagement(Announcement $announcement, array $values): void
    {
        $user = $this->student();

        if ($announcement->recipients()->whereKey($user)->exists()) {
            $announcement->recipients()->updateExistingPivot($user->id, $values);

            return;
        }

        $announcement->recipients()->attach($user->id, $values);
    }

    private function visibleAnnouncement(int $announcementId): Announcement
    {
        $announcement = Announcement::query()
            ->active()
            ->visibleTo($this->student())
            ->findOrFail($announcementId);

        $this->authorize('view', $announcement);

        return $announcement;
    }

    private function student(): User
    {
        $user = auth()->user();
        abort_unless($user->isStudent(), 403);
        $user->loadMissing(['profile', 'campus']);

        return $user;
    }
}
