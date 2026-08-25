<div class="mx-auto max-w-4xl px-4 py-8 lg:px-0 space-y-8">
    {{-- Back Link & Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="text-sm font-medium text-mist hover:text-ink transition" wire:navigate>
            ← Back to Events
        </a>
        @if ($isOrganizer)
            <span class="rounded-full bg-ember/10 px-3 py-1 text-xs font-semibold text-ember">
                You are organizing this event
            </span>
        @endif
    </div>

    {{-- Flash status notification --}}
    @if (session('status'))
        <div class="rounded-2xl border border-ember/30 bg-ember/10 px-5 py-4 text-sm font-medium text-ember shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- 🎉 CHOSEN STUDENT SPECIAL NOTIFICATION & CONTACT CARD --}}
    @if ($userApplication && $userApplication->isAccepted())
        <div class="rounded-3xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/10 via-amber-400/5 to-amber-500/10 p-6 md:p-8 shadow-md">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-400/20 pb-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 px-3 py-1 text-xs font-bold text-amber-800">
                        <x-icon name="sparkles" class="size-3.5 text-amber-700" /> SELECTED CANDIDATE
                    </span>
                    <h2 class="mt-2 font-display text-2xl md:text-3xl text-ink">Congratulations! You have been chosen for this event!</h2>
                    <p class="mt-1 text-sm text-mist">
                        Campus organizers have selected your application for 
                        <strong class="text-ink font-semibold">{{ $userApplication->talent?->name ?? $event->talent?->name ?? 'this event' }}</strong>.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-amber-800">Campus Organizer Contact & Instructions</h3>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if ($event->contact_email)
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 border border-ink/8">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-ember/10 text-ember">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-mist">Campus Email</p>
                                <a href="mailto:{{ $event->contact_email }}" class="font-semibold text-ink hover:text-ember truncate block">
                                    {{ $event->contact_email }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($event->contact_phone)
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 border border-ink/8">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-mist">Campus Phone / WhatsApp</p>
                                <a href="tel:{{ $event->contact_phone }}" class="font-semibold text-ink hover:text-ember truncate block">
                                    {{ $event->contact_phone }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($event->contact_instructions)
                    <div class="mt-4 rounded-2xl bg-white p-5 border border-ink/8 space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-mist">Attendance & Report Instructions</p>
                        <p class="text-sm leading-relaxed text-ink whitespace-pre-wrap">{{ $event->contact_instructions }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Event Header Card --}}
    <div class="rounded-3xl border border-ink/10 bg-white p-6 md:p-8 shadow-sm">
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-ember px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white">
                {{ $event->event_type ?? 'Campus Event' }}
            </span>
            @if ($event->talent)
                <span class="rounded-full bg-wall px-3 py-1 text-xs font-medium text-mist">
                    {{ $event->talent->name }} Focus
                </span>
            @endif
            <span class="ml-auto text-xs font-medium text-mist">
                Hosted by <strong class="text-ink">{{ $event->organizer->name }}</strong>
            </span>
        </div>

        <h1 class="mt-4 font-display text-4xl md:text-5xl text-ink">{{ $event->title }}</h1>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 border-y border-ink/8 py-4 text-sm">
            <div class="flex items-center gap-3">
                <div class="flex size-9 items-center justify-center rounded-xl bg-wall text-mist">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-mist">Date & Time</p>
                    <p class="font-semibold text-ink">{{ $event->starts_at->format('l, F j, Y · g:ia') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex size-9 items-center justify-center rounded-xl bg-wall text-mist">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-mist">Venue / Location</p>
                    <p class="font-semibold text-ink">{{ $event->location ?? 'Campus Main Grounds' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex size-9 items-center justify-center rounded-xl bg-wall text-mist">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-mist">Capacity & Applicants</p>
                    <p class="font-semibold text-ink">
                        {{ $event->applications->count() }} registered
                        @if ($event->capacity) / {{ $event->capacity }} spots @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Description & Requirements --}}
        <div class="mt-6 space-y-6">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-mist">Overview & Description</h3>
                <p class="mt-2 text-base leading-relaxed text-ink/90 whitespace-pre-wrap">{{ $event->description }}</p>
            </div>

            @if ($event->requirements)
                <div class="rounded-2xl bg-wall/60 p-5 border border-ink/8">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-ember">Participant Prerequisites & Eligibility</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink/90 whitespace-pre-wrap">{{ $event->requirements }}</p>
                </div>
            @endif
        </div>

        {{-- Specific Talent Roles Needed --}}
        @if ($event->talents->isNotEmpty())
            <div class="mt-8 border-t border-ink/8 pt-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-mist">Requested Talent Types & Open Roles</h3>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($event->talents as $t)
                        <div class="flex items-start justify-between gap-3 rounded-2xl border border-ink/10 bg-wall/40 p-4">
                            <div>
                                <span class="font-display font-semibold text-ink">{{ $t->name }}</span>
                                @if ($t->pivot->notes)
                                    <p class="mt-0.5 text-xs text-mist">{{ $t->pivot->notes }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 rounded-full bg-ember/10 px-2.5 py-1 text-xs font-bold text-ember">
                                {{ $t->pivot->slots }} {{ Str::plural('spot', $t->pivot->slots) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- STUDENT RSVP & APPLICATION FORM --}}
    @if (! $isOrganizer && auth()->check() && auth()->user()->isStudent())
        <div class="rounded-3xl border border-ink/10 bg-white p-6 md:p-8 shadow-sm">
            <h2 class="font-display text-2xl">RSVP & Apply for Event</h2>
            <p class="mt-1 text-sm text-mist">Submit your application to participate. Campus organizers will review applications and select candidates.</p>

            @if ($userApplication)
                <div class="mt-6 rounded-2xl border p-5 flex flex-col md:flex-row md:items-center justify-between gap-4
                            {{ $userApplication->isAccepted() ? 'border-emerald-300 bg-emerald-50 text-emerald-900' : ($userApplication->isDeclined() ? 'border-red-200 bg-red-50 text-red-900' : 'border-amber-200 bg-amber-50 text-amber-900') }}">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider">Application Status:</span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold capitalize
                                         {{ $userApplication->isAccepted() ? 'bg-emerald-600 text-white' : ($userApplication->isDeclined() ? 'bg-red-600 text-white' : 'bg-amber-500 text-white') }}">
                                {{ $userApplication->status->value }}
                            </span>
                        </div>
                        @if ($userApplication->talent)
                            <p class="mt-1 text-xs">Applied Role: <strong>{{ $userApplication->talent->name }}</strong></p>
                        @endif
                        @if ($userApplication->message)
                            <p class="mt-2 text-xs italic">"{{ $userApplication->message }}"</p>
                        @endif
                    </div>

                    <button wire:click="cancelApplication" class="rounded-xl border border-ink/20 bg-white px-4 py-2 text-xs font-semibold text-ink transition hover:bg-red-50 hover:text-red-600 shrink-0">
                        Withdraw Application
                    </button>
                </div>
            @endif

            <form wire:submit="applyOrRsvp" class="mt-6 space-y-4">
                @if ($event->talents->isNotEmpty())
                    <div>
                        <label class="block text-sm font-medium text-ink">
                            Select Talent Role You're Applying For
                            <select wire:model="selected_talent_id" class="field mt-1">
                                <option value="">General Attendee / RSVP</option>
                                @foreach ($event->talents as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->pivot->slots }} open spots)</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Application Pitch / Message to Campus Organizers
                        <textarea wire:model="message" rows="3" placeholder="Tell campus organizers about your experience, links to your portfolio, or why you're excited to perform/participate..." class="field mt-1"></textarea>
                    </label>
                    @error('message') <span class="text-xs text-ember">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="rounded-xl bg-ember px-6 py-3 font-semibold text-white transition hover:bg-ember/90 shadow-sm">
                    {{ $userApplication ? 'Update Application' : 'Submit RSVP / Application' }}
                </button>
            </form>
        </div>
    @endif

    {{-- CAMPUS ORGANIZER CANDIDATE SELECTION PANEL --}}
    @if ($isOrganizer)
        <div class="rounded-3xl border border-ink/10 bg-white p-6 md:p-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-ink/8 pb-6">
                <div>
                    <h2 class="font-display text-2xl">Campus Candidate Selection</h2>
                    <p class="mt-1 text-sm text-mist">Review student applications and select chosen candidates for this event.</p>
                </div>

                {{-- Filter buttons --}}
                <div class="flex items-center gap-1 rounded-xl bg-wall p-1 text-xs font-medium">
                    <button wire:click="$set('applicantFilter', 'all')" class="rounded-lg px-3 py-1.5 transition {{ $applicantFilter === 'all' ? 'bg-white font-bold shadow-sm' : 'text-mist' }}">
                        All ({{ $event->applications->count() }})
                    </button>
                    <button wire:click="$set('applicantFilter', 'pending')" class="rounded-lg px-3 py-1.5 transition {{ $applicantFilter === 'pending' ? 'bg-white font-bold text-amber-600 shadow-sm' : 'text-mist' }}">
                        Pending ({{ $event->applications->where('status', \App\Enums\EventApplicationStatus::Pending)->count() }})
                    </button>
                    <button wire:click="$set('applicantFilter', 'accepted')" class="rounded-lg px-3 py-1.5 transition {{ $applicantFilter === 'accepted' ? 'bg-white font-bold text-emerald-600 shadow-sm' : 'text-mist' }}">
                        Chosen ({{ $event->applications->where('status', \App\Enums\EventApplicationStatus::Accepted)->count() }})
                    </button>
                </div>
            </div>

            <div class="mt-6">
                @if ($filteredApplications->isEmpty())
                    <div class="py-12 text-center text-mist text-sm">
                        No applications matching this filter.
                    </div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($filteredApplications as $app)
                            <li class="py-4 flex flex-col md:flex-row md:items-center justify-between gap-4" wire:key="app-row-{{ $app->id }}">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        @if ($app->user)
                                            <a href="{{ route('students.show', $app->user_id) }}" class="font-semibold text-ink hover:text-ember transition" wire:navigate>
                                                {{ $app->user->name }}
                                            </a>
                                            @if ($app->user->university_id)
                                                <span class="text-xs text-mist">({{ $app->user->university_id }})</span>
                                            @endif
                                        @else
                                            <span class="font-semibold text-ink">Applicant #{{ $app->user_id }}</span>
                                        @endif
                                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold capitalize
                                                     {{ $app->isAccepted() ? 'bg-emerald-100 text-emerald-800' : ($app->isDeclined() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $app->status->value }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-mist">
                                        Role applied: <strong class="text-ink">{{ $app->talent?->name ?? 'General Attendee' }}</strong>
                                        · Submitted {{ $app->created_at->diffForHumans() }}
                                    </p>

                                    @if ($app->message)
                                        <p class="mt-2 text-sm text-ink/80 bg-wall/50 p-3 rounded-xl">
                                            "{{ $app->message }}"
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if ($app->isAccepted())
                                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                            ✓ Selected
                                        </span>
                                        <button wire:click="resetCandidate({{ $app->id }})" class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium text-mist hover:text-ink">
                                            Reset
                                        </button>
                                    @else
                                        <button wire:click="declineCandidate({{ $app->id }})" class="rounded-xl border border-ink/15 px-3.5 py-2 text-xs font-semibold text-mist hover:text-red-600 hover:border-red-200">
                                            Decline
                                        </button>
                                        <button wire:click="selectCandidate({{ $app->id }})" class="flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                                            <x-icon name="sparkles" class="size-3.5 text-emerald-200" />
                                            <span>Select / Choose Student</span>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
</div>
