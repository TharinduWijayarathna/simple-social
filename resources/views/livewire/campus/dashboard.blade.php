<div class="flex flex-col min-h-full">

    {{-- Page header --}}
    <div class="border-b border-ink/10 bg-white px-6 py-5">
        <div class="flex items-center justify-between">
            <div>
                @if ($activeTab === 'overview')
                    <h1 class="text-xl font-semibold">Overview</h1>
                    <p class="mt-0.5 text-sm text-mist">Your campus at a glance.</p>
                @elseif ($activeTab === 'students')
                    <h1 class="text-xl font-semibold">Student Management</h1>
                    <p class="mt-0.5 text-sm text-mist">Approve or reject student registrations.</p>
                @else
                    <h1 class="text-xl font-semibold">Event Management</h1>
                    <p class="mt-0.5 text-sm text-mist">Create and manage campus events.</p>
                @endif
            </div>
            @if ($activeTab === 'events')
                <a href="{{ route('events.create') }}"
                   class="rounded-xl bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90"
                   wire:navigate>
                    + New event
                </a>
            @endif
        </div>

        {{-- Tab bar --}}
        <div class="mt-4 flex gap-1 -mb-5">
            <button wire:click="$set('activeTab', 'overview')"
                    class="border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'overview' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Overview
            </button>
            <button wire:click="$set('activeTab', 'students')"
                    class="flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'students' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Students
                @if ($totalPending > 0)
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $totalPending }}</span>
                @endif
            </button>
            <button wire:click="$set('activeTab', 'events')"
                    class="border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'events' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Events
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 px-6 py-6">

        {{-- ── OVERVIEW TAB ── --}}
        @if ($activeTab === 'overview')

            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Students</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalStudents }}</p>
                    <p class="mt-1 text-xs text-mist">Approved accounts</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Pending</p>
                    <p class="mt-2 text-3xl font-semibold {{ $totalPending > 0 ? 'text-ember' : '' }}">{{ $totalPending }}</p>
                    <p class="mt-1 text-xs text-mist">Awaiting approval</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Events</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalEvents }}</p>
                    <p class="mt-1 text-xs text-mist">Created by you</p>
                </div>
            </div>

            @if ($totalPending > 0)
                <div class="mt-6 flex items-center gap-3 rounded-2xl border border-ember/20 bg-ember/5 px-5 py-4">
                    <div class="size-2.5 rounded-full bg-ember"></div>
                    <p class="text-sm">
                        <span class="font-semibold">{{ $totalPending }} student{{ $totalPending > 1 ? 's' : '' }}</span>
                        waiting for approval.
                        <button wire:click="$set('activeTab', 'students')" class="ml-1 font-semibold text-ember underline-offset-2 hover:underline">Review now →</button>
                    </p>
                </div>
            @endif

            {{-- Recent events --}}
            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Recent events</h2>
                </div>
                @if ($events->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No events yet. <button wire:click="$set('activeTab', 'events')" class="text-ember underline-offset-2 hover:underline">Create one</button>.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($events->take(5) as $event)
                            <li class="flex items-center justify-between gap-4 px-5 py-3 text-sm" wire:key="ov-ev-{{ $event->id }}">
                                <div>
                                    <p class="font-medium">{{ $event->title }}</p>
                                    <p class="text-xs text-mist">{{ $event->starts_at->format('M j, Y · g:ia') }} · {{ $event->location }}</p>
                                </div>
                                <span class="shrink-0 text-xs text-ember">{{ $event->applications_count }} going</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        {{-- ── STUDENTS TAB ── --}}
        @elseif ($activeTab === 'students')

            {{-- Pending approvals --}}
            <div class="rounded-2xl border border-ink/8 bg-white">
                <div class="flex items-center justify-between border-b border-ink/8 px-5 py-4">
                    <div>
                        <h2 class="font-semibold">Pending registrations</h2>
                        <p class="text-sm text-mist">Students waiting to access VibeCraft</p>
                    </div>
                    @if ($pendingStudents->isNotEmpty())
                        <span class="rounded-full bg-ember px-2.5 py-0.5 text-xs font-semibold text-white">{{ $pendingStudents->count() }}</span>
                    @endif
                </div>

                @if ($pendingStudents->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No pending registrations ✓</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($pendingStudents as $student)
                            <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-4" wire:key="pend-{{ $student->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 items-center justify-center rounded-full bg-wall text-sm font-semibold text-ink">
                                        {{ $student->initials() }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $student->name }}</p>
                                        <p class="text-sm text-mist">{{ $student->email }}</p>
                                        <p class="text-xs text-mist">Registered {{ $student->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="rejectStudent({{ $student->id }})"
                                            class="rounded-lg border border-ink/15 px-4 py-2 text-sm font-medium transition hover:bg-ink/5">
                                        Reject
                                    </button>
                                    <button wire:click="approveStudent({{ $student->id }})"
                                            class="rounded-lg bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90">
                                        Approve
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Approved students --}}
            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Approved students</h2>
                    <p class="text-sm text-mist">{{ $approvedStudents->count() }} active student{{ $approvedStudents->count() !== 1 ? 's' : '' }}</p>
                </div>

                @if ($approvedStudents->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No approved students yet.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($approvedStudents as $student)
                            <li class="flex items-center justify-between gap-4 px-5 py-3 text-sm" wire:key="appr-{{ $student->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-wall text-xs font-semibold text-ink">
                                        {{ $student->initials() }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $student->name }}</p>
                                        <p class="text-xs text-mist">{{ $student->email }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-mist">{{ $student->created_at->format('M j, Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        {{-- ── EVENTS TAB ── --}}
        @else

            @if ($events->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-ink/15 bg-white py-20 text-center">
                    <svg class="size-12 text-mist" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 font-medium text-mist">No events yet</p>
                    <p class="mt-1 text-sm text-mist">Post campus nights, open mics, and exhibitions.</p>
                    <a href="{{ route('events.create') }}"
                       class="mt-6 rounded-xl bg-ember px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ember/90"
                       wire:navigate>
                        Create first event
                    </a>
                </div>
            @else
                <div class="grid gap-6 lg:grid-cols-[20rem_minmax(0,1fr)]">
                    {{-- Event list --}}
                    <ul class="flex flex-col gap-2">
                        @foreach ($events as $event)
                            <li wire:key="ev-{{ $event->id }}">
                                <button type="button"
                                        wire:click="selectEvent({{ $event->id }})"
                                        class="w-full rounded-xl border px-4 py-3 text-left transition
                                               {{ $selectedEvent?->is($event) ? 'border-ember bg-white shadow-sm' : 'border-ink/10 bg-white/70 hover:bg-white' }}">
                                    <p class="text-xs uppercase tracking-wide text-mist">{{ $event->starts_at->format('D, M j') }}</p>
                                    <p class="mt-0.5 font-medium">{{ $event->title }}</p>
                                    <div class="mt-1.5 flex items-center justify-between">
                                        <span class="text-xs text-mist">{{ $event->location }}</span>
                                        <span class="text-xs font-semibold text-ember">{{ $event->applications_count }} going</span>
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Event detail --}}
                    @if ($selectedEvent)
                        <div class="rounded-2xl border border-ink/8 bg-white p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-mist">{{ $selectedEvent->starts_at->format('l, F j · g:ia') }}</p>
                                    <h2 class="mt-1 font-display text-2xl">{{ $selectedEvent->title }}</h2>
                                    <p class="mt-1 text-sm text-mist">{{ $selectedEvent->location }}</p>
                                </div>
                                <a href="{{ route('events.show', $selectedEvent) }}"
                                   class="shrink-0 rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5"
                                   wire:navigate>
                                    View page ↗
                                </a>
                            </div>

                            @if ($selectedEvent->description)
                                <p class="mt-4 whitespace-pre-wrap text-sm text-ink/80">{{ $selectedEvent->description }}</p>
                            @endif

                            <div class="mt-6">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-mist">
                                    Applicants & Participants · {{ $selectedEvent->applications->count() }}
                                </h3>
                                <ul class="mt-3 divide-y divide-ink/8">
                                    @forelse ($selectedEvent->applications as $application)
                                        <li class="flex flex-col md:flex-row md:items-center justify-between gap-3 py-3 text-sm" wire:key="app-{{ $application->id }}">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-ink">{{ $application->user->name }}</span>
                                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold capitalize
                                                                 {{ $application->isAccepted() ? 'bg-emerald-100 text-emerald-800' : ($application->isDeclined() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                                        {{ $application->status->value }}
                                                    </span>
                                                </div>
                                                @if ($application->talent)
                                                    <p class="text-xs text-mist">Role: <strong class="text-ink">{{ $application->talent->name }}</strong></p>
                                                @endif
                                                @if ($application->message)
                                                    <p class="mt-1 text-xs italic text-mist">"{{ $application->message }}"</p>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                @if (! $application->isAccepted())
                                                    <button wire:click="selectCandidate({{ $application->id }})" class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm hover:bg-emerald-700">
                                                        Select Student
                                                    </button>
                                                @else
                                                    <span class="text-xs font-bold text-emerald-600">✓ Selected</span>
                                                @endif
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-sm text-mist">Nobody has joined yet.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        @endif
    </div>
</div>
