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
        @if ($rankings->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-ink/15 bg-white py-16 text-center">
                <div class="mb-3 text-4xl">🏆</div>
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
            <div class="rounded-2xl border border-ink/8 bg-white overflow-hidden">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Your rankings</h2>
                </div>
                <ul class="divide-y divide-ink/8">
                    @foreach ($rankings as $ranking)
                        <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-4" wire:key="ranking-{{ $ranking->id }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-ember/10">
                                    <span class="text-lg">🏆</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-medium">{{ $ranking->title }}</p>
                                    <p class="text-xs text-mist">{{ $ranking->talent->name }} · {{ $ranking->talent->category }}</p>
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
                                    {{ $ranking->is_active ? 'Active' : 'Inactive' }}
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
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</div>
