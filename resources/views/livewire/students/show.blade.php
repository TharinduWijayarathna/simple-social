<div class="mx-auto max-w-4xl px-4 py-8">
    <div class="flex items-start gap-6 md:gap-16">
        <div class="p-2 shrink-0 overflow-visible">
            @if ($highlights->isNotEmpty())
                <a href="{{ route('status.show', $highlights->first()) }}" class="flex size-24 items-center justify-center overflow-hidden rounded-full bg-studio text-2xl font-semibold text-gold ring-4 ring-amber-400 ring-offset-4 ring-offset-wall md:size-36 md:text-4xl shadow-md transition hover:scale-105" wire:navigate>
                    @if ($student->avatarUrl())
                        <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                    @else
                        {{ $student->initials() }}
                    @endif
                </a>
            @else
                <span class="flex size-24 items-center justify-center overflow-hidden rounded-full bg-studio text-2xl font-semibold text-gold md:size-36 md:text-4xl shadow-md">
                    @if ($student->avatarUrl())
                        <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                    @else
                        {{ $student->initials() }}
                    @endif
                </span>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-xl font-semibold tracking-tight md:text-2xl">{{ $student->name }}</h1>
                @if ($isOwnProfile)
                    <a href="{{ route('profile.edit') }}" class="btn-ghost" wire:navigate>Edit profile</a>
                    <a href="{{ route('portfolio.create') }}" class="btn-primary" wire:navigate>Add post</a>
                    <x-logout-button />
                @elseif (auth()->check())
                    <button type="button" wire:click="follow" class="{{ $isFollowing ? 'btn-ghost' : 'btn-primary' }}">
                        {{ $isFollowing ? 'Following' : 'Follow' }}
                    </button>
                @endif
            </div>

            <ul class="mt-5 flex gap-6 text-sm md:gap-10">
                <li><span class="font-semibold">{{ $posts->count() }}</span> <span class="text-mist">posts</span></li>
                <li><span class="font-semibold">{{ $student->followers_count }}</span> <span class="text-mist">followers</span></li>
                <li><span class="font-semibold">{{ $student->following_count }}</span> <span class="text-mist">following</span></li>
            </ul>

            <div class="mt-4 text-sm space-y-2">
                @if ($student->profile?->profile_type)
                    <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3.5 py-1 text-xs font-bold text-amber-900 shadow-sm border border-amber-300/50">
                        <span class="flex items-center gap-1">
                            @if (str_contains($student->profile->profile_type, 'Performing'))
                                <x-icon name="microphone" class="size-3.5 text-amber-700" />
                            @elseif (str_contains($student->profile->profile_type, 'Creative'))
                                <x-icon name="paint-brush" class="size-3.5 text-amber-700" />
                            @elseif (str_contains($student->profile->profile_type, 'Sports'))
                                <x-icon name="trophy" class="size-3.5 text-amber-700" />
                            @elseif (str_contains($student->profile->profile_type, 'Unique'))
                                <x-icon name="sparkles" class="size-3.5 text-amber-700" />
                            @else
                                <x-icon name="user" class="size-3.5 text-amber-700" />
                            @endif
                            <span>{{ $student->profile->displayProfileType() }}</span>
                        </span>
                        @if ($student->profile->primaryTalentModel)
                            <span class="text-amber-700">· {{ $student->profile->primaryTalentModel->name }}</span>
                        @endif
                    </div>
                @endif

                @if ($student->profile?->headline)
                    <p class="font-semibold text-ink">{{ $student->profile->headline }}</p>
                @endif
                @if ($student->profile?->bio)
                    <p class="leading-6 text-ink/80">{{ $student->profile->bio }}</p>
                @endif
                
                <ul class="flex flex-col gap-1 text-xs text-mist">
                    @if ($student->profile?->batch || $student->profile?->program)
                        <li class="font-medium text-ember">
                            🎓 {{ $student->profile->batch ?: 'Campus Student' }}
                            @if ($student->profile->program) · {{ $student->profile->program }} @endif
                        </li>
                    @endif
                    @if ($student->profile?->faculty)
                        <li>Studies {{ $student->profile->faculty }}@if ($student->profile->department) · {{ $student->profile->department }}@endif</li>
                    @endif
                    @if ($student->profile?->location)
                        <li>Lives in {{ $student->profile->location }}</li>
                    @endif
                    @if ($student->profile?->birthday)
                        <li>Born {{ $student->profile->birthday->format('F j') }}</li>
                    @endif
                </ul>

                @if ($student->profile?->talents?->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($student->profile->talents as $talent)
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-medium border border-ink/8 shadow-sm" wire:key="badge-{{ $talent->id }}">
                                {{ $talent->name }}
                                <span class="text-[10px] text-mist">({{ $talent->category }})</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($highlights->isNotEmpty())
        <div class="mt-8 flex gap-5 overflow-x-auto p-2 pb-3">
            @foreach ($highlights as $status)
                <a href="{{ route('status.show', $status) }}" class="flex w-16 shrink-0 flex-col items-center gap-1.5 transition hover:scale-105" wire:key="highlight-{{ $status->id }}" wire:navigate>
                    <img src="{{ $status->imageUrl() }}" alt="" class="size-16 rounded-full object-cover ring-2 ring-amber-400 ring-offset-2 ring-offset-wall shadow-sm">
                    <span class="w-full truncate text-center text-[11px] font-semibold text-mist">{{ $status->created_at->isToday() ? 'Today' : $status->created_at->format('M j') }}</span>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-8 flex justify-center gap-16 border-t border-ink/10">
        <button type="button" wire:click="showGallery" class="inline-flex items-center gap-2 border-t px-1 py-3 text-xs font-semibold uppercase tracking-[0.18em] {{ $tab === 'gallery' ? 'border-ink text-ink' : 'border-transparent text-mist' }}">
            <x-icon name="grid" class="size-4" />
            Gallery
        </button>
        <button type="button" wire:click="showFeed" class="inline-flex items-center gap-2 border-t px-1 py-3 text-xs font-semibold uppercase tracking-[0.18em] {{ $tab === 'feed' ? 'border-ink text-ink' : 'border-transparent text-mist' }}">
            <x-icon name="bars" class="size-4" />
            Posts
        </button>
    </div>

    @if ($tab === 'gallery')
        <div class="grid grid-cols-3 gap-1 md:gap-2">
            @forelse ($posts as $item)
                <a href="{{ route('portfolio.show', $item) }}" class="group relative aspect-square overflow-hidden bg-studio" wire:key="gallery-{{ $item->id }}" wire:navigate>
                    <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="size-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 flex items-center justify-center gap-5 bg-studio/55 text-sm font-semibold text-paper opacity-0 transition group-hover:opacity-100">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icon name="heart" solid class="size-4" />
                            {{ $item->likes_count }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <x-icon name="chat" class="size-4" />
                            {{ $item->comments_count }}
                        </span>
                    </span>
                    @if ($item->media_type === \App\Enums\PortfolioMediaType::Video)
                        <span class="absolute right-2 top-2 text-white">
                            <x-icon name="play" solid class="size-4" />
                        </span>
                    @endif
                </a>
            @empty
                <p class="col-span-3 py-16 text-center text-sm text-mist">No work in this gallery yet.@if ($isOwnProfile) Share your first piece.@endif</p>
            @endforelse
        </div>
    @else
        <div class="mx-auto flex max-w-xl flex-col gap-5 pt-6">
            @forelse ($posts as $item)
                <livewire:post-card :item="$item" wire:key="profile-post-{{ $item->id }}" />
            @empty
                <p class="rounded-[1.75rem] bg-white p-8 text-sm text-mist">No posts yet.@if ($isOwnProfile) Share your first piece.@endif</p>
            @endforelse
        </div>
    @endif
</div>
