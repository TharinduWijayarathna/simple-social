<div class="px-4 py-8 lg:px-10 space-y-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-ink/10 pb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-ember font-semibold">Campus & Talent Hub</p>
            <h1 class="font-display text-4xl mt-1">Events & Open Calls</h1>
            <p class="mt-1 text-mist">Exhibitions, concerts, hackathons & showcases — apply, get chosen, and participate.</p>
        </div>
        @if (auth()->check() && auth()->user()->canOrganizeEvents())
            <a href="{{ route('events.create') }}" class="btn-dark shrink-0" wire:navigate>
                + Create Event
            </a>
        @endif
    </div>

    {{-- Notification Alert if student is chosen for any events --}}
    @if ($chosenCount > 0 && $activeTab !== 'chosen')
        <div class="rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/10 via-amber-400/5 to-amber-500/10 p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-amber-400 text-amber-950">
                    <x-icon name="sparkles" class="size-5 text-amber-950" />
                </div>
                <div>
                    <h3 class="font-bold text-ink text-sm">Congratulations! You've been chosen for {{ $chosenCount }} campus {{ Str::plural('event', $chosenCount) }}!</h3>
                    <p class="text-xs text-mist">Check your chosen events tab to view contact details and instructions from campus organizers.</p>
                </div>
            </div>
            <button wire:click="$set('activeTab', 'chosen')" class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-600 transition shrink-0">
                View Chosen Events →
            </button>
        </div>
    @endif

    {{-- Tab Bar --}}
    <div class="flex items-center gap-2 border-b border-ink/10 pb-3 overflow-x-auto">
        <button wire:click="$set('activeTab', 'all')"
                class="rounded-xl px-4 py-2 text-sm font-semibold transition shrink-0
                       {{ $activeTab === 'all' ? 'bg-ink text-white' : 'bg-white text-mist hover:text-ink border border-ink/10' }}">
            All Events
        </button>

        @auth
            <button wire:click="$set('activeTab', 'my_rsvps')"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition shrink-0 flex items-center gap-2
                           {{ $activeTab === 'my_rsvps' ? 'bg-ink text-white' : 'bg-white text-mist hover:text-ink border border-ink/10' }}">
                My Applications
                @if ($myAppCount > 0)
                    <span class="rounded-full bg-ember px-2 py-0.5 text-[10px] font-bold text-white">{{ $myAppCount }}</span>
                @endif
            </button>

            <button wire:click="$set('activeTab', 'chosen')"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition shrink-0 flex items-center gap-2
                           {{ $activeTab === 'chosen' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100' }}">
                <x-icon name="sparkles" class="size-4" />
                Chosen Events
                @if ($chosenCount > 0)
                    <span class="rounded-full bg-amber-900 px-2 py-0.5 text-[10px] font-bold text-white">{{ $chosenCount }}</span>
                @endif
            </button>

            @if (auth()->user()->canOrganizeEvents())
                <button wire:click="$set('activeTab', 'my_campus')"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition shrink-0
                               {{ $activeTab === 'my_campus' ? 'bg-ink text-white' : 'bg-white text-mist hover:text-ink border border-ink/10' }}">
                    My Campus Events (Organizer)
                </button>
            @endif
        @endauth
    </div>

    {{-- TAB 1: ALL EVENTS --}}
    @if ($activeTab === 'all')
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($events as $event)
                <a href="{{ route('events.show', $event) }}" 
                   class="group flex flex-col justify-between rounded-3xl border border-ink/10 bg-white p-6 transition duration-200 hover:-translate-y-1 hover:shadow-lg" 
                   wire:key="event-card-{{ $event->id }}" 
                   wire:navigate>
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-full bg-wall px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-mist">
                                {{ $event->event_type ?? 'Event' }}
                            </span>
                            @if ($event->talent)
                                <span class="text-xs text-ember font-medium">{{ $event->talent->name }}</span>
                            @endif
                        </div>

                        <h2 class="mt-4 font-display text-2xl group-hover:text-ember transition">{{ $event->title }}</h2>
                        <p class="mt-2 line-clamp-2 text-sm text-mist">{{ $event->description }}</p>
                    </div>

                    <div class="mt-6 border-t border-ink/8 pt-4 space-y-2 text-xs text-mist">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <x-icon name="calendar" class="size-3.5" />
                                <span>{{ $event->starts_at->format('D, M j · g:ia') }}</span>
                            </span>
                            <span class="font-semibold text-ink">{{ $event->applications_count }} applicants</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <x-icon name="map-pin" class="size-3.5" />
                                <span>{{ $event->location ?? 'Campus Grounds' }}</span>
                            </span>
                            <span>By {{ $event->organizer->name }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center text-mist">
                    <p class="font-medium text-lg">No upcoming events found</p>
                    <p class="text-sm mt-1">Check back later or change your search filter.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    @endif

    {{-- TAB 2: MY APPLICATIONS --}}
    @if ($activeTab === 'my_rsvps')
        <div class="space-y-4">
            <h2 class="font-display text-2xl">My Applications & RSVPs</h2>
            
            @if ($myApplications->isEmpty())
                <div class="rounded-3xl border border-dashed border-ink/15 bg-white p-12 text-center text-mist">
                    <p class="font-medium">You haven't applied or RSVP'd to any events yet.</p>
                    <button wire:click="$set('activeTab', 'all')" class="mt-4 text-sm font-semibold text-ember underline">Browse all events →</button>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($myApplications as $app)
                        <div class="rounded-2xl border bg-white p-6 shadow-sm flex flex-col justify-between
                                    {{ $app->isAccepted() ? 'border-amber-300 ring-2 ring-amber-400/20' : 'border-ink/10' }}"
                             wire:key="my-app-{{ $app->id }}">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-bold capitalize flex items-center gap-1
                                                 {{ $app->isAccepted() ? 'bg-emerald-600 text-white' : ($app->isDeclined() ? 'bg-red-600 text-white' : 'bg-amber-500 text-white') }}">
                                        @if ($app->isAccepted())
                                            <x-icon name="sparkles" class="size-3" />
                                        @endif
                                        <span>{{ $app->isAccepted() ? 'Chosen' : $app->status->value }}</span>
                                    </span>
                                    <span class="text-xs text-mist">{{ $app->created_at->diffForHumans() }}</span>
                                </div>

                                <h3 class="mt-3 font-display text-xl">
                                    <a href="{{ route('events.show', $app->event) }}" class="hover:text-ember transition" wire:navigate>
                                        {{ $app->event->title }}
                                    </a>
                                </h3>

                                <p class="mt-1 text-xs text-mist">
                                    Role applied: <strong class="text-ink">{{ $app->talent?->name ?? 'General Attendee' }}</strong>
                                    · Host: {{ $app->event->organizer->name }}
                                </p>
                            </div>

                            <div class="mt-6 flex items-center justify-between border-t border-ink/8 pt-4">
                                <span class="flex items-center gap-1 text-xs text-mist">
                                    <x-icon name="calendar" class="size-3.5" />
                                    <span>{{ $app->event->starts_at->format('M j, g:ia') }}</span>
                                </span>
                                <a href="{{ route('events.show', $app->event) }}" class="text-xs font-bold text-ember hover:underline" wire:navigate>
                                    View Event Details →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 3: CHOSEN EVENTS --}}
    @if ($activeTab === 'chosen')
        <div class="space-y-6">
            <div>
                <h2 class="font-display text-2xl text-ink flex items-center gap-1.5">
                    <x-icon name="sparkles" class="size-6 text-amber-500" />
                    <span>Events You Have Been Chosen For</span>
                </h2>
                <p class="text-sm text-mist mt-1">Campus organizers selected you for these events. Review contact details and instructions below to connect with campus!</p>
            </div>

            @if ($chosenApplications->isEmpty())
                <div class="rounded-3xl border border-dashed border-ink/15 bg-white p-12 text-center text-mist">
                    <p class="font-medium">No chosen events yet.</p>
                    <p class="text-xs mt-1">Apply to talent open calls and check back when campus organizers review your application!</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($chosenApplications as $app)
                        <div class="rounded-3xl border-2 border-amber-400/60 bg-gradient-to-r from-amber-500/10 via-white to-amber-500/5 p-6 md:p-8 shadow-md"
                             wire:key="chosen-app-{{ $app->id }}">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-ink/10 pb-6">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-amber-950">
                                        <x-icon name="sparkles" class="size-3.5 text-amber-950" />
                                        <span>SELECTED FOR THIS EVENT</span>
                                    </span>
                                    <h3 class="mt-3 font-display text-3xl text-ink">
                                        <a href="{{ route('events.show', $app->event) }}" class="hover:text-ember transition" wire:navigate>
                                            {{ $app->event->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-1 text-sm text-mist">
                                        Selected Role: <strong class="text-ink font-semibold">{{ $app->talent?->name ?? 'Participant' }}</strong>
                                        · Hosted by {{ $app->event->organizer->name }}
                                    </p>
                                </div>

                                <a href="{{ route('events.show', $app->event) }}" class="rounded-xl bg-ember px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-ember/90 transition shrink-0" wire:navigate>
                                    View Full Event Page →
                                </a>
                            </div>

                            {{-- Campus Contact Details --}}
                            <div class="mt-6">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900">Campus Contact Information</h4>
                                
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if ($app->event->contact_email)
                                        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 border border-ink/10">
                                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-ember/10 text-ember">
                                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-mist">Campus Email</p>
                                                <a href="mailto:{{ $app->event->contact_email }}" class="font-semibold text-ink hover:text-ember text-sm">
                                                    {{ $app->event->contact_email }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($app->event->contact_phone)
                                        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 border border-ink/10">
                                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600">
                                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-mist">Campus Phone / WhatsApp</p>
                                                <a href="tel:{{ $app->event->contact_phone }}" class="font-semibold text-ink hover:text-ember text-sm">
                                                    {{ $app->event->contact_phone }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($app->event->contact_instructions)
                                    <div class="mt-4 rounded-2xl bg-white p-4 border border-ink/10 text-sm">
                                        <p class="text-xs font-bold uppercase tracking-wider text-mist">Attendance Instructions</p>
                                        <p class="mt-1 text-ink leading-relaxed whitespace-pre-wrap">{{ $app->event->contact_instructions }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 4: MY CAMPUS EVENTS (For Organizers) --}}
    @if ($activeTab === 'my_campus' && auth()->user()->canOrganizeEvents())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-2xl">Events Created by Your Campus</h2>
                <a href="{{ route('events.create') }}" class="btn-dark" wire:navigate>+ Create New Event</a>
            </div>

            @if ($myCampusEvents->isEmpty())
                <div class="rounded-3xl border border-dashed border-ink/15 bg-white p-12 text-center text-mist">
                    <p class="font-medium">No events created yet.</p>
                    <a href="{{ route('events.create') }}" class="mt-4 inline-block btn-dark" wire:navigate>Create Your First Campus Event</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($myCampusEvents as $ev)
                        <div class="rounded-2xl border border-ink/10 bg-white p-6 shadow-sm flex flex-col justify-between" wire:key="campus-ev-{{ $ev->id }}">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="rounded-full bg-wall px-2.5 py-0.5 text-xs font-semibold uppercase text-mist">
                                        {{ $ev->event_type ?? 'Event' }}
                                    </span>
                                    <span class="text-xs font-bold text-ember">{{ $ev->applications_count }} applicants</span>
                                </div>

                                <h3 class="mt-3 font-display text-xl">
                                    <a href="{{ route('events.show', $ev) }}" class="hover:text-ember transition" wire:navigate>
                                        {{ $ev->title }}
                                    </a>
                                </h3>

                                <p class="mt-1 flex items-center gap-1 text-xs text-mist">
                                    <x-icon name="calendar" class="size-3.5" />
                                    <span>{{ $ev->starts_at->format('l, M j, Y · g:ia') }}</span>
                                </p>
                            </div>

                            <div class="mt-6 flex items-center justify-between border-t border-ink/8 pt-4">
                                <span class="text-xs font-semibold text-emerald-600">
                                    {{ $ev->applications->where('status', \App\Enums\EventApplicationStatus::Accepted)->count() }} Selected Candidates
                                </span>
                                <a href="{{ route('events.show', $ev) }}" class="text-xs font-bold text-ember hover:underline" wire:navigate>
                                    Manage Candidates →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
