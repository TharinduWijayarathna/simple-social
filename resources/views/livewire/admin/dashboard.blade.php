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
                    @case('students')
                        <h1 class="text-xl font-semibold">Student Management</h1>
                        <p class="mt-0.5 text-sm text-mist">Review, verify, approve, reject, and ban student profiles across all campuses.</p>
                        @break
                    @case('campuses')
                        <h1 class="text-xl font-semibold">Campus Management</h1>
                        <p class="mt-0.5 text-sm text-mist">Review campus admin applications and manage approved campuses.</p>
                        @break
                    @case('users')
                        <h1 class="text-xl font-semibold">User Management</h1>
                        <p class="mt-0.5 text-sm text-mist">Search users, change roles, suspend or remove accounts.</p>
                        @break
                    @case('moderation')
                        <h1 class="text-xl font-semibold">Content Moderation</h1>
                        <p class="mt-0.5 text-sm text-mist">Review reports and manage recently published work.</p>
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
            @if ($activeTab === 'campuses' && $pendingCampusAdmins->isNotEmpty())
                <span class="flex items-center gap-1.5 rounded-full bg-ember/10 px-3 py-1 text-xs font-semibold text-ember">
                    <span class="size-2 rounded-full bg-ember"></span>
                    {{ $pendingCampusAdmins->count() }} pending
                </span>
            @elseif ($activeTab === 'students' && $pendingStudents->isNotEmpty())
                <span class="flex items-center gap-1.5 rounded-full bg-ember/10 px-3 py-1 text-xs font-semibold text-ember">
                    <span class="size-2 rounded-full bg-ember"></span>
                    {{ $pendingStudents->count() }} pending
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
            <button wire:click="$set('activeTab', 'students')"
                    class="flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'students' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Student Management
                @if ($pendingStudents->isNotEmpty())
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $pendingStudents->count() }}</span>
                @endif
            </button>
            <button wire:click="$set('activeTab', 'campuses')"
                    class="shrink-0 flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'campuses' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Campus Management
                @if ($pendingCampusAdmins->isNotEmpty())
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $pendingCampusAdmins->count() }}</span>
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
                @if ($reports->isNotEmpty())
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $reports->count() }}</span>
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

        {{-- ── STUDENTS TAB ── --}}
        @elseif ($activeTab === 'students')

            {{-- Pending student registrations across all campuses --}}
            <div class="rounded-2xl border border-ink/8 bg-white">
                <div class="flex items-center justify-between border-b border-ink/8 px-5 py-4">
                    <div>
                        <h2 class="font-semibold">Pending registrations (All Campuses)</h2>
                        <p class="text-sm text-mist">Students waiting for approval across all campuses</p>
                    </div>
                    @if ($pendingStudents->isNotEmpty())
                        <span class="rounded-full bg-ember px-2.5 py-0.5 text-xs font-semibold text-white">{{ $pendingStudents->count() }}</span>
                    @endif
                </div>

                @if ($pendingStudents->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No pending student registrations ✓</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($pendingStudents as $student)
                            <li class="flex flex-col gap-3 px-5 py-4" wire:key="admin-pend-{{ $student->id }}" x-data="{ expanded: false }">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 items-center justify-center overflow-hidden rounded-full bg-wall text-sm font-semibold text-ink">
                                            @if ($student->avatarUrl())
                                                <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                                            @else
                                                {{ $student->initials() }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium flex items-center gap-2">
                                                <span>{{ $student->name }}</span>
                                                @if ($student->campus)
                                                    <span class="rounded-full bg-wall px-2 py-0.5 text-[10px] font-bold text-mist">{{ $student->campus->name }}</span>
                                                @endif
                                                <button type="button" @click="expanded = !expanded" class="text-xs text-ember font-semibold hover:underline">
                                                    <span x-text="expanded ? 'Hide Details' : 'View Details'"></span>
                                                </button>
                                            </p>
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
                                </div>

                                {{-- Collapsible Details Section --}}
                                <div x-show="expanded" x-collapse class="border-t border-ink/5 pt-3 mt-1 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-mist bg-wall/30 p-3.5 rounded-xl" style="display: none;">
                                    <div>
                                        <span class="block font-semibold text-ink">Campus</span>
                                        <span>{{ $student->campus?->name ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-ink">University ID</span>
                                        <span>{{ $student->university_id ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-ink">Batch</span>
                                        <span>{{ $student->profile?->batch ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-ink">Program</span>
                                        <span>{{ $student->profile?->program ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-ink">Faculty</span>
                                        <span>{{ $student->profile?->faculty ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-ink">Department</span>
                                        <span>{{ $student->profile?->department ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-ink">Primary Talent</span>
                                        <span>{{ $student->profile?->primaryTalentModel?->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-span-full pt-1">
                                        <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1 font-bold text-ember hover:underline" wire:navigate>
                                            View Public Profile Page ↗
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Approved students --}}
            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold">Approved students (All Campuses)</h2>
                        <p class="text-sm text-mist">{{ $approvedStudents->count() }} active student{{ $approvedStudents->count() !== 1 ? 's' : '' }} across the platform</p>
                    </div>
                </div>

                @if ($approvedStudents->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No approved students yet.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($approvedStudents as $student)
                            <li class="flex items-center justify-between gap-4 px-5 py-3.5 text-sm" wire:key="admin-appr-{{ $student->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 items-center justify-center overflow-hidden rounded-full bg-wall text-xs font-semibold text-ink">
                                        @if ($student->avatarUrl())
                                            <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                                        @else
                                            {{ $student->initials() }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium flex items-center gap-2">
                                            <span>{{ $student->name }}</span>
                                            @if ($student->campus)
                                                <span class="rounded-full bg-wall px-2 py-0.5 text-[10px] font-bold text-mist">{{ $student->campus->name }}</span>
                                            @endif
                                            <a href="{{ route('students.show', $student) }}" class="text-[10px] text-ember hover:underline" wire:navigate>
                                                View Profile ↗
                                            </a>
                                        </p>
                                        <p class="text-xs text-mist">{{ $student->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-mist">{{ $student->created_at->format('M j, Y') }}</span>
                                    <button wire:click="banStudent({{ $student->id }})" class="rounded-lg border border-red-200 text-red-600 px-3 py-1 text-xs font-semibold hover:bg-red-50 transition">
                                        Ban Profile
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Banned students --}}
            <div class="mt-6 rounded-2xl border border-red-200 bg-white">
                <div class="border-b border-red-100 bg-red-50/50 px-5 py-4 rounded-t-2xl">
                    <h2 class="font-semibold text-red-950">Banned Students (All Campuses)</h2>
                    <p class="text-sm text-red-800/80">{{ $bannedStudents->count() }} banned student{{ $bannedStudents->count() !== 1 ? 's' : '' }}</p>
                </div>

                @if ($bannedStudents->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No banned students.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($bannedStudents as $student)
                            <li class="flex items-center justify-between gap-4 px-5 py-3 text-sm" wire:key="admin-bann-{{ $student->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 items-center justify-center overflow-hidden rounded-full bg-wall text-xs font-semibold text-ink">
                                        @if ($student->avatarUrl())
                                            <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                                        @else
                                            {{ $student->initials() }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium flex items-center gap-2 text-ink/70">
                                            <span class="line-through">{{ $student->name }}</span>
                                            @if ($student->campus)
                                                <span class="rounded-full bg-wall px-2 py-0.5 text-[10px] font-bold text-mist">{{ $student->campus->name }}</span>
                                            @endif
                                            <a href="{{ route('students.show', $student) }}" class="text-[10px] text-ember hover:underline" wire:navigate>
                                                View Profile ↗
                                            </a>
                                        </p>
                                        <p class="text-xs text-mist">{{ $student->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-mist">Banned</span>
                                    <button wire:click="unbanStudent({{ $student->id }})" class="rounded-lg bg-red-600 text-white px-3 py-1 text-xs font-semibold hover:bg-red-700 transition shadow-sm">
                                        Unban
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        {{-- ── CAMPUSES TAB ── --}}
        @elseif ($activeTab === 'campuses')

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
                                    <div class="flex size-10 items-center justify-center overflow-hidden rounded-full bg-ember/10 text-sm font-semibold text-ember">
                                        @if ($applicant->avatarUrl())
                                            <img src="{{ $applicant->avatarUrl() }}" alt="{{ $applicant->name }}" class="size-full object-cover rounded-full">
                                        @else
                                            {{ $applicant->initials() }}
                                        @endif
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
                                    <div class="flex size-10 items-center justify-center overflow-hidden rounded-full bg-studio text-sm font-semibold text-gold">
                                        @if ($campus->avatarUrl())
                                            <img src="{{ $campus->avatarUrl() }}" alt="{{ $campus->name }}" class="size-full object-cover rounded-full">
                                        @else
                                            {{ $campus->initials() }}
                                        @endif
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

        {{-- ── USERS TAB ── --}}
        @elseif ($activeTab === 'users')

            <div class="mb-4">
                <input type="text" wire:model.live.debounce.400ms="userSearch"
                       placeholder="Search by name or email…"
                       class="w-full max-w-sm rounded-lg border border-ink/15 px-3 py-2 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            </div>

            <div class="rounded-2xl border border-ink/8 bg-white">
                <ul class="divide-y divide-ink/8">
                    @forelse ($users as $user)
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
                                    <p class="text-xs text-mist">{{ $user->role->label() }} · Joined {{ $user->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            @unless ($user->is(auth()->user()))
                                <div class="flex flex-wrap items-center gap-2">
                                    <select wire:change="assignRole({{ $user->id }}, $event.target.value)"
                                            class="rounded-lg border border-ink/15 px-2 py-1.5 text-xs">
                                        @foreach (\App\Enums\Role::cases() as $role)
                                            <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                        @endforeach
                                    </select>
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
                    @empty
                        <li class="px-5 py-10 text-center text-sm text-mist">No users found.</li>
                    @endforelse
                </ul>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>

        {{-- ── MODERATION TAB ── --}}
        @elseif ($activeTab === 'moderation')

            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Reports queue --}}
                <div class="rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Reports queue</h2>
                    </div>
                    <ul class="divide-y divide-ink/8">
                        @forelse ($reports as $report)
                            <li class="px-5 py-4 text-sm" wire:key="mod-rep-{{ $report->id }}">
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

                {{-- Recently published work --}}
                <div class="rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Recently published work</h2>
                    </div>
                    <ul class="divide-y divide-ink/8">
                        @forelse ($recentItems as $item)
                            <li class="px-5 py-4 text-sm" wire:key="item-{{ $item->id }}">
                                <p><span class="font-medium">{{ $item->title }}</span> <span class="text-mist">· by {{ $item->user->name }}</span></p>
                                <div class="mt-3 flex gap-2">
                                    <button wire:click="unpublishItem({{ $item->id }})"
                                            class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                        Unpublish
                                    </button>
                                    <button wire:click="deleteItem({{ $item->id }})"
                                            wire:confirm="Permanently delete this item? This cannot be undone."
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700">
                                        Delete
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-mist">Nothing published yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        {{-- ── ANALYTICS TAB ── --}}
        @elseif ($activeTab === 'analytics')

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Total users</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalUsers }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">New (7 days)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $newUsersLast7Days }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">New (30 days)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $newUsersLast30Days }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Suspended</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalBanned }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Talent rooms by published work</h2>
                </div>
                <ul class="divide-y divide-ink/8">
                    @foreach ($categories as $category)
                        <li class="flex items-center justify-between px-5 py-3 text-sm" wire:key="an-cat-{{ $category->id }}">
                            <span>{{ $category->name }}</span>
                            <span class="font-semibold text-ember">{{ $category->published_items_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

        {{-- ── SETTINGS TAB ── --}}
        @elseif ($activeTab === 'settings')

            <div class="max-w-xl rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Site announcement</h2>
                    <p class="text-sm text-mist">Shown as a banner to every signed-in user.</p>
                </div>
                <form wire:submit="saveSettings" class="space-y-4 px-5 py-5">
                    <label class="flex items-center gap-2 text-sm font-medium">
                        <input type="checkbox" wire:model="announcementEnabled" class="rounded border-ink/20">
                        Enable announcement banner
                    </label>
                    <div>
                        <textarea wire:model="announcementMessage" rows="3" maxlength="280"
                                  placeholder="e.g. Scheduled maintenance on Friday, 9–10pm."
                                  class="w-full rounded-lg border border-ink/15 px-3 py-2 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember"></textarea>
                        @error('announcementMessage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                            class="rounded-lg bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90">
                        Save settings
                    </button>
                </form>
            </div>

        @endif
    </div>
</div>
