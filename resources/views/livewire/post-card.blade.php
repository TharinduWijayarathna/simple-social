<article class="feed-card">
    <div class="flex items-center justify-between gap-3 px-4 py-3">
        <a href="{{ route('students.show', $item->user) }}" class="flex items-center gap-3" wire:navigate>
            <span class="flex size-10 items-center justify-center overflow-hidden rounded-full bg-studio text-xs font-semibold text-gold">
                @if ($item->user->avatarUrl())
                    <img src="{{ $item->user->avatarUrl() }}" alt="{{ $item->user->name }}" class="size-full object-cover rounded-full">
                @else
                    {{ $item->user->initials() }}
                @endif
            </span>
            <span>
                <span class="block text-sm font-semibold">{{ $item->user->name }}</span>
                <span class="block text-xs text-mist">{{ $item->talent?->name }} · {{ $item->published_at?->diffForHumans() }}</span>
            </span>
        </a>
        @if ($item->talent)
            <span class="rounded-full bg-wall px-3 py-1 text-[11px] uppercase tracking-wide text-mist">{{ $item->talent->name }}</span>
        @endif
    </div>

    @if ($item->isVideo())
        <div class="relative overflow-hidden bg-black/90 w-full h-auto max-h-[750px] flex items-center justify-center rounded-2xl group shadow-sm"
             x-data="{ playing: false, muted: true, togglePlay() { if (this.playing) { $refs.video.pause(); this.playing = false; } else { $refs.video.play(); this.playing = true; } }, toggleMute() { this.muted = !this.muted; $refs.video.muted = this.muted; } }">
            
            <video x-ref="video"
                   src="{{ $item->fileUrl() }}"
                   poster="{{ $item->displayUrl() }}"
                   loop
                   playsinline
                   :muted="muted"
                   @click="togglePlay"
                   @play="playing = true"
                   @pause="playing = false"
                   class="w-full h-auto max-h-[750px] object-contain rounded-2xl cursor-pointer">
            </video>

            {{-- Reels Badge --}}
            <span class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-full bg-black/60 px-3 py-1 text-[11px] font-bold text-white backdrop-blur pointer-events-none">
                <svg class="size-3.5 text-rose-500 fill-current" viewBox="0 0 24 24"><path d="M17 10.5V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3.5l4 4v-11z"/></svg>
                REEL
            </span>

            {{-- Play/Pause Center Button Overlay --}}
            <button type="button" @click="togglePlay" class="absolute inset-0 z-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition duration-200">
                <span class="flex size-16 items-center justify-center rounded-full bg-black/60 text-white backdrop-blur shadow-lg transform transition group-hover:scale-105">
                    <template x-if="!playing">
                        <svg class="size-8 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </template>
                    <template x-if="playing">
                        <svg class="size-8" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </template>
                </span>
            </button>

            {{-- Mute / Unmute Toggle Button --}}
            <button type="button" @click.stop="toggleMute" class="absolute bottom-3 right-3 z-10 flex size-9 items-center justify-center rounded-full bg-black/60 text-white backdrop-blur hover:bg-black/80 transition" title="Toggle Sound">
                <template x-if="muted">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" /></svg>
                </template>
                <template x-if="!muted">
                    <svg class="size-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
                </template>
            </button>
        </div>
    @else
        <a href="{{ route('portfolio.show', $item) }}" class="block overflow-hidden bg-wall/30 rounded-2xl" wire:navigate>
            <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="w-full h-auto max-h-[750px] object-contain mx-auto rounded-2xl">
        </a>
    @endif

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
                @continue($comment->user === null)
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
