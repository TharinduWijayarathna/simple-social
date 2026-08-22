<div class="mx-auto max-w-3xl px-4 py-8 lg:px-0">
    <div class="flex items-center justify-between border-b border-ink/10 pb-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-ember">Campus Event Portal</p>
            <h1 class="mt-1 font-display text-4xl">Create Event</h1>
            <p class="mt-1 text-mist">Organize campus nights, open calls, showcases, and talent recruitment events.</p>
        </div>
        <a href="{{ route('events.index') }}" class="rounded-xl border border-ink/15 px-4 py-2 text-sm font-medium text-ink transition hover:bg-white" wire:navigate>
            ← Cancel
        </a>
    </div>

    <form wire:submit="save" class="mt-8 space-y-8">
        {{-- Section 1: Basic Information --}}
        <div class="rounded-2xl border border-ink/10 bg-white p-6 shadow-sm">
            <h2 class="font-display text-xl">1. Event Details</h2>
            <p class="text-sm text-mist">Basic information and summary for students.</p>

            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-ink">
                        Event Title <span class="text-ember">*</span>
                        <input type="text" wire:model="title" placeholder="e.g. VibeCraft Annual Art Showcase & Music Fest" class="field mt-1">
                    </label>
                    @error('title') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Event Category <span class="text-ember">*</span>
                        <select wire:model="event_type" class="field mt-1">
                            @foreach ($eventTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </label>
                    @error('event_type') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Primary Category / Talent Focus
                        <select wire:model="talent_id" class="field mt-1">
                            <option value="">All / Open to All</option>
                            @foreach ($talents as $talent)
                                <option value="{{ $talent->id }}">{{ $talent->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    @error('talent_id') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-ink">
                        Description & Overview <span class="text-ember">*</span>
                        <textarea wire:model="description" rows="4" placeholder="Detailed event description, agenda, what students will experience..." class="field mt-1"></textarea>
                    </label>
                    @error('description') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-ink">
                        Participant Requirements & Eligibility
                        <textarea wire:model="requirements" rows="2" placeholder="e.g. Bring own instrument, submit portfolio prior to event, open to 2nd year and above..." class="field mt-1"></textarea>
                    </label>
                    @error('requirements') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Date, Location & Capacity --}}
        <div class="rounded-2xl border border-ink/10 bg-white p-6 shadow-sm">
            <h2 class="font-display text-xl">2. Schedule & Venue</h2>
            <p class="text-sm text-mist">When and where will this event take place?</p>

            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-ink">
                        Venue / Location <span class="text-ember">*</span>
                        <input type="text" wire:model="location" placeholder="e.g. Campus Main Auditorium, Hall B or Zoom link" class="field mt-1">
                    </label>
                    @error('location') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Starts At <span class="text-ember">*</span>
                        <input type="datetime-local" wire:model="starts_at" class="field mt-1">
                    </label>
                    @error('starts_at') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Ends At
                        <input type="datetime-local" wire:model="ends_at" class="field mt-1">
                    </label>
                    @error('ends_at') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Application / RSVP Deadline
                        <input type="datetime-local" wire:model="application_deadline" class="field mt-1">
                    </label>
                    @error('application_deadline') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Total Capacity / Max Attendees
                        <input type="number" wire:model="capacity" min="1" placeholder="e.g. 100" class="field mt-1">
                    </label>
                    @error('capacity') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Talent Requirements --}}
        <div class="rounded-2xl border border-ink/10 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-xl">3. Specific Talent Requirements</h2>
                    <p class="text-sm text-mist">Specify individual talent roles and open spots needed for this event.</p>
                </div>
                <button type="button" wire:click="addTalentRequirement" class="rounded-lg bg-studio/10 px-3 py-1.5 text-xs font-semibold text-studio transition hover:bg-studio/20">
                    + Add Talent Role
                </button>
            </div>

            @if (empty($talent_requirements))
                <div class="mt-4 rounded-xl border border-dashed border-ink/15 p-6 text-center text-sm text-mist">
                    No specific talent requirements added yet. Click "+ Add Talent Role" if your event needs specific roles (e.g. Photographers, Dancers, Vocalists).
                </div>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($talent_requirements as $index => $req)
                        <div class="flex flex-col gap-3 rounded-xl border border-ink/10 bg-wall/50 p-4 md:flex-row md:items-center" wire:key="talent-req-{{ $index }}">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-mist">Talent Role</label>
                                <select wire:model="talent_requirements.{{ $index }}.talent_id" class="field mt-1 text-sm">
                                    <option value="">Select Talent Type</option>
                                    @foreach ($talents as $talent)
                                        <option value="{{ $talent->id }}">{{ $talent->name }}</option>
                                    @endforeach
                                </select>
                                @error("talent_requirements.{$index}.talent_id") <span class="text-xs text-ember">{{ $message }}</span> @enderror
                            </div>

                            <div class="w-full md:w-28">
                                <label class="block text-xs font-medium text-mist">Spots Needed</label>
                                <input type="number" min="1" wire:model="talent_requirements.{{ $index }}.slots" class="field mt-1 text-sm">
                            </div>

                            <div class="flex-1">
                                <label class="block text-xs font-medium text-mist">Role Notes / Details</label>
                                <input type="text" wire:model="talent_requirements.{{ $index }}.notes" placeholder="e.g. Lead Vocalist, Event Photographer" class="field mt-1 text-sm">
                            </div>

                            <div class="flex items-end pt-5 md:pt-0">
                                <button type="button" wire:click="removeTalentRequirement({{ $index }})" class="rounded-lg p-2 text-ember hover:bg-ember/10" title="Remove role">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Section 4: Campus Contact Info for Selected Candidates --}}
        <div class="rounded-2xl border border-ink/10 bg-white p-6 shadow-sm">
            <h2 class="font-display text-xl">4. Campus Contact & Instructions for Selected Candidates</h2>
            <p class="text-sm text-mist">These details will be revealed to students who are selected/chosen by campus organizers.</p>

            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">
                        Campus Contact Email <span class="text-ember">*</span>
                        <input type="email" wire:model="contact_email" placeholder="campus.events@university.edu" class="field mt-1">
                    </label>
                    @error('contact_email') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink">
                        Campus Contact Phone / WhatsApp
                        <input type="text" wire:model="contact_phone" placeholder="+1 (555) 019-2834" class="field mt-1">
                    </label>
                    @error('contact_phone') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-ink">
                        Contact / Attendance Instructions for Chosen Students
                        <textarea wire:model="contact_instructions" rows="3" placeholder="e.g. Please report to Room 102 by 8:30 AM with your Student ID card. Contact Prof. Davis via WhatsApp prior to the rehearsal." class="field mt-1"></textarea>
                    </label>
                    @error('contact_instructions') <span class="mt-1 block text-xs font-medium text-ember">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 5: Publish Settings & Submission --}}
        <div class="flex items-center justify-between rounded-2xl border border-ink/10 bg-white p-6 shadow-sm">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" wire:model="is_published" class="size-5 rounded border-ink/20 text-ember focus:ring-ember">
                <div>
                    <span class="font-semibold text-ink">Publish Immediately</span>
                    <p class="text-xs text-mist">Make event visible to all students on the campus event feed.</p>
                </div>
            </label>

            <div class="flex gap-3">
                <button type="submit" class="rounded-xl bg-ember px-6 py-3 font-semibold text-white transition hover:bg-ember/90 shadow-sm">
                    Publish Event
                </button>
            </div>
        </div>
    </form>
</div>
