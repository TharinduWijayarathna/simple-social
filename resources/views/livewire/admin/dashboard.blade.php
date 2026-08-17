<div class="px-4 py-8 lg:px-10">
    <p class="text-xs uppercase tracking-[0.28em] text-gold">Platform</p>
    <h1 class="font-display text-4xl">Super admin</h1>
    <p class="mt-2 text-mist">Campus-wide moderation, talent stats, and staff roles.</p>

    <dl class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <div class="rounded-2xl bg-studio p-4 text-paper"><dt class="text-xs text-gold">Users</dt><dd class="text-2xl font-semibold">{{ $users }}</dd></div>
        <div class="rounded-2xl bg-studio p-4 text-paper"><dt class="text-xs text-gold">Students</dt><dd class="text-2xl font-semibold">{{ $students }}</dd></div>
        <div class="rounded-2xl bg-studio p-4 text-paper"><dt class="text-xs text-gold">Campus admins</dt><dd class="text-2xl font-semibold">{{ $campusAdmins }}</dd></div>
        <div class="rounded-2xl bg-studio p-4 text-paper"><dt class="text-xs text-gold">Works</dt><dd class="text-2xl font-semibold">{{ $items }}</dd></div>
        <div class="rounded-2xl bg-studio p-4 text-paper"><dt class="text-xs text-gold">Events</dt><dd class="text-2xl font-semibold">{{ $events }}</dd></div>
    </dl>

    <div class="mt-10 grid gap-8 lg:grid-cols-2">
        <section>
            <h2 class="font-display text-2xl">Talent rooms</h2>
            <ul class="mt-3 grid gap-2">
                @foreach ($categories as $category)
                    <li class="flex justify-between rounded-xl bg-white px-4 py-3 text-sm" wire:key="cat-{{ $category->id }}">
                        <span>{{ $category->name }}</span>
                        <span class="text-ember">{{ $category->published_items_count }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2 class="font-display text-2xl">People & roles</h2>
            <ul class="mt-3 flex flex-col gap-2">
                @foreach ($staff as $member)
                    <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-white px-4 py-3 text-sm" wire:key="staff-{{ $member->id }}">
                        <span>
                            <span class="font-medium">{{ $member->name }}</span>
                            <span class="text-mist"> · {{ $member->role->label() }}</span>
                        </span>
                        @unless ($member->is(auth()->user()))
                            <span class="flex gap-2">
                                <button type="button" wire:click="assignRole({{ $member->id }}, 'student')" class="btn-ghost px-3 py-1">Student</button>
                                <button type="button" wire:click="assignRole({{ $member->id }}, 'campus_admin')" class="btn-ghost px-3 py-1">Campus</button>
                            </span>
                        @endunless
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <h2 class="mt-10 font-display text-2xl">Reports queue</h2>
    <ul class="mt-3 flex flex-col gap-3">
        @forelse ($reports as $report)
            <li class="rounded-2xl border border-ink/10 bg-white p-4 text-sm" wire:key="rep-{{ $report->id }}">
                <p><span class="font-medium">{{ $report->reporter->name }}</span> · {{ $report->reason }}</p>
                <p class="mt-1 text-mist">{{ $report->details }}</p>
                <div class="mt-3 flex gap-2">
                    <button wire:click="moderate({{ $report->id }}, 'dismissed')" class="btn-ghost">Dismiss</button>
                    <button wire:click="moderate({{ $report->id }}, 'actioned')" class="btn-primary">Take down</button>
                </div>
            </li>
        @empty
            <li class="text-mist">Queue is clear.</li>
        @endforelse
    </ul>
</div>
