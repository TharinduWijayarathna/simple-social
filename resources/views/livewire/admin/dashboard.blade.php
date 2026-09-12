<div class="flex flex-col min-h-full">

    {{-- Page header --}}
    <div class="border-b border-ink/10 bg-white px-6 py-5">
        <div class="flex items-center justify-between">
            <div>
                @switch($activeTab)
                    @case('overview')
                        <h1 class="text-xl font-semibold">Overview</h1>
                        <p class="mt-0.5 text-sm text-mist">Platform statistics and moderation queue.</p>
                        @break
                    @case('campuses')
                        <h1 class="text-xl font-semibold">Campus Management</h1>
                        <p class="mt-0.5 text-sm text-mist">Review campus registrations and manage approved campuses.</p>
                        @break
                    @case('users')
                        <h1 class="text-xl font-semibold">User Management</h1>
                        <p class="mt-0.5 text-sm text-mist">Search users, change roles, suspend or remove accounts.</p>
                        @break
                    @case('moderation')
                        <h1 class="text-xl font-semibold">Content Moderation</h1>
                        <p class="mt-0.5 text-sm text-mist">Investigate reports, document decisions, and manage content across every campus.</p>
                        @break
                    @case('settings')
                        <h1 class="text-xl font-semibold">Site Settings</h1>
                        <p class="mt-0.5 text-sm text-mist">Manage platform-wide configuration.</p>
                        @break
                    @case('analytics')
                        <h1 class="text-xl font-semibold">Analytics</h1>
                        <p class="mt-0.5 text-sm text-mist">Growth and engagement across the platform.</p>
                        @break
                @endswitch
            </div>
            @if ($activeTab === 'campuses' && $pendingCampuses->isNotEmpty())
                <span class="flex items-center gap-1.5 rounded-full bg-ember/10 px-3 py-1 text-xs font-semibold text-ember">
                    <span class="size-2 rounded-full bg-ember"></span>
                    {{ $pendingCampuses->count() }} pending
                </span>
            @endif
        </div>

        {{-- Tab bar (mobile + inline for desktop too) --}}
        <div class="mt-4 flex gap-1 overflow-x-auto border-b -mb-5 border-transparent">
            <button wire:click="$set('activeTab', 'overview')"
                    class="shrink-0 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'overview' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Overview
            </button>
            <button wire:click="$set('activeTab', 'campuses')"
                    class="shrink-0 flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'campuses' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Campus Management
                @if ($pendingCampuses->isNotEmpty())
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $pendingCampuses->count() }}</span>
                @endif
            </button>
            <button wire:click="$set('activeTab', 'users')"
                    class="shrink-0 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'users' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Users
            </button>
            <button wire:click="$set('activeTab', 'moderation')"
                    class="shrink-0 flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'moderation' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Moderation
                @if ($pendingReportsCount > 0)
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $pendingReportsCount }}</span>
                @endif
            </button>
            <button wire:click="$set('activeTab', 'analytics')"
                    class="shrink-0 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'analytics' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Analytics
            </button>
            <button wire:click="$set('activeTab', 'settings')"
                    class="shrink-0 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'settings' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Settings
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
                    <p class="mt-2 text-3xl font-semibold">{{ $totalCampuses }}</p>
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
                        @forelse ($overviewReports as $report)
                            <li class="px-5 py-4 text-sm" wire:key="rep-{{ $report->id }}">
                                <p><span class="font-medium">{{ $report->reporter->name }}</span> <span class="text-mist">· {{ $report->reason }}</span></p>
                                @if ($report->details)
                                    <p class="mt-0.5 text-mist">{{ Str::limit($report->details, 80) }}</p>
                                @endif
                                <div class="mt-3 flex gap-2">
                                    <button wire:click="moderateReport({{ $report->id }}, 'dismissed')"
                                            class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                        Dismiss
                                    </button>
                                    <button wire:click="moderateReport({{ $report->id }}, 'actioned')"
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

        {{-- ── CAMPUSES TAB ── --}}
        @elseif ($activeTab === 'campuses')
            @if (session('campus-status'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('campus-status') }}</div>
            @endif

            <div class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach (['all' => 'All campuses', 'pending' => 'Pending', 'approved' => 'Active', 'banned' => 'On hold'] as $status => $label)
                    <button wire:click="$set('campusStatus', '{{ $status }}')" class="rounded-2xl border p-4 text-left transition {{ $campusStatus === $status ? 'border-ember bg-ember/5' : 'border-ink/8 bg-white hover:border-ink/20' }}">
                        <p class="text-xs font-semibold uppercase tracking-wider text-mist">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $status === 'all' ? $campusOptions->count() : $campusOptions->filter(fn ($campusOption) => $campusOption->status->value === $status)->count() }}</p>
                    </button>
                @endforeach
            </div>

            <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-ink/8 bg-white p-4 sm:flex-row">
                <input type="search" wire:model.live.debounce.350ms="campusSearch" placeholder="Search campus, contact, email or address…" class="min-w-0 flex-1 rounded-xl border border-ink/15 px-3 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
                <select wire:model.live="campusStatus" class="rounded-xl border border-ink/15 px-3 py-2.5 text-sm">
                    <option value="all">Every status</option><option value="pending">Pending</option><option value="approved">Active</option><option value="rejected">Rejected</option><option value="banned">On hold</option>
                </select>
            </div>

            <div class="space-y-4">
                @forelse ($campuses as $campus)
                    <article x-data="{ expanded: false }" class="overflow-hidden rounded-2xl border border-ink/8 bg-white shadow-sm" wire:key="campus-{{ $campus->id }}">
                        <div class="flex flex-col gap-5 p-5 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex min-w-0 items-center gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-studio font-semibold text-gold">{{ $campus->initials() }}</div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2"><h2 class="font-semibold">{{ $campus->displayCampusName() }}</h2><span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $campus->status === \App\Enums\UserStatus::Approved ? 'bg-emerald-100 text-emerald-700' : ($campus->status === \App\Enums\UserStatus::Pending ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $campus->status === \App\Enums\UserStatus::Banned ? 'On hold' : $campus->status->label() }}</span></div>
                                    <p class="truncate text-sm text-mist">{{ $campus->email }} · Contact: {{ $campus->name }}</p>
                                    <p class="mt-1 text-xs text-mist">Registered {{ $campus->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-5 text-center lg:min-w-72">
                                <div><p class="text-xl font-semibold">{{ $campus->students_count }}</p><p class="text-xs text-mist">Students</p></div>
                                <div><p class="text-xl font-semibold">{{ $campus->published_items_count }}</p><p class="text-xs text-mist">Works</p></div>
                                <div><p class="text-xl font-semibold">{{ $campus->events_count }}</p><p class="text-xs text-mist">Events</p></div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button x-on:click="expanded = ! expanded" class="rounded-xl border border-ink/15 px-3 py-2 text-xs font-semibold" x-text="expanded ? 'Hide details' : 'View details'"></button>
                                @if ($campus->status === \App\Enums\UserStatus::Pending)
                                    <button wire:click="rejectCampus({{ $campus->id }})" wire:confirm="Reject this campus application?" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Reject</button>
                                    <button wire:click="approveCampus({{ $campus->id }})" class="rounded-xl bg-ember px-3 py-2 text-xs font-semibold text-white">Approve</button>
                                @elseif ($campus->status === \App\Enums\UserStatus::Approved)
                                    <button wire:click="holdCampus({{ $campus->id }})" wire:confirm="Place this campus on hold? Campus access will be suspended." class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-semibold text-white">Place on hold</button>
                                @elseif ($campus->status === \App\Enums\UserStatus::Banned)
                                    <button wire:click="reactivateCampus({{ $campus->id }})" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Reactivate</button>
                                @endif
                            </div>
                        </div>
                        <div x-show="expanded" x-collapse class="border-t border-ink/8 bg-canvas/60 px-5 py-5">
                            <dl class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4"><div><dt class="text-xs text-mist">Phone</dt><dd class="mt-1 font-medium">{{ $campus->campus_phone ?: 'Not provided' }}</dd></div><div><dt class="text-xs text-mist">Address</dt><dd class="mt-1 font-medium">{{ $campus->campus_address ?: 'Not provided' }}</dd></div><div><dt class="text-xs text-mist">Website</dt><dd class="mt-1 font-medium">@if ($campus->campus_website)<a class="text-ember hover:underline" target="_blank" rel="noopener" href="{{ $campus->campus_website }}">Open website</a>@else Not provided @endif</dd></div><div><dt class="text-xs text-mist">Pending students</dt><dd class="mt-1 font-medium">{{ $campus->pending_students_count }}</dd></div></dl>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-ink/15 bg-white px-6 py-14 text-center text-sm text-mist">No campuses match these filters.</div>
                @endforelse
            </div>
            <div class="mt-5">{{ $campuses->links() }}</div>

        {{-- ── USERS TAB ── --}}
        @elseif ($activeTab === 'users')

            <div class="mb-5 grid gap-3 rounded-2xl border border-ink/8 bg-white p-4 md:grid-cols-4">
                <input type="search" wire:model.live.debounce.350ms="userSearch" placeholder="Name, email or student ID…" class="rounded-xl border border-ink/15 px-3 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
                <select wire:model.live="userCampus" class="rounded-xl border border-ink/15 px-3 py-2.5 text-sm">
                    <option value="all">All campuses</option>
                    @foreach ($campusOptions as $campusOption)<option value="{{ $campusOption->id }}">{{ $campusOption->displayCampusName() }} ({{ $campusOption->students_count }})</option>@endforeach
                    <option value="unassigned">Without a campus</option>
                </select>
                <select wire:model.live="userRole" class="rounded-xl border border-ink/15 px-3 py-2.5 text-sm"><option value="all">All roles</option>@foreach (\App\Enums\Role::cases() as $role)<option value="{{ $role->value }}">{{ $role->label() }}</option>@endforeach</select>
                <select wire:model.live="userStatus" class="rounded-xl border border-ink/15 px-3 py-2.5 text-sm"><option value="all">All statuses</option>@foreach (\App\Enums\UserStatus::cases() as $status)<option value="{{ $status->value }}">{{ $status->label() }}</option>@endforeach</select>
            </div>

            <div class="space-y-5">
                @forelse ($usersByCampus as $campusName => $campusUsers)
                    <section class="overflow-hidden rounded-2xl border border-ink/8 bg-white" wire:key="user-group-{{ md5($campusName) }}">
                        <div class="flex items-center justify-between border-b border-ink/8 bg-canvas/50 px-5 py-3"><div><h2 class="font-semibold">{{ $campusName }}</h2><p class="text-xs text-mist">Users in this category</p></div><span class="rounded-full bg-ink px-2.5 py-1 text-xs font-bold text-white">{{ $campusUsers->count() }}</span></div>
                        <ul class="divide-y divide-ink/8">
                    @foreach ($campusUsers as $user)
                        <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-4" wire:key="user-{{ $user->id }}">
                            <div class="flex items-center gap-3">
                                <div class="flex size-10 items-center justify-center rounded-full bg-studio text-sm font-semibold text-gold">
                                    {{ $user->initials() }}
                                </div>
                                <div>
                                    <p class="font-medium">
                                        {{ $user->name }}
                                        @if ($user->status->value === 'banned')
                                            <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-red-700">Banned</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-mist">{{ $user->email }}</p>
                                    <p class="text-xs text-mist">{{ $user->role->label() }}@if ($user->university_id) · ID {{ $user->university_id }}@endif · Joined {{ $user->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            @unless ($user->is(auth()->user()))
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($user->isCampus())
                                        <span class="rounded-lg bg-ink/5 px-2 py-1.5 text-xs font-medium">Campus</span>
                                    @else
                                        <select wire:change="assignRole({{ $user->id }}, $event.target.value)"
                                                class="rounded-lg border border-ink/15 px-2 py-1.5 text-xs">
                                            @foreach (\App\Enums\Role::cases() as $role)
                                                @continue($role === \App\Enums\Role::Campus)
                                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @if ($user->status->value === 'banned')
                                        <button wire:click="unbanUser({{ $user->id }})"
                                                class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                            Unban
                                        </button>
                                    @else
                                        <button wire:click="banUser({{ $user->id }})"
                                                class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                            Suspend
                                        </button>
                                    @endif
                                    <button wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Permanently delete this user? This cannot be undone."
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700">
                                        Delete
                                    </button>
                                </div>
                            @endunless
                        </li>
                    @endforeach
                        </ul>
                    </section>
                @empty
                    <div class="rounded-2xl border border-dashed border-ink/15 bg-white px-5 py-12 text-center text-sm text-mist">No users match these filters.</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $users->links() }}</div>

        {{-- ── MODERATION TAB ── --}}
        @elseif ($activeTab === 'moderation')

            @if (session('moderation-status'))
                <div class="mb-5 flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <span>{{ session('moderation-status') }}</span>
                    <span class="font-bold">✓</span>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                <div class="rounded-2xl border border-ink/8 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-mist">Needs review</p>
                        <span class="flex size-8 items-center justify-center rounded-full bg-red-50 text-sm font-bold text-red-600">!</span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold {{ $pendingReportsCount > 0 ? 'text-ember' : 'text-ink' }}">{{ $pendingReportsCount }}</p>
                    <p class="mt-1 text-xs text-mist">Open reports across the platform</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-mist">Resolved</p>
                        <span class="flex size-8 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">✓</span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold">{{ $resolvedReportsLast30Days }}</p>
                    <p class="mt-1 text-xs text-mist">Decisions in the last 30 days</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-mist">Live content</p>
                        <span class="flex size-8 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-700">↗</span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold">{{ $publishedItemsCount }}</p>
                    <p class="mt-1 text-xs text-mist">Published work across all campuses</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-mist">Removed</p>
                        <span class="flex size-8 items-center justify-center rounded-full bg-amber-50 text-sm font-bold text-amber-700">↓</span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold">{{ $removedItemsCount }}</p>
                    <p class="mt-1 text-xs text-mist">Restorable moderated work</p>
                </div>
            </div>

            <section class="mt-6 overflow-hidden rounded-2xl border border-ink/8 bg-white shadow-sm">
                <div class="border-b border-ink/8 px-5 py-4">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-semibold">Reports workspace</h2>
                                <span class="rounded-full bg-wall px-2 py-0.5 text-[11px] font-semibold text-mist">{{ $reports->total() }} shown</span>
                            </div>
                            <p class="mt-1 text-xs text-mist">Review the reported work and record why you made each decision.</p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <label class="relative min-w-64">
                                <span class="sr-only">Search reports</span>
                                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-mist" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="m20 20-3.5-3.5"></path>
                                </svg>
                                <input wire:model.live.debounce.300ms="moderationSearch" type="search" placeholder="Search reporter, creator, or reason…" class="field w-full pl-9 text-sm">
                            </label>
                            <label>
                                <span class="sr-only">Filter report status</span>
                                <select wire:model.live="moderationStatus" class="field min-w-40 text-sm">
                                    <option value="pending">Needs review</option>
                                    <option value="all">All reports</option>
                                    <option value="reviewed">Reviewed</option>
                                    <option value="dismissed">Dismissed</option>
                                    <option value="actioned">Action taken</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                @if (count($selectedReportIds) > 0)
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-200 bg-amber-50 px-5 py-3">
                        <p class="text-sm font-semibold text-amber-900">{{ count($selectedReportIds) }} report{{ count($selectedReportIds) === 1 ? '' : 's' }} selected</p>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="bulkModerateReports('reviewed')" wire:loading.attr="disabled" class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-900 transition hover:bg-amber-100 disabled:opacity-50">Mark reviewed</button>
                            <button wire:click="bulkModerateReports('dismissed')" wire:loading.attr="disabled" class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-900 transition hover:bg-amber-100 disabled:opacity-50">Dismiss</button>
                            <button wire:click="bulkModerateReports('actioned')" wire:confirm="Take down the content attached to every selected report?" wire:loading.attr="disabled" class="rounded-lg bg-ember px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-ember/90 disabled:opacity-50">Take down content</button>
                        </div>
                    </div>
                @endif
                @error('selectedReportIds')
                    <p class="border-b border-red-100 bg-red-50 px-5 py-2 text-xs font-semibold text-red-700">{{ $message }}</p>
                @enderror

                <div class="divide-y divide-ink/8">
                    @forelse ($reports as $report)
                        @php
                            $reportedItem = $report->reportable instanceof \App\Models\PortfolioItem ? $report->reportable : null;
                            $statusClasses = match ($report->status) {
                                \App\Enums\ReportStatus::Pending => 'bg-red-50 text-red-700 ring-red-600/10',
                                \App\Enums\ReportStatus::Reviewed => 'bg-blue-50 text-blue-700 ring-blue-600/10',
                                \App\Enums\ReportStatus::Dismissed => 'bg-slate-100 text-slate-600 ring-slate-500/10',
                                \App\Enums\ReportStatus::Actioned => 'bg-amber-50 text-amber-800 ring-amber-600/10',
                            };
                        @endphp
                        <article class="p-5" wire:key="admin-mod-report-{{ $report->id }}">
                            <div class="flex items-start gap-4">
                                <label class="mt-1 flex shrink-0 cursor-pointer items-center">
                                    <input type="checkbox" value="{{ $report->id }}" wire:model.live="selectedReportIds" class="size-4 rounded border-ink/20 text-ember focus:ring-ember">
                                    <span class="sr-only">Select report {{ $report->id }}</span>
                                </label>

                                @if ($reportedItem)
                                    <div class="relative size-20 shrink-0 overflow-hidden rounded-xl bg-wall sm:size-24">
                                        <img src="{{ $reportedItem->displayUrl() }}" alt="" class="size-full object-cover">
                                        <span class="absolute bottom-1.5 left-1.5 rounded-md bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-white uppercase">{{ $reportedItem->media_type->value }}</span>
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $statusClasses }}">{{ $report->status === \App\Enums\ReportStatus::Pending ? 'Needs review' : Str::headline($report->status->value) }}</span>
                                                <span class="text-xs text-mist">Case #{{ $report->id }} · {{ $report->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h3 class="mt-2 truncate font-semibold text-ink">{{ $reportedItem?->title ?? 'Unavailable content' }}</h3>
                                            <p class="mt-0.5 text-xs text-mist">
                                                {{ Str::headline($report->reason) }}
                                                @if ($reportedItem?->user)
                                                    · created by <span class="font-medium text-ink">{{ $reportedItem->user->name }}</span>
                                                    · {{ $reportedItem->user->campus?->displayCampusName() ?? 'No campus assigned' }}
                                                @endif
                                            </p>
                                        </div>
                                        @if ($reportedItem)
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $reportedItem->isPublished() ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $reportedItem->isPublished() ? 'Currently live' : 'Not published' }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                        <div class="rounded-xl bg-wall/70 p-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-mist">Reporter statement</p>
                                            <p class="mt-1.5 text-sm leading-relaxed text-ink">{{ $report->details ?: 'No additional details were supplied.' }}</p>
                                            <p class="mt-2 text-xs text-mist">Reported by <span class="font-semibold text-ink">{{ $report->reporter?->name ?? 'Deleted account' }}</span></p>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-mist" for="moderator-note-{{ $report->id }}">Private moderator note</label>
                                            <textarea id="moderator-note-{{ $report->id }}" wire:model="moderatorNotes.{{ $report->id }}" rows="3" maxlength="2000" placeholder="{{ $report->moderator_notes ?: 'Add context for this decision…' }}" class="field mt-1.5 w-full resize-none text-sm"></textarea>
                                            @error("moderatorNotes.{$report->id}") <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex flex-wrap gap-2">
                                            @if ($reportedItem)
                                                <a href="{{ $reportedItem->fileUrl() }}" target="_blank" rel="noopener" class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-semibold text-ink transition hover:bg-wall">Open original ↗</a>
                                            @endif
                                            <button wire:click="saveModeratorNote({{ $report->id }})" wire:loading.attr="disabled" class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-semibold text-ink transition hover:bg-wall disabled:opacity-50">Save note</button>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @if ($report->status === \App\Enums\ReportStatus::Pending)
                                                <button wire:click="moderateReport({{ $report->id }}, 'reviewed')" wire:loading.attr="disabled" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 disabled:opacity-50">Keep & review</button>
                                                <button wire:click="moderateReport({{ $report->id }}, 'dismissed')" wire:confirm="Dismiss this report as not actionable?" wire:loading.attr="disabled" class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-semibold text-mist transition hover:bg-wall disabled:opacity-50">Dismiss</button>
                                                <button wire:click="moderateReport({{ $report->id }}, 'actioned')" wire:confirm="Take down this work and resolve related reports?" wire:loading.attr="disabled" class="rounded-lg bg-ember px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-ember/90 disabled:opacity-50">Take down</button>
                                            @else
                                                <button wire:click="moderateReport({{ $report->id }}, 'pending')" wire:loading.attr="disabled" class="rounded-lg border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-800 transition hover:bg-amber-50 disabled:opacity-50">Reopen case</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="flex flex-col items-center px-5 py-14 text-center">
                            <span class="flex size-12 items-center justify-center rounded-full bg-emerald-50 text-xl text-emerald-700">✓</span>
                            <h3 class="mt-3 font-semibold">No matching reports</h3>
                            <p class="mt-1 max-w-sm text-sm text-mist">The platform queue is clear for this filter. Try another status or search term to review moderation history.</p>
                        </div>
                    @endforelse
                </div>

                @if ($reports->hasPages())
                    <div class="border-t border-ink/8 px-5 py-4">{{ $reports->links(data: ['scrollTo' => false]) }}</div>
                @endif
            </section>

            <section class="mt-6 rounded-2xl border border-ink/8 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h2 class="font-semibold">Content review library</h2>
                        <p class="mt-1 text-xs text-mist">Proactively inspect published work or restore content removed by moderation.</p>
                    </div>
                    <div class="grid gap-2 sm:grid-cols-3">
                        <label>
                            <span class="sr-only">Search content</span>
                            <input wire:model.live.debounce.300ms="moderationContentSearch" type="search" placeholder="Search work or creator…" class="field w-full text-sm">
                        </label>
                        <label>
                            <span class="sr-only">Filter publication state</span>
                            <select wire:model.live="moderationContentState" class="field w-full text-sm">
                                <option value="published">Published</option>
                                <option value="removed">Removed</option>
                                <option value="all">Published & removed</option>
                            </select>
                        </label>
                        <label>
                            <span class="sr-only">Filter media type</span>
                            <select wire:model.live="moderationContentType" class="field w-full text-sm">
                                <option value="all">All media</option>
                                <option value="image">Images</option>
                                <option value="video">Videos</option>
                                <option value="audio">Audio</option>
                                <option value="document">Documents</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($moderationItems as $item)
                        <article class="overflow-hidden rounded-2xl border border-ink/10 bg-white" wire:key="admin-moderation-item-{{ $item->id }}">
                            <div class="relative aspect-[16/9] overflow-hidden bg-wall">
                                <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="size-full object-cover transition duration-300 hover:scale-[1.02]">
                                <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
                                    <span class="rounded-lg bg-black/70 px-2 py-1 text-[10px] font-bold text-white uppercase tracking-wide">{{ $item->media_type->value }}</span>
                                    <span class="rounded-lg px-2 py-1 text-[10px] font-bold uppercase tracking-wide {{ $item->isPublished() ? 'bg-emerald-600 text-white' : 'bg-amber-100 text-amber-900' }}">{{ $item->isPublished() ? 'Live' : 'Removed' }}</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-wall text-xs font-bold text-ink">
                                        @if ($item->user->avatarUrl())
                                            <img src="{{ $item->user->avatarUrl() }}" alt="" class="size-full object-cover">
                                        @else
                                            {{ $item->user->initials() }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-semibold text-ink">{{ $item->title }}</h3>
                                        <p class="truncate text-xs text-mist">{{ $item->user->name }} · {{ $item->user->campus?->displayCampusName() ?? 'No campus' }} · {{ $item->talent?->name ?? 'Uncategorized' }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] text-mist">
                                    <span>{{ $item->isPublished() ? 'Published '.$item->published_at->diffForHumans() : 'Removed '.$item->updated_at->diffForHumans() }}</span>
                                    <span>·</span>
                                    <span class="{{ $item->pending_reports_count > 0 ? 'font-semibold text-ember' : '' }}">{{ $item->reports_count }} report{{ $item->reports_count === 1 ? '' : 's' }}</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-2">
                                    <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" class="text-xs font-semibold text-ink underline-offset-2 hover:text-ember hover:underline">Inspect original ↗</a>
                                    @if ($item->isPublished())
                                        <button wire:click="openContentReview({{ $item->id }})" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50">Review & remove</button>
                                    @else
                                        <button wire:click="republishItem({{ $item->id }})" wire:confirm="Publish this moderated work again?" class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">Restore</button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-ink/15 bg-wall/40 px-5 py-12 text-center">
                            <h3 class="font-semibold">No matching content</h3>
                            <p class="mt-1 text-sm text-mist">Adjust the publication state, media type, or search.</p>
                        </div>
                    @endforelse
                </div>

                @if ($moderationItems->hasPages())
                    <div class="mt-5 border-t border-ink/8 pt-4">{{ $moderationItems->links(data: ['scrollTo' => false]) }}</div>
                @endif
            </section>

            @if ($contentUnderReviewId !== null)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/55 p-4 backdrop-blur-sm">
                    <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="content-review-title">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-ember">Proactive moderation</p>
                                <h2 id="content-review-title" class="mt-1 font-display text-2xl">Remove this work?</h2>
                            </div>
                            <button type="button" wire:click="closeContentReview" class="flex size-8 items-center justify-center rounded-full bg-wall text-lg text-mist transition hover:text-ink" aria-label="Close">×</button>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-mist">The work will disappear from student feeds across the platform. Your explanation will be stored in moderation history, and the work can be restored later.</p>
                        <form wire:submit="unpublishItem" class="mt-5 space-y-4">
                            <div>
                                <label for="content-moderation-reason" class="text-xs font-semibold text-ink">Reason for removal</label>
                                <textarea id="content-moderation-reason" wire:model="contentModerationReason" rows="4" maxlength="1000" placeholder="Explain the policy concern or safety issue…" class="field mt-1.5 w-full resize-none" required></textarea>
                                @error('contentModerationReason') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" wire:click="closeContentReview" class="rounded-xl border border-ink/15 px-4 py-2 text-sm font-semibold text-ink transition hover:bg-wall">Cancel</button>
                                <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90 disabled:opacity-50">Unpublish & record</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        {{-- ── ANALYTICS TAB ── --}}
        @elseif ($activeTab === 'analytics')

            <div class="grid grid-cols-2 gap-4 xl:grid-cols-6">
                @foreach ([['Users', $totalUsers, '+'.$newUsersLast30Days.' this month'], ['Campuses', $totalCampuses, 'active institutions'], ['Published work', $totalItems, 'student creations'], ['Events', $totalEvents, 'published'], ['Engagements', $totalLikes + $totalComments + $totalFollows, 'likes, comments & follows'], ['Open reports', $pendingReportsCount, 'need review']] as [$label, $value, $detail])
                    <div class="rounded-2xl border border-ink/8 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-mist">{{ $label }}</p><p class="mt-3 text-3xl font-semibold">{{ number_format($value) }}</p><p class="mt-1 text-xs text-mist">{{ $detail }}</p></div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-5">
                <section class="rounded-2xl border border-ink/8 bg-white p-5 xl:col-span-3">
                    <div><h2 class="font-semibold">Registration growth</h2><p class="text-sm text-mist">New accounts during the last six calendar months</p></div>
                    <div class="mt-8 flex h-56 items-end gap-3 border-b border-ink/10 pb-2">
                        @foreach ($registrationTrend as $month)
                            <div class="flex h-full flex-1 flex-col justify-end text-center" wire:key="growth-{{ $month['label'] }}"><span class="mb-2 text-xs font-semibold">{{ $month['count'] }}</span><div class="mx-auto w-full max-w-14 rounded-t-lg bg-ember/80" style="height: {{ max(4, round(($month['count'] / $registrationTrendMax) * 100)) }}%"></div><span class="mt-2 text-xs text-mist">{{ $month['label'] }}</span></div>
                        @endforeach
                    </div>
                </section>
                <section class="overflow-hidden rounded-2xl border border-ink/8 bg-white xl:col-span-2">
                    <div class="border-b border-ink/8 px-5 py-4"><h2 class="font-semibold">Engagement mix</h2><p class="text-sm text-mist">Platform-wide social activity</p></div>
                    <dl class="divide-y divide-ink/8">@foreach ([['Likes', $totalLikes, 'text-ember'], ['Comments', $totalComments, 'text-blue-600'], ['Follows', $totalFollows, 'text-emerald-600']] as [$label, $value, $color])<div class="flex items-center justify-between px-5 py-4"><dt class="text-sm text-mist">{{ $label }}</dt><dd class="text-xl font-semibold {{ $color }}">{{ number_format($value) }}</dd></div>@endforeach</dl>
                </section>
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-2">
                <section class="overflow-hidden rounded-2xl border border-ink/8 bg-white"><div class="border-b border-ink/8 px-5 py-4"><h2 class="font-semibold">Campus performance</h2><p class="text-sm text-mist">Ranked by published student work</p></div><div class="divide-y divide-ink/8">@forelse ($topCampuses as $index => $campus)<div class="flex items-center gap-4 px-5 py-4"><span class="flex size-8 items-center justify-center rounded-full bg-studio text-xs font-bold text-gold">{{ $index + 1 }}</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">{{ $campus->displayCampusName() }}</p><p class="text-xs text-mist">{{ $campus->students_count }} students · {{ $campus->events_count }} events</p></div><p class="text-right text-lg font-semibold text-ember">{{ $campus->published_items_count }}<span class="block text-[10px] font-normal text-mist">works</span></p></div>@empty<div class="p-8 text-center text-sm text-mist">No campus activity yet.</div>@endforelse</div></section>
                <section class="overflow-hidden rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4"><h2 class="font-semibold">Talent room activity</h2><p class="text-sm text-mist">Published work by talent</p></div>
                    <ul class="divide-y divide-ink/8">
                        @foreach ($categories->take(8) as $category)
                            <li class="flex items-center gap-4 px-5 py-3 text-sm" wire:key="an-cat-{{ $category->id }}"><span class="min-w-0 flex-1 truncate">{{ $category->name }}</span><div class="h-2 w-24 overflow-hidden rounded-full bg-ink/8"><div class="h-full rounded-full bg-gold" style="width: {{ $categories->max('published_items_count') > 0 ? max(4, round(($category->published_items_count / $categories->max('published_items_count')) * 100)) : 0 }}%"></div></div><span class="w-8 text-right font-semibold text-ember">{{ $category->published_items_count }}</span></li>
                        @endforeach
                    </ul>
                </section>
            </div>

        {{-- ── SETTINGS TAB ── --}}
        @elseif ($activeTab === 'settings')

            @if (session('settings-status'))<div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('settings-status') }}</div>@endif
            <div class="grid items-start gap-6 xl:grid-cols-2">
                <section class="overflow-hidden rounded-2xl border border-ink/8 bg-white"><div class="border-b border-ink/8 px-5 py-4"><h2 class="font-semibold">Platform configuration</h2><p class="text-sm text-mist">Identity, support contact, and availability controls.</p></div><form wire:submit="saveGeneralSettings" class="space-y-5 p-5"><div class="grid gap-4 sm:grid-cols-2"><label class="text-sm font-medium">Platform name<input wire:model="siteName" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label><label class="text-sm font-medium">Support email<input type="email" wire:model="supportEmail" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label></div><div class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><label class="flex items-start gap-3"><input type="checkbox" wire:model="siteUnderConstruction" class="mt-1 rounded border-ink/20"><span><span class="block text-sm font-semibold text-amber-900">Under-construction mode</span><span class="block text-xs leading-5 text-amber-800">Visitors and students receive a polished 503 page. Super admins retain access.</span></span></label><div class="mt-4 space-y-3"><input wire:model="underConstructionTitle" placeholder="Page title" class="w-full rounded-xl border border-amber-200 px-3 py-2.5 text-sm"><textarea wire:model="underConstructionMessage" rows="3" placeholder="Explain when the platform will return" class="w-full rounded-xl border border-amber-200 px-3 py-2.5 text-sm"></textarea></div></div>@error('siteName')<p class="text-xs text-red-600">{{ $message }}</p>@enderror @error('supportEmail')<p class="text-xs text-red-600">{{ $message }}</p>@enderror @error('underConstructionTitle')<p class="text-xs text-red-600">{{ $message }}</p>@enderror @error('underConstructionMessage')<p class="text-xs text-red-600">{{ $message }}</p>@enderror<button class="rounded-xl bg-ember px-4 py-2.5 text-sm font-semibold text-white">Save platform settings</button></form></section>
                <section class="overflow-hidden rounded-2xl border border-ink/8 bg-white"><div class="border-b border-ink/8 px-5 py-4"><h2 class="font-semibold">SMTP email</h2><p class="text-sm text-mist">Secure outgoing email connection for platform messages.</p></div><form wire:submit="saveSmtpSettings" class="space-y-4 p-5"><div class="grid gap-4 sm:grid-cols-3"><label class="text-sm font-medium sm:col-span-2">SMTP host<input wire:model="smtpHost" placeholder="smtp.example.com" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label><label class="text-sm font-medium">Port<input type="number" wire:model="smtpPort" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label></div><div class="grid gap-4 sm:grid-cols-2"><label class="text-sm font-medium">Username<input wire:model="smtpUsername" autocomplete="off" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label><label class="text-sm font-medium">Password<input type="password" wire:model="smtpPassword" autocomplete="new-password" placeholder="Leave blank to keep saved password" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label></div><div class="grid gap-4 sm:grid-cols-3"><label class="text-sm font-medium">Security<select wire:model="smtpScheme" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"><option value="tls">TLS</option><option value="smtps">SMTPS</option></select></label><label class="text-sm font-medium sm:col-span-2">From email<input type="email" wire:model="smtpFromAddress" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label></div><label class="block text-sm font-medium">From name<input wire:model="smtpFromName" class="mt-1.5 w-full rounded-xl border border-ink/15 px-3 py-2.5"></label>@foreach (['smtpHost','smtpPort','smtpUsername','smtpPassword','smtpScheme','smtpFromAddress','smtpFromName'] as $field)@error($field)<p class="text-xs text-red-600">{{ $message }}</p>@enderror @endforeach<div class="flex flex-wrap gap-2"><button class="rounded-xl bg-ink px-4 py-2.5 text-sm font-semibold text-white">Save SMTP</button></div></form><div class="border-t border-ink/8 bg-canvas/50 p-5"><p class="text-sm font-semibold">Send a connection test</p><div class="mt-3 flex flex-col gap-2 sm:flex-row"><input type="email" wire:model="smtpTestRecipient" placeholder="recipient@example.com" class="min-w-0 flex-1 rounded-xl border border-ink/15 px-3 py-2.5 text-sm"><button type="button" wire:click="sendSmtpTest" wire:loading.attr="disabled" class="rounded-xl border border-ember px-4 py-2.5 text-sm font-semibold text-ember disabled:opacity-50">Send test email</button></div>@error('smtpTestRecipient')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div></section>
            </div>

        @endif
    </div>
</div>
