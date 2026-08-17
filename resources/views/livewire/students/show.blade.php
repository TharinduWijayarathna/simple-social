<div>
    <div class="bg-white">
        <div class="mx-auto max-w-5xl">
            <img src="https://picsum.photos/seed/vc-cover{{ $student->id }}/1600/420" alt="" class="h-48 w-full object-cover md:h-64">
            <div class="px-4 pb-4 md:px-8">
                <div class="-mt-12 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex items-end gap-4">
                        <span class="flex size-28 items-center justify-center rounded-full border-4 border-white bg-studio text-3xl font-semibold text-gold">{{ $student->initials() }}</span>
                        <div class="pb-1">
                            <h1 class="font-display text-3xl md:text-4xl">{{ $student->name }}</h1>
                            <p class="mt-1 text-mist">{{ $student->profile?->headline }}</p>
                            <p class="mt-2 text-sm text-mist">{{ $posts->count() }} posts · {{ $student->followers_count }} followers · {{ $student->following_count }} following</p>
                        </div>
                    </div>
                    <div class="flex gap-2 pb-1">
                        @if ($isOwnProfile)
                            <a href="{{ route('portfolio.create') }}" class="btn-primary" wire:navigate>Add post</a>
                            <a href="{{ route('profile.edit') }}" class="btn-ghost" wire:navigate>Edit profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn-ghost">Sign out</button>
                            </form>
                        @elseif (auth()->check())
                            <button type="button" wire:click="follow" class="{{ $isFollowing ? 'btn-ghost' : 'btn-primary' }}">
                                {{ $isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        @endif
                    </div>
                </div>
                @if ($student->profile?->talents?->isNotEmpty())
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($student->profile->talents as $talent)
                            <span class="rounded-full bg-wall px-3 py-1 text-xs font-medium" wire:key="badge-{{ $talent->id }}">{{ $talent->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-5xl gap-6 px-4 py-6 lg:grid-cols-[18rem_minmax(0,1fr)] md:px-8">
        <aside class="flex flex-col gap-4">
            <section class="rounded-[1.5rem] bg-white p-5">
                <h2 class="font-display text-xl">Intro</h2>
                @if ($student->profile?->bio)
                    <p class="mt-3 text-sm leading-6">{{ $student->profile->bio }}</p>
                @endif
                <ul class="mt-4 flex flex-col gap-2 text-sm">
                    @if ($student->profile?->faculty)
                        <li class="text-mist">Studies {{ $student->profile->faculty }}@if ($student->profile->department) · {{ $student->profile->department }}@endif</li>
                    @endif
                    @if ($student->profile?->location)
                        <li class="text-mist">Lives in {{ $student->profile->location }}</li>
                    @endif
                    @if ($student->profile?->birthday)
                        <li class="text-mist">Born {{ $student->profile->birthday->format('F j') }}</li>
                    @endif
                </ul>
            </section>
        </aside>

        <div>
            <div class="mb-4 flex gap-2 border-b border-ink/10">
                <button type="button" wire:click="showPosts" class="px-4 py-2 text-sm font-medium {{ $tab === 'posts' ? 'border-b-2 border-ember text-ember' : 'text-mist' }}">Posts</button>
                <button type="button" wire:click="showPhotos" class="px-4 py-2 text-sm font-medium {{ $tab === 'photos' ? 'border-b-2 border-ember text-ember' : 'text-mist' }}">Photos</button>
            </div>

            @if ($tab === 'photos')
                <div class="grid grid-cols-3 gap-1">
                    @forelse ($posts as $item)
                        <a href="{{ route('portfolio.show', $item) }}" wire:key="photo-{{ $item->id }}" wire:navigate>
                            <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover">
                        </a>
                    @empty
                        <p class="col-span-3 py-8 text-sm text-mist">No photos yet.</p>
                    @endforelse
                </div>
            @else
                <div class="flex flex-col gap-5">
                    @if ($isOwnProfile)
                        <a href="{{ route('portfolio.create') }}" class="feed-card flex items-center gap-3 px-4 py-3" wire:navigate>
                            <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold">{{ $student->initials() }}</span>
                            <span class="flex-1 rounded-full bg-wall px-4 py-3 text-sm text-mist">Share a new post with campus…</span>
                        </a>
                    @endif

                    @forelse ($posts as $item)
                        <livewire:post-card :item="$item" wire:key="profile-post-{{ $item->id }}" />
                    @empty
                        <p class="rounded-[1.75rem] bg-white p-8 text-sm text-mist">No posts yet.@if ($isOwnProfile) Share your first piece.@endif</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>
