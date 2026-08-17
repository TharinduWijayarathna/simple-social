<?php

namespace App\Livewire\Portfolio;

use App\Actions\AwardXp;
use App\Actions\SharePortfolioItem;
use App\Actions\ToggleLike;
use App\Enums\XpEventType;
use App\Models\PortfolioItem;
use App\Notifications\PortfolioCommentedNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Portfolio item')]
class Show extends Component
{
    public PortfolioItem $item;

    public string $body = '';

    public function mount(PortfolioItem $item): void
    {
        $this->authorize('view', $item);
        $this->item = $item;
    }

    public function like(ToggleLike $toggleLike): void
    {
        $toggleLike->handle(auth()->user(), $this->item);
    }

    public function share(SharePortfolioItem $sharePortfolioItem): void
    {
        $url = $sharePortfolioItem->handle(auth()->user(), $this->item);

        $this->dispatch('share-copied', url: $url);
        session()->flash('shared_item_id', $this->item->id);
    }

    public function comment(AwardXp $awardXp): void
    {
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

    public function render(): View
    {
        $this->item->load(['user:id,name', 'talent:id,name,slug,theme', 'comments.user:id,name'])
            ->loadCount(['likes', 'comments', 'shares']);

        return view('livewire.portfolio.show');
    }
}
