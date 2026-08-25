<?php

namespace App\Livewire;

use App\Actions\AwardXp;
use App\Actions\SharePortfolioItem;
use App\Actions\ToggleLike;
use App\Enums\XpEventType;
use App\Models\PortfolioItem;
use App\Notifications\PortfolioCommentedNotification;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostCard extends Component
{
    public PortfolioItem $item;

    public string $body = '';

    public function like(ToggleLike $toggleLike): void
    {
        if (! $this->requireCampusAccount()) {
            return;
        }

        $toggleLike->handle(auth()->user(), $this->item);

        // Notify the rankings page (if open) to refresh live
        $this->dispatch('like-toggled')->to('rankings');
    }

    public function comment(AwardXp $awardXp): void
    {
        if (! $this->requireCampusAccount()) {
            return;
        }

        $validated = $this->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = $this->item->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        if ($this->item->user_id !== auth()->id()) {
            $awardXp->handle($this->item->user, XpEventType::CommentReceived, $comment);
            $this->item->user->notify((new PortfolioCommentedNotification(auth()->user(), $this->item, $comment))->afterCommit());
        }

        $this->body = '';
    }

    public function share(SharePortfolioItem $sharePortfolioItem): void
    {
        if (! $this->requireCampusAccount()) {
            return;
        }

        $url = $sharePortfolioItem->handle(auth()->user(), $this->item);

        $this->dispatch('share-copied', url: $url);
        session()->flash('shared_item_id', $this->item->id);
    }

    public function render(): View
    {
        $this->item->load([
            'user:id,name',
            'talent:id,name,slug,theme',
            'comments' => fn ($query) => $query->latest()->with('user:id,name'),
        ])->loadCount(['likes', 'comments', 'shares']);

        if (auth()->check()) {
            $this->item->loadExists([
                'likes as liked_by_viewer' => fn ($query) => $query->where('user_id', auth()->id()),
            ]);
        }

        return view('livewire.post-card', [
            'likedByViewer' => (bool) $this->item->liked_by_viewer,
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
