<div class="page-shell py-6">
    <div class="relative">
        <x-icon name="people" class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-mist" />
        <input wire:model.live.debounce.300ms="search" type="search" class="field w-full rounded-full py-3 pl-11 shadow-sm" placeholder="Search by name, category, batch, degree, program..." aria-label="Search campus">
    </div>

    {{-- Category Tabs --}}
    <div class="mt-4 flex gap-2 overflow-x-auto pb-2 scrollbar-none">
        @foreach ($categories as $catKey => $catLabel)
            <button type="button"
                    wire:click="setCategory('{{ $catKey }}')"
                    class="shrink-0 rounded-full px-4 py-2 text-xs font-bold transition shadow-sm border {{ $category === $catKey ? 'bg-ember text-white border-ember' : 'bg-white text-ink border-ink/10 hover:bg-wall' }}">
                {{ $catLabel }}
            </button>
        @endforeach
    </div>

    <h1 class="mt-6 text-base font-semibold">{{ $this->search === '' && $this->category === 'All' ? 'Suggested for you' : 'People on Campus' }}</h1>

    <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
        @forelse ($students as $student)
            @php
                $cover = $student->portfolioItems->first();
            @endphp
            <article class="relative overflow-hidden rounded-2xl bg-studio border border-ink/8 shadow-sm" wire:key="stu-{{ $student->id }}">
                <a href="{{ route('students.show', $student) }}" class="group block aspect-[3/4]" wire:navigate>
                    @if ($cover)
                        <img src="{{ $cover->displayUrl() }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105">
                    @elseif ($student->avatarUrl())
                        <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <span class="flex size-full items-center justify-center text-4xl font-semibold text-gold">{{ $student->initials() }}</span>
                    @endif
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></span>
                    
                    {{-- Profile Type Badge --}}
                    @if ($student->profile?->profile_type)
                        <span class="absolute top-2 left-2 flex items-center gap-1 rounded-full bg-black/60 px-2 py-0.5 text-[9px] font-bold text-amber-300 backdrop-blur truncate max-w-[85%]">
                            @if (str_contains($student->profile->profile_type, 'Performing'))
                                <x-icon name="microphone" class="size-3 text-amber-300" />
                            @elseif (str_contains($student->profile->profile_type, 'Creative'))
                                <x-icon name="paint-brush" class="size-3 text-amber-300" />
                            @elseif (str_contains($student->profile->profile_type, 'Sports'))
                                <x-icon name="trophy" class="size-3 text-amber-300" />
                            @elseif (str_contains($student->profile->profile_type, 'Unique'))
                                <x-icon name="sparkles" class="size-3 text-amber-300" />
                            @else
                                <x-icon name="user" class="size-3 text-amber-300" />
                            @endif
                            <span>{{ $student->profile->displayProfileType() }}</span>
                        </span>
                    @endif

                    <span class="absolute inset-x-3 bottom-14 flex min-w-0 items-center gap-2 text-white">
                        <span @class([
                            'flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-studio text-[10px] font-semibold text-gold',
                            'ring-2 ring-amber-400 ring-offset-2 ring-offset-black/40' => $student->has_active_status,
                        ])>
                            @if ($student->avatarUrl())
                                <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                            @else
                                {{ $student->initials() }}
                            @endif
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold leading-tight text-white drop-shadow">{{ $student->name }}</span>
                            <span class="block truncate text-[11px] text-white/80">
                                {{ $student->profile?->primaryTalent()?->name ?? $student->profile?->batch ?? 'Student' }} · {{ $student->followers_count }} followers
                            </span>
                        </span>
                    </span>
                </a>
                <div class="absolute inset-x-3 bottom-3">
                    <button type="button" wire:click="follow({{ $student->id }})" @class([
                        'w-full rounded-full py-1.5 text-xs font-bold transition',
                        'bg-white/20 text-white ring-1 ring-white/40 hover:bg-white/30' => $student->followed_by_viewer,
                        'bg-ember text-white hover:bg-ember/90' => ! $student->followed_by_viewer,
                    ])>
                        {{ $student->followed_by_viewer ? 'Following' : 'Follow' }}
                    </button>
                </div>
            </article>
        @empty
            <p class="col-span-full py-16 text-center text-sm text-mist">No creators found matching that filter yet.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $students->links() }}</div>

    @if ($explore->isNotEmpty())
        <h2 class="mt-10 text-base font-semibold">Explore Campus Works</h2>
        <div class="mt-4 grid grid-cols-3 gap-1 md:gap-2">
            @foreach ($explore as $item)
                <a href="{{ route('portfolio.show', $item) }}" class="group relative aspect-square overflow-hidden bg-studio rounded-xl" wire:key="explore-{{ $item->id }}" wire:navigate>
                    <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="size-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></span>
                    <span class="absolute inset-x-2 bottom-2 truncate text-xs font-semibold text-white opacity-0 transition group-hover:opacity-100">{{ $item->user->name }}</span>
                    @if ($item->media_type === \App\Enums\PortfolioMediaType::Video)
                        <span class="absolute right-2 top-2 text-white">
                            <x-icon name="play" solid class="size-4" />
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
