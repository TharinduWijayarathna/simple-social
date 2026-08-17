<?php

namespace App\Livewire;

use App\Actions\AwardXp;
use App\Actions\SharePortfolioItem;
use App\Actions\ToggleLike;
use App\Enums\XpEventType;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\User;
use App\Notifications\PortfolioCommentedNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('Home')]
class Feed extends Component
{
    use WithPagination;

    /**
     * @var array<int, string>
     */
    public array $commentDrafts = [];

    public function like(int $itemId, ToggleLike $toggleLike): void
    {
        if (! $this->requireCampusAccount()) {
            return;
        }

        $item = PortfolioItem::query()->published()->findOrFail($itemId);

        $toggleLike->handle(auth()->user(), $item);
    }

    public function comment(int $itemId, AwardXp $awardXp): void
    {
        if (! $this->requireCampusAccount()) {
            return;
        }

        $item = PortfolioItem::query()->published()->findOrFail($itemId);

        $validated = $this->validate([
            'commentDrafts.'.$itemId => ['required', 'string', 'max:2000'],
        ], [], [
            'commentDrafts.'.$itemId => 'comment',
        ]);

        $comment = $item->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['commentDrafts'][$itemId],
        ]);

        if ($item->user_id !== auth()->id()) {
            $awardXp->handle($item->user, XpEventType::CommentReceived, $comment);
            $item->user->notify((new PortfolioCommentedNotification(auth()->user(), $item, $comment))->afterCommit());
        }

        $this->commentDrafts[$itemId] = '';
    }

    public function share(int $itemId, SharePortfolioItem $sharePortfolioItem): void
    {
        if (! $this->requireCampusAccount()) {
            return;
        }

        $item = PortfolioItem::query()->published()->findOrFail($itemId);
        $url = $sharePortfolioItem->handle(auth()->user(), $item);

        $this->dispatch('share-copied', url: $url);
        session()->flash('shared_item_id', $itemId);
    }

    public function render(): View
    {
        $posts = PortfolioItem::query()
            ->published()
            ->with([
                'user:id,name',
                'user.profile:id,user_id,headline,faculty',
                'talent:id,name,slug,theme',
                'comments' => fn ($query) => $query->latest()->with('user:id,name'),
            ])
            ->withCount(['likes', 'comments', 'shares'])
            ->when(
                auth()->check(),
                fn ($query) => $query->withExists([
                    'likes as liked_by_viewer' => fn ($likes) => $likes->where('user_id', auth()->id()),
                ]),
            )
            ->latest('published_at')
            ->paginate(12);

        return view('livewire.feed', [
            'posts' => $posts,
            'upcomingEvents' => Event::query()
                ->published()
                ->upcoming()
                ->with('organizer:id,name')
                ->limit(4)
                ->get(),
            'risingStudents' => User::query()
                ->students()
                ->with('profile:id,user_id,headline')
                ->orderByDesc('xp')
                ->limit(5)
                ->get(),
        ]);
    }

    private function requireCampusAccount(): bool
    {
        if (auth()->check()) {
            return true;
        }

        $this->redirect(route('login'), navigate: true);

        return false;
    }
}
