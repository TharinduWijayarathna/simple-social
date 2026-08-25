<div class="flex flex-col min-h-full">

    {{-- Page header --}}
    <div class="border-b border-ink/10 bg-white px-6 py-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Talent Rankings</h1>
                <p class="mt-0.5 text-sm text-mist">Create and manage talent-based leaderboards for your campus.</p>
            </div>
            @if (! $showForm)
                <button wire:click="openForm"
                        class="rounded-xl bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90">
                    + New ranking
                </button>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 px-6 py-6 space-y-4">

        {{-- Add ranking form --}}
        @if ($showForm)
            <div class="rounded-2xl border border-ember/20 bg-white p-5">
                <h2 class="mb-4 font-semibold">Add a new ranking</h2>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="talent_select" class="mb-1.5 block text-xs font-medium text-mist uppercase tracking-wider">Talent</label>
                        <select id="talent_select"
                                wire:model.live="talent_id"
                                class="w-full rounded-xl border border-ink/15 bg-white px-3 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-2 focus:ring-ember/20">
                            <option value="">Select a talent…</option>
                            @php $currentCategory = null; @endphp
                            @foreach ($talents as $talent)
                                @if ($talent->category !== $currentCategory)
                                    @if ($currentCategory !== null)
                                        </optgroup>
                                    @endif
                                    <optgroup label="{{ $talent->category }}">
                                    @php $currentCategory = $talent->category; @endphp
                                @endif
                                <option value="{{ $talent->id }}">{{ $talent->name }}</option>
                            @endforeach
                            @if ($currentCategory !== null)
                                </optgroup>
                            @endif
                        </select>
                        @error('talent_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1">
                        <label for="ranking_title" class="mb-1.5 block text-xs font-medium text-mist uppercase tracking-wider">Display title</label>
                        <input id="ranking_title"
                               type="text"
                               wire:model="title"
                               placeholder="e.g. Top Singers"
                               class="w-full rounded-xl border border-ink/15 bg-white px-3 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-2 focus:ring-ember/20">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="save"
                                class="rounded-xl bg-ember px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ember/90">
                            Save
                        </button>
                        <button wire:click="cancelForm"
                                class="rounded-xl border border-ink/15 px-4 py-2.5 text-sm font-medium text-mist transition hover:bg-ink/5">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Rankings list --}}
        @if ($rankingsWithLeaders->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-ink/15 bg-white py-16 text-center">
                <div class="mb-3 text-amber-500">
                    <x-icon name="trophy" class="size-12" />
                </div>
                <p class="font-semibold">No rankings yet</p>
                <p class="mt-1 text-sm text-mist">Create talent-based leaderboards for your campus students.</p>
                @if (! $showForm)
                    <button wire:click="openForm"
                            class="mt-4 rounded-xl bg-ember px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ember/90">
                        + New ranking
                    </button>
                @endif
            </div>
        @else
            <div class="space-y-6">
                @foreach ($rankingsWithLeaders as $item)
                    @php
                        $ranking = $item['ranking'];
                        $leaders = $item['leaders'];
                    @endphp
                    <div class="rounded-2xl border border-ink/8 bg-white overflow-hidden shadow-sm" wire:key="ranking-card-{{ $ranking->id }}">
                        {{-- Ranking Header --}}
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-ink/8 px-6 py-4 bg-wall/30">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-ember/10">
                                    <x-icon name="trophy" class="size-5 text-ember" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-semibold text-base text-ink truncate">{{ $ranking->title }}</h2>
                                        <span class="rounded-full bg-wall px-2 py-0.5 text-[10px] font-bold text-mist">{{ $ranking->talent->category }}</span>
                                    </div>
                                    <p class="text-xs text-mist mt-0.5">
                                        Talent: <strong class="text-ink font-medium">{{ $ranking->talent->name }}</strong> · {{ $leaders->count() }} ranked student{{ $leaders->count() !== 1 ? 's' : '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                {{-- Active/Inactive toggle --}}
                                <button wire:click="toggle({{ $ranking->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition
                                               {{ $ranking->is_active
                                                    ? 'bg-green-50 text-green-700 hover:bg-green-100'
                                                    : 'bg-ink/5 text-mist hover:bg-ink/10' }}"
                                        title="{{ $ranking->is_active ? 'Deactivate' : 'Activate' }}">
                                    <span class="size-1.5 rounded-full {{ $ranking->is_active ? 'bg-green-500' : 'bg-mist' }}"></span>
                                    {{ $ranking->is_active ? 'Active on Campus' : 'Inactive' }}
                                </button>

                                {{-- Delete button --}}
                                <button wire:click="delete({{ $ranking->id }})"
                                        wire:confirm="Delete the '{{ $ranking->title }}' ranking?"
                                        class="rounded-lg p-2 text-mist transition hover:bg-red-50 hover:text-red-500"
                                        title="Delete ranking">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Leaderboard list --}}
                        @if ($leaders->isEmpty())
                            <div class="px-6 py-8 text-center text-sm text-mist">
                                No students currently ranked under this talent.
                            </div>
                        @else
                            <div class="divide-y divide-ink/6">
                                @foreach ($leaders as $i => $student)
                                    <div class="flex items-center justify-between gap-4 px-6 py-3.5 hover:bg-wall/20 transition" wire:key="rank-leader-{{ $ranking->id }}-{{ $student->id }}">
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            {{-- Rank number / medal --}}
                                            <div class="flex w-7 shrink-0 items-center justify-center text-center">
                                                @if ($i === 0)
                                                    <span class="flex size-6 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-white shadow-sm ring-2 ring-amber-300">1</span>
                                                @elseif ($i === 1)
                                                    <span class="flex size-6 items-center justify-center rounded-full bg-slate-400 text-xs font-bold text-white shadow-sm ring-2 ring-slate-300">2</span>
                                                @elseif ($i === 2)
                                                    <span class="flex size-6 items-center justify-center rounded-full bg-amber-700 text-xs font-bold text-white shadow-sm ring-2 ring-amber-600">3</span>
                                                @else
                                                    <span class="text-xs font-bold text-mist">#{{ $i + 1 }}</span>
                                                @endif
                                            </div>

                                            {{-- Avatar --}}
                                            <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-wall text-xs font-semibold text-ink">
                                                @if ($student->avatarUrl())
                                                    <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                                                @else
                                                    {{ $student->initials() }}
                                                @endif
                                            </div>

                                            {{-- Student details --}}
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('students.show', $student) }}" class="truncate text-sm font-semibold text-ink hover:text-ember transition" wire:navigate>
                                                        {{ $student->name }}
                                                    </a>
                                                    <a href="{{ route('students.show', $student) }}" class="text-[10px] font-medium text-ember hover:underline" wire:navigate>
                                                        View Profile ↗
                                                    </a>
                                                </div>
                                                <p class="text-xs text-mist truncate">
                                                    {{ $student->profile?->headline ?? $student->email }}
                                                    @if ($student->profile?->batch || $student->profile?->program)
                                                        · {{ implode(' · ', array_filter([$student->profile?->program, $student->profile?->batch])) }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Points & Likes count --}}
                                        <div class="shrink-0 text-right">
                                            <div class="inline-flex items-center gap-1 rounded-xl bg-ember/10 px-3 py-1.5 text-ember">
                                                <span class="text-sm font-extrabold">{{ number_format($student->talent_likes_total ?? 0) }}</span>
                                                <span class="text-[11px] font-semibold text-ember/80">points ({{ number_format($student->talent_likes_total ?? 0) }} likes)</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
