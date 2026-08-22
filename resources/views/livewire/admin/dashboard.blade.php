<div class="flex flex-col min-h-full">

    {{-- Page header --}}
    <div class="border-b border-ink/10 bg-white px-6 py-5">
        <div class="flex items-center justify-between">
            <div>
                @if ($activeTab === 'overview')
                    <h1 class="text-xl font-semibold">Overview</h1>
                    <p class="mt-0.5 text-sm text-mist">Platform statistics and moderation queue.</p>
                @else
                    <h1 class="text-xl font-semibold">Campus Management</h1>
                    <p class="mt-0.5 text-sm text-mist">Review campus admin applications and manage approved campuses.</p>
                @endif
            </div>
            @if ($activeTab === 'campuses' && $pendingCampusAdmins->isNotEmpty())
                <span class="flex items-center gap-1.5 rounded-full bg-ember/10 px-3 py-1 text-xs font-semibold text-ember">
                    <span class="size-2 rounded-full bg-ember"></span>
                    {{ $pendingCampusAdmins->count() }} pending
                </span>
            @endif
        </div>

        {{-- Tab bar (mobile + inline for desktop too) --}}
        <div class="mt-4 flex gap-1 border-b -mb-5 border-transparent">
            <button wire:click="$set('activeTab', 'overview')"
                    class="border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'overview' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Overview
            </button>
            <button wire:click="$set('activeTab', 'campuses')"
                    class="flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'campuses' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Campus Management
                @if ($pendingCampusAdmins->isNotEmpty())
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $pendingCampusAdmins->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 px-6 py-6">

        {{-- ── OVERVIEW TAB ── --}}
        @if ($activeTab === 'overview')

            {{-- Stats grid --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Users</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalUsers }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Students</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalStudents }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Campuses</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalCampusAdmins }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Works</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalItems }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Events</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalEvents }}</p>
                </div>
            </div>

            {{-- Two columns --}}
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                {{-- Talent rooms --}}
                <div class="rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Talent rooms</h2>
                    </div>
                    <ul class="divide-y divide-ink/8">
                        @foreach ($categories as $category)
                            <li class="flex items-center justify-between px-5 py-3 text-sm" wire:key="cat-{{ $category->id }}">
                                <span>{{ $category->name }}</span>
                                <span class="font-semibold text-ember">{{ $category->published_items_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Reports queue --}}
                <div class="rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Reports queue</h2>
                    </div>
                    <ul class="divide-y divide-ink/8">
                        @forelse ($reports as $report)
                            <li class="px-5 py-4 text-sm" wire:key="rep-{{ $report->id }}">
                                <p><span class="font-medium">{{ $report->reporter->name }}</span> <span class="text-mist">· {{ $report->reason }}</span></p>
                                @if ($report->details)
                                    <p class="mt-0.5 text-mist">{{ Str::limit($report->details, 80) }}</p>
                                @endif
                                <div class="mt-3 flex gap-2">
                                    <button wire:click="moderate({{ $report->id }}, 'dismissed')"
                                            class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                        Dismiss
                                    </button>
                                    <button wire:click="moderate({{ $report->id }}, 'actioned')"
                                            class="rounded-lg bg-ember px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-ember/90">
                                        Take down
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-mist">Queue is clear ✓</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        {{-- ── CAMPUS MANAGEMENT TAB ── --}}
        @else

            {{-- Pending applications --}}
            <div class="rounded-2xl border border-ink/8 bg-white">
                <div class="flex items-center justify-between border-b border-ink/8 px-5 py-4">
                    <div>
                        <h2 class="font-semibold">Pending applications</h2>
                        <p class="text-sm text-mist">Campus admin accounts waiting for approval</p>
                    </div>
                    @if ($pendingCampusAdmins->isNotEmpty())
                        <span class="rounded-full bg-ember px-2.5 py-0.5 text-xs font-semibold text-white">{{ $pendingCampusAdmins->count() }}</span>
                    @endif
                </div>

                @if ($pendingCampusAdmins->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No pending applications.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($pendingCampusAdmins as $applicant)
                            <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-4" wire:key="pending-{{ $applicant->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 items-center justify-center rounded-full bg-ember/10 text-sm font-semibold text-ember">
                                        {{ $applicant->initials() }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $applicant->name }}</p>
                                        <p class="text-sm text-mist">{{ $applicant->email }}</p>
                                        <p class="text-xs text-mist">Applied {{ $applicant->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="rejectCampusAdmin({{ $applicant->id }})"
                                            class="rounded-lg border border-ink/15 px-4 py-2 text-sm font-medium transition hover:bg-ink/5">
                                        Reject
                                    </button>
                                    <button wire:click="approveCampusAdmin({{ $applicant->id }})"
                                            class="rounded-lg bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90">
                                        Approve
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Approved campuses --}}
            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Approved campuses</h2>
                    <p class="text-sm text-mist">Active campus admin accounts</p>
                </div>

                @if ($approvedCampusAdmins->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No approved campus admins yet.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($approvedCampusAdmins as $campus)
                            <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-4" wire:key="campus-{{ $campus->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 items-center justify-center rounded-full bg-studio text-sm font-semibold text-gold">
                                        {{ $campus->initials() }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $campus->name }}</p>
                                        <p class="text-sm text-mist">{{ $campus->email }}</p>
                                        <p class="text-xs text-mist">Joined {{ $campus->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                @unless ($campus->is(auth()->user()))
                                    <div class="flex gap-2">
                                        <button wire:click="assignRole({{ $campus->id }}, 'student')"
                                                class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                            Demote to student
                                        </button>
                                    </div>
                                @endunless
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        @endif
    </div>
</div>
