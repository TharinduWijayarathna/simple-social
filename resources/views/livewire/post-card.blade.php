<article class="feed-card">
    <div class="flex items-center justify-between gap-3 px-4 py-3">
        <a href="{{ route('students.show', $item->user) }}" class="flex items-center gap-3" wire:navigate>
            <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold">{{ $item->user->initials() }}</span>
            <span>
                <span class="block text-sm font-semibold">{{ $item->user->name }}</span>
                <span class="block text-xs text-mist">{{ $item->talent?->name }} · {{ $item->published_at?->diffForHumans() }}</span>
            </span>
        </a>
        @if ($item->talent)
            <span class="rounded-full bg-wall px-3 py-1 text-[11px] uppercase tracking-wide text-mist">{{ $item->talent->name }}</span>
        @endif
    </div>

    <a href="{{ route('portfolio.show', $item) }}" wire:navigate>
        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="{{ $item->feedAspectClass() }} w-full object-cover">
    </a>

    <div class="px-4 pb-4 pt-1">
        <div class="grid grid-cols-3 border-t border-ink/10 text-sm font-medium">
            @auth
                <button type="button" wire:click="like" class="inline-flex items-center justify-center gap-2 rounded-lg py-2.5 {{ $likedByViewer ? 'text-ember' : 'text-mist hover:bg-wall hover:text-ink' }}">
                    <x-icon name="heart" :solid="$likedByViewer" />
                    {{ $likedByViewer ? 'Liked' : 'Like' }}
                    <span class="text-xs">{{ $item->likes_count }}</span>
                </button>
                <button type="button" @click="$refs.commentInput?.focus()" class="inline-flex items-center justify-center gap-2 rounded-lg py-2.5 text-mist hover:bg-wall hover:text-ink">
                    <x-icon name="chat" />
                    Comment
                    <span class="text-xs">{{ $item->comments_count }}</span>
                </button>
                <button type="button" wire:click="share" class="inline-flex items-center justify-center gap-2 rounded-lg py-2.5 text-mist hover:bg-wall hover:text-ink">
                    <x-icon name="share" />
                    Share
                    <span class="text-xs">{{ $item->shares_count }}</span>
                </button>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg py-2.5 text-mist hover:bg-wall hover:text-ink" wire:navigate>
                    <x-icon name="heart" />
                    Like
                    <span class="text-xs">{{ $item->likes_count }}</span>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg py-2.5 text-mist hover:bg-wall hover:text-ink" wire:navigate>
                    <x-icon name="chat" />
                    Comment
                    <span class="text-xs">{{ $item->comments_count }}</span>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg py-2.5 text-mist hover:bg-wall hover:text-ink" wire:navigate>
                    <x-icon name="share" />
                    Share
                    <span class="text-xs">{{ $item->shares_count }}</span>
                </a>
            @endauth
        </div>
        @if (session('shared_item_id') === $item->id)
            <p class="mt-2 text-xs text-ember">Link copied. Shared with campus.</p>
        @endif
        <h2 class="mt-3 font-display text-xl">{{ $item->title }}</h2>
        @if ($item->description)
            <p class="mt-1 text-sm text-mist">{{ $item->description }}</p>
        @endif

        <ul class="mt-4 flex flex-col gap-2">
            @foreach ($item->comments->take(2) as $comment)
                <li class="text-sm" wire:key="card-comment-{{ $comment->id }}">
                    <span class="font-semibold">{{ $comment->user->name }}</span>
                    <span class="text-mist"> {{ $comment->body }}</span>
                </li>
            @endforeach
        </ul>

        @auth
            <form wire:submit="comment" class="mt-3 flex gap-2">
                <input wire:model="body" x-ref="commentInput" type="text" class="field flex-1" placeholder="Write a comment…">
                <button type="submit" class="btn-ghost">Send</button>
            </form>
            @error('body')
                <p class="mt-1 text-xs text-ember">{{ $message }}</p>
            @enderror
        @endauth
    </div>
</article>
