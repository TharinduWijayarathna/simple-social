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
                    <p class="mt-0.5 text-sm text-mist">Approve, suspend, or remove students.</p>
                @elseif ($activeTab === 'talents')
                    <h1 class="text-xl font-semibold">Talent Management</h1>
                    <p class="mt-0.5 text-sm text-mist">Manage custom talent categories and tags for your campus.</p>
                @elseif ($activeTab === 'events')
                    <h1 class="text-xl font-semibold">Event Management</h1>
                    <p class="mt-0.5 text-sm text-mist">Create and manage campus events.</p>
                @elseif ($activeTab === 'moderation')
                    <h1 class="text-xl font-semibold">Content Moderation</h1>
                    <p class="mt-0.5 text-sm text-mist">Investigate reports, document decisions, and protect your campus feed.</p>
                @elseif ($activeTab === 'analytics')
                    <h1 class="text-xl font-semibold">Analytics</h1>
                    <p class="mt-0.5 text-sm text-mist">Growth and engagement for your campus.</p>
                @else
                    <h1 class="text-xl font-semibold">Announcement</h1>
                    <p class="mt-0.5 text-sm text-mist">Set a banner shown only to your students.</p>
                @endif
            </div>
            @if ($activeTab === 'events')
                <a href="{{ route('events.create') }}"
                   class="rounded-xl bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90"
                   wire:navigate>
                    + New event
                </a>
            @elseif ($activeTab === 'talents')
                @if ($talentSubTab === 'talents')
                    <button wire:click="openTalentForm()"
                            class="rounded-xl bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90">
                        + Add Talent
                    </button>
                @else
                    <button wire:click="openCategoryForm()"
                            class="rounded-xl bg-ember px-4 py-2 text-sm font-semibold text-white transition hover:bg-ember/90">
                        + Add Category
                    </button>
                @endif
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
            <button wire:click="$set('activeTab', 'talents')"
                    class="border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'talents' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Talents
            </button>
            <button wire:click="$set('activeTab', 'moderation')"
                    class="flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'moderation' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Moderation
                @if ($pendingReportsCount > 0)
                    <span class="rounded-full bg-ember px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $pendingReportsCount }}</span>
                @endif
            </button>
            <button wire:click="$set('activeTab', 'analytics')"
                    class="border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'analytics' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Analytics
            </button>
            <button wire:click="$set('activeTab', 'announcement')"
                    class="border-b-2 px-4 pb-4 text-sm font-medium transition
                           {{ $activeTab === 'announcement' ? 'border-ember text-ember' : 'border-transparent text-mist hover:text-ink' }}">
                Announcement
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 px-6 py-6">

        {{-- ── OVERVIEW TAB ── --}}
        @if ($activeTab === 'overview')

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
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
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">New students (30d)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $newStudentsLast30Days }}</p>
                    <p class="mt-1 text-xs text-mist">{{ $newStudentsLast7Days }} in the last 7 days</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Published (30d)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $itemsPublishedLast30Days }}</p>
                    <p class="mt-1 text-xs text-mist">Portfolio items from your students</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Reports</p>
                    <p class="mt-2 text-3xl font-semibold {{ $pendingReportsCount > 0 ? 'text-ember' : '' }}">{{ $pendingReportsCount }}</p>
                    <p class="mt-1 text-xs text-mist">Awaiting moderation</p>
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
                            <li class="flex flex-col gap-3 px-5 py-4" wire:key="pend-{{ $student->id }}" x-data="{ expanded: false }">
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

            {{-- Manage students --}}
            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Manage students</h2>
                    <p class="text-sm text-mist">Search, suspend, or remove students from your campus.</p>
                    <input type="text" wire:model.live.debounce.400ms="studentSearch"
                           placeholder="Search by name or email…"
                           class="mt-3 w-full max-w-sm rounded-lg border border-ink/15 px-3 py-2 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
                </div>

                @if ($manageableStudents->isEmpty())
                    <div class="px-5 py-10 text-center text-sm text-mist">No students found.</div>
                @else
                    <ul class="divide-y divide-ink/8">
                        @foreach ($manageableStudents as $student)
                            <li class="flex flex-wrap items-center justify-between gap-4 px-5 py-3 text-sm" wire:key="appr-{{ $student->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 items-center justify-center overflow-hidden rounded-full bg-wall text-xs font-semibold text-ink">
                                        @if ($student->avatarUrl())
                                            <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="size-full object-cover rounded-full">
                                        @else
                                            {{ $student->initials() }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium">
                                            {{ $student->name }}
                                            @if ($student->status->value === 'banned')
                                                <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-red-700">Suspended</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-mist">{{ $student->email }} · Joined {{ $student->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if ($student->status->value === 'banned')
                                        <button wire:click="unsuspendStudent({{ $student->id }})"
                                                class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                            Unsuspend
                                        </button>
                                    @else
                                        <button wire:click="suspendStudent({{ $student->id }})"
                                                class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-medium transition hover:bg-ink/5">
                                            Suspend
                                        </button>
                                    @endif
                                    <button wire:click="removeStudent({{ $student->id }})"
                                            wire:confirm="Remove this student from your campus? This cannot be undone."
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700">
                                        Remove
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="mt-4">{{ $manageableStudents->links() }}</div>

        {{-- ── TALENTS & CATEGORIES TAB ── --}}
        @elseif ($activeTab === 'talents')

            {{-- Sub-tab Switcher & Actions --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex gap-2">
                    <button type="button"
                            wire:click="$set('talentSubTab', 'talents')"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-sm border {{ $talentSubTab === 'talents' ? 'bg-ember text-white border-ember' : 'bg-white text-ink border-ink/10 hover:bg-wall' }}">
                        Talents ({{ $talents->count() }})
                    </button>
                    <button type="button"
                            wire:click="$set('talentSubTab', 'categories')"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-sm border {{ $talentSubTab === 'categories' ? 'bg-ember text-white border-ember' : 'bg-white text-ink border-ink/10 hover:bg-wall' }}">
                        Talent Categories ({{ $talentCategories->count() }})
                    </button>
                </div>

                <div>
                    @if ($talentSubTab === 'talents')
                        <button type="button"
                                wire:click="openTalentForm()"
                                class="rounded-xl bg-ember px-4 py-2 text-xs font-semibold text-white transition hover:bg-ember/90 shadow-sm">
                            + Add New Talent
                        </button>
                    @else
                        <button type="button"
                                wire:click="openCategoryForm()"
                                class="rounded-xl bg-ember px-4 py-2 text-xs font-semibold text-white transition hover:bg-ember/90 shadow-sm">
                            + Add New Category
                        </button>
                    @endif
                </div>
            </div>

            @if (session('talent-status'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs text-emerald-800 font-semibold">
                    {{ session('talent-status') }}
                </div>
            @endif

            {{-- Category Modal --}}
            @if ($showCategoryForm)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/50 p-4 backdrop-blur-sm">
                    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl">
                        <div class="mb-4">
                            <h3 class="font-display text-xl">{{ $editingCategoryId ? 'Edit Talent Category' : 'Add Talent Category' }}</h3>
                            <p class="text-xs text-mist mt-0.5">Categories help organize and filter creator talents on your campus.</p>
                        </div>

                        <form wire:submit="saveCategory" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-ink">Category Name</label>
                                <input wire:model="categoryName" type="text" class="field mt-1 w-full" placeholder="e.g. Performing Arts, Culinary Arts, Tech & Coding" required>
                                @error('categoryName') <span class="text-xs text-ember mt-0.5 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" wire:click="closeCategoryForm" class="rounded-xl border border-ink/10 px-4 py-2 text-xs font-semibold hover:bg-ink/5">
                                    Cancel
                                </button>
                                <button type="submit" class="rounded-xl bg-ember px-4 py-2 text-xs font-semibold text-white hover:bg-ember/90 shadow-sm">
                                    Save Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Talent Modal --}}
            @if ($showTalentForm)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/50 p-4 backdrop-blur-sm">
                    <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl">
                        <div class="mb-4">
                            <h3 class="font-display text-xl">{{ $editingTalentId ? 'Edit Talent' : 'Add Talent' }}</h3>
                            <p class="text-xs text-mist mt-0.5">Define talent tags available for your students and events.</p>
                        </div>

                        <form wire:submit="saveTalent" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-ink">Talent Name</label>
                                <input wire:model="talentName" type="text" class="field mt-1 w-full" placeholder="e.g. Solo Guitar, Classical Dance, Calligraphy" required>
                                @error('talentName') <span class="text-xs text-ember mt-0.5 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-ink">Category</label>
                                <select wire:model="talentCategory" class="field mt-1 w-full" required>
                                    <option value="">Select a Category</option>
                                    @foreach ($talentCategories as $cat)
                                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('talentCategory') <span class="text-xs text-ember mt-0.5 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-ink">Format/Theme Layout</label>
                                <select wire:model="talentTheme" class="field mt-1 w-full" required>
                                    <option value="stage">Stage (Performing Arts style)</option>
                                    <option value="gallery">Gallery (Creative & Visual Arts style)</option>
                                    <option value="grid">Grid (Sports & Stats style)</option>
                                    <option value="social">Social (General/Community style)</option>
                                </select>
                                @error('talentTheme') <span class="text-xs text-ember mt-0.5 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-ink">Description (Optional)</label>
                                <textarea wire:model="talentDescription" rows="3" class="field mt-1 w-full" placeholder="Describe the talent and guidelines for students..."></textarea>
                                @error('talentDescription') <span class="text-xs text-ember mt-0.5 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" wire:click="closeTalentForm" class="rounded-xl border border-ink/10 px-4 py-2 text-xs font-semibold hover:bg-ink/5">
                                    Cancel
                                </button>
                                <button type="submit" class="rounded-xl bg-ember px-4 py-2 text-xs font-semibold text-white hover:bg-ember/90 shadow-sm">
                                    Save Talent
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- List view based on sub-tab --}}
            @if ($talentSubTab === 'categories')
                {{-- Categories List --}}
                <div class="rounded-2xl border border-ink/8 bg-white overflow-hidden shadow-sm">
                    <div class="border-b border-ink/8 px-5 py-4 flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold">Talent Categories</h2>
                            <p class="text-xs text-mist mt-0.5">Manage talent categories available on your campus.</p>
                        </div>
                    </div>

                    @if ($talentCategories->isEmpty())
                        <div class="px-5 py-10 text-center text-sm text-mist">No categories found. Click "+ Add New Category" to create one.</div>
                    @else
                        <div class="divide-y divide-ink/8 max-h-[600px] overflow-y-auto">
                            @foreach ($talentCategories as $cat)
                                <div class="flex items-center justify-between gap-4 px-5 py-4" wire:key="cat-list-{{ $cat->id }}">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-sm text-ink">{{ $cat->name }}</p>
                                            @if ($cat->campus_id)
                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-bold text-amber-800 uppercase tracking-wider">Campus Custom</span>
                                            @else
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600 uppercase tracking-wider">System Category</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-mist mt-0.5">
                                            {{ $cat->talents_count }} talent{{ $cat->talents_count !== 1 ? 's' : '' }} assigned
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button wire:click="openCategoryForm({{ $cat->id }})" class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-semibold text-mist hover:text-ink hover:bg-wall transition">
                                            Edit
                                        </button>
                                        <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Are you sure you want to delete this category? Talents under it will be reassigned." class="rounded-lg border border-red-200 text-red-600 px-3 py-1.5 text-xs font-semibold hover:bg-red-50 transition">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                {{-- Talents List --}}
                <div class="rounded-2xl border border-ink/8 bg-white overflow-hidden shadow-sm">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Talents</h2>
                        <p class="text-xs text-mist mt-0.5">Manage talent tags and themes available on your campus.</p>
                    </div>

                    @if ($talents->isEmpty())
                        <div class="px-5 py-10 text-center text-sm text-mist">No talents found. Click "+ Add New Talent" to create one.</div>
                    @else
                        <div class="divide-y divide-ink/8 max-h-[600px] overflow-y-auto">
                            @foreach ($talents as $talent)
                                <div class="flex items-center justify-between gap-4 px-5 py-3.5" wire:key="tal-list-{{ $talent->id }}">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-sm text-ink truncate">{{ $talent->name }}</p>
                                            @if ($talent->campus_id)
                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-bold text-amber-800 uppercase tracking-wider">Campus Custom</span>
                                            @else
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600 uppercase tracking-wider">System Talent</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-mist mt-0.5">
                                            Category: <strong class="text-ink font-medium">{{ $talent->category }}</strong> 
                                            · Theme: <span class="capitalize">{{ $talent->theme->value }}</span>
                                        </p>
                                        @if ($talent->description)
                                            <p class="text-xs text-mist mt-1 leading-relaxed line-clamp-1">{{ $talent->description }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button wire:click="openTalentForm({{ $talent->id }})" class="rounded-lg border border-ink/15 px-3 py-1.5 text-xs font-semibold text-mist hover:text-ink hover:bg-wall transition">
                                            Edit
                                        </button>
                                        <button wire:click="deleteTalent({{ $talent->id }})" wire:confirm="Are you sure you want to delete this talent? This cannot be undone." class="rounded-lg border border-red-200 text-red-600 px-3 py-1.5 text-xs font-semibold hover:bg-red-50 transition">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        {{-- ── EVENTS TAB ── --}}
        @elseif ($activeTab === 'events')

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
                    <p class="mt-1 text-xs text-mist">Open reports in your queue</p>
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
                    <p class="mt-1 text-xs text-mist">Published campus work</p>
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
                        <article class="p-5" wire:key="mod-report-{{ $report->id }}">
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
                            <p class="mt-1 max-w-sm text-sm text-mist">Your queue is clear for this filter. Try another status or search term to review moderation history.</p>
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
                        <article class="overflow-hidden rounded-2xl border border-ink/10 bg-white" wire:key="moderation-item-{{ $item->id }}">
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
                                        <p class="truncate text-xs text-mist">{{ $item->user->name }} · {{ $item->talent?->name ?? 'Uncategorized' }}</p>
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
                        <p class="mt-3 text-sm leading-relaxed text-mist">The work will disappear from the campus feed. Your explanation will be stored in moderation history, and the work can be restored later.</p>
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

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Students</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalStudents }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">New (7 days)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $newStudentsLast7Days }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">New (30 days)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $newStudentsLast30Days }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Events</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $totalEvents }}</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">XP earned (30d)</p>
                    <p class="mt-2 text-3xl font-semibold text-ember">{{ number_format($xpEarnedLast30Days) }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Published (30d)</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $itemsPublishedLast30Days }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Applications</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $eventApplicationsTotal }}</p>
                </div>
                <div class="rounded-2xl border border-ink/8 bg-white p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-mist">Accepted</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $eventApplicationsAccepted }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                {{-- Weekly publishing trend --}}
                <div class="rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Published work, last 6 weeks</h2>
                    </div>
                    <div class="flex items-end gap-3 px-5 py-6">
                        @php $maxCount = max(1, $weeklyPublishedCounts->max('count')); @endphp
                        @foreach ($weeklyPublishedCounts as $week)
                            <div class="flex flex-1 flex-col items-center gap-2" wire:key="week-{{ $week['label'] }}">
                                <span class="text-xs font-semibold text-ink">{{ $week['count'] }}</span>
                                <div class="flex h-24 w-full items-end rounded-md bg-wall">
                                    <div class="w-full rounded-md bg-ember transition-all"
                                         style="height: {{ max(4, ($week['count'] / $maxCount) * 100) }}%"></div>
                                </div>
                                <span class="text-[10px] text-mist">{{ $week['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top students --}}
                <div class="rounded-2xl border border-ink/8 bg-white">
                    <div class="border-b border-ink/8 px-5 py-4">
                        <h2 class="font-semibold">Top students by rank</h2>
                    </div>
                    <ul class="divide-y divide-ink/8">
                        @forelse ($topStudents as $student)
                            <li class="flex items-center justify-between px-5 py-3 text-sm" wire:key="top-{{ $student->id }}">
                                <div class="flex items-center gap-3">
                                    <span class="w-4 text-xs font-semibold text-mist">#{{ $student->current_rank }}</span>
                                    <div class="flex size-8 items-center justify-center rounded-full bg-wall text-xs font-semibold text-ink">
                                        {{ $student->initials() }}
                                    </div>
                                    <span class="font-medium">{{ $student->name }}</span>
                                </div>
                                <span class="font-semibold text-ember">{{ number_format($student->xp) }} XP</span>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-mist">No ranked students yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-ink/8 bg-white">
                <div class="border-b border-ink/8 px-5 py-4">
                    <h2 class="font-semibold">Talent rooms by published work</h2>
                </div>
                <ul class="divide-y divide-ink/8">
                    @forelse ($categories as $category)
                        <li class="flex items-center justify-between px-5 py-3 text-sm" wire:key="an-cat-{{ $category->id }}">
                            <span>{{ $category->name }}</span>
                            <span class="font-semibold text-ember">{{ $category->published_items_count }}</span>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-mist">No published work yet.</li>
                    @endforelse
                </ul>
            </div>

        {{-- ── ANNOUNCEMENT TAB ── --}}
        @else
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-ember">Student communications</p>
                    <h2 class="mt-1 font-display text-3xl">Announcement centre</h2>
                    <p class="mt-1 text-sm text-mist">Create targeted notices, schedule publication, and track student reads.</p>
                </div>
                <button wire:click="openAnnouncementForm" class="btn-primary shrink-0">
                    + New announcement
                </button>
            </div>

            @if (session('announcement-status'))
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('announcement-status') }}
                </div>
            @endif

            <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    'active' => ['Active', 'bg-emerald-100 text-emerald-700'],
                    'scheduled' => ['Scheduled', 'bg-blue-100 text-blue-700'],
                    'draft' => ['Drafts', 'bg-studio/8 text-studio'],
                    'expired' => ['Expired', 'bg-ink/5 text-mist'],
                ] as $key => [$label, $tone])
                    <button wire:click="$set('announcementStatus', '{{ $key }}')" class="rounded-2xl border border-ink/8 bg-white p-4 text-left transition hover:-translate-y-0.5 hover:shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-mist">{{ $label }}</span>
                            <span class="size-2 rounded-full {{ $tone }}"></span>
                        </div>
                        <div class="mt-2 text-3xl font-bold">{{ $announcementStats[$key] }}</div>
                    </button>
                @endforeach
            </div>

            <div class="mt-6 rounded-2xl border border-ink/8 bg-white p-3">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach (['all' => 'All', 'active' => 'Active', 'scheduled' => 'Scheduled', 'draft' => 'Drafts', 'expired' => 'Expired'] as $value => $label)
                            <button wire:click="$set('announcementStatus', '{{ $value }}')" class="shrink-0 rounded-xl px-3.5 py-2 text-xs font-semibold {{ $announcementStatus === $value ? 'bg-ink text-white' : 'text-mist hover:bg-wall hover:text-ink' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <input wire:model.live.debounce.300ms="announcementSearch" type="search" placeholder="Search title or message…" class="w-full rounded-xl border border-ink/10 bg-wall px-4 py-2.5 text-sm outline-none focus:border-ember focus:ring-2 focus:ring-ember/10 lg:w-80">
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-ink/8 bg-white">
                <div class="hidden grid-cols-[1fr_150px_140px_100px_180px] gap-4 border-b border-ink/8 bg-wall/70 px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-mist lg:grid">
                    <span>Announcement</span><span>Audience</span><span>Timing</span><span>Reads</span><span class="text-right">Actions</span>
                </div>
                <div class="divide-y divide-ink/8">
                    @forelse ($announcements as $announcement)
                        @php
                            $status = $announcement->status();
                            $statusTone = match ($status) {
                                'active' => 'bg-emerald-100 text-emerald-700',
                                'scheduled' => 'bg-blue-100 text-blue-700',
                                'expired' => 'bg-ink/5 text-mist',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <div wire:key="admin-announcement-{{ $announcement->id }}" class="grid gap-4 px-5 py-4 lg:grid-cols-[1fr_150px_140px_100px_180px] lg:items-center">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate font-semibold">{{ $announcement->title }}</h3>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $statusTone }}">{{ $status }}</span>
                                    @if ($announcement->is_pinned)<span class="rounded-full bg-gold/20 px-2 py-0.5 text-[10px] font-bold">Pinned</span>@endif
                                </div>
                                <p class="mt-1 line-clamp-1 text-xs text-mist">{{ $announcement->body }}</p>
                            </div>
                            <div class="text-xs">
                                <div class="font-semibold">{{ $announcement->audience->label() }}</div>
                                @if ($announcement->audience_value)<div class="mt-0.5 truncate text-mist">{{ $announcement->audience_value }}</div>@endif
                            </div>
                            <div class="text-xs text-mist">
                                @if ($status === 'scheduled')Starts {{ $announcement->starts_at->format('M j, g:i A') }}
                                @elseif ($announcement->expires_at)Ends {{ $announcement->expires_at->format('M j, g:i A') }}
                                @elseif ($announcement->published_at)Since {{ $announcement->published_at->format('M j') }}
                                @elseNot published
                                @endif
                            </div>
                            <div class="text-xs"><strong class="text-base text-ink">{{ $announcement->read_count }}</strong><span class="ml-1 text-mist">students</span></div>
                            <div class="flex flex-wrap justify-start gap-1.5 lg:justify-end">
                                <button wire:click="openAnnouncementForm({{ $announcement->id }})" class="rounded-lg border border-ink/10 px-2.5 py-1.5 text-xs font-semibold hover:bg-wall">Edit</button>
                                @if ($announcement->published_at)
                                    <button wire:click="unpublishAnnouncement({{ $announcement->id }})" wire:confirm="Move this announcement back to drafts?" class="rounded-lg border border-ink/10 px-2.5 py-1.5 text-xs font-semibold hover:bg-wall">Unpublish</button>
                                @else
                                    <button wire:click="publishAnnouncement({{ $announcement->id }})" class="rounded-lg bg-ember px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-ember/90">Publish</button>
                                @endif
                                <button wire:click="deleteAnnouncement({{ $announcement->id }})" wire:confirm="Delete this announcement permanently?" class="rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50" aria-label="Delete {{ $announcement->title }}">Delete</button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-wall text-mist"><x-icon name="megaphone" class="size-6" /></div>
                            <h3 class="mt-4 font-display text-xl">No announcements found</h3>
                            <p class="mt-1 text-sm text-mist">Create a new announcement or adjust the current filters.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if ($announcements->hasPages())<div class="mt-5">{{ $announcements->links() }}</div>@endif

            @if ($showAnnouncementForm)
                <div class="fixed inset-0 z-50 overflow-y-auto bg-ink/55 p-4 backdrop-blur-sm" wire:click.self="closeAnnouncementForm">
                    <div class="mx-auto my-4 max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl sm:my-10">
                        <div class="flex items-start justify-between border-b border-ink/8 px-6 py-5">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-ember">{{ $editingAnnouncementId ? 'Edit communication' : 'New communication' }}</p>
                                <h3 class="mt-1 font-display text-2xl">{{ $editingAnnouncementId ? 'Update announcement' : 'Create announcement' }}</h3>
                            </div>
                            <button wire:click="closeAnnouncementForm" class="flex size-9 items-center justify-center rounded-full text-xl text-mist hover:bg-wall hover:text-ink" aria-label="Close">×</button>
                        </div>
                        <form class="space-y-5 px-6 py-6">
                            <div>
                                <label class="text-sm font-semibold">Title</label>
                                <input wire:model="announcementTitle" maxlength="120" placeholder="Clear, action-focused headline" class="mt-1.5 w-full rounded-xl border border-ink/15 px-4 py-3 text-sm outline-none focus:border-ember focus:ring-2 focus:ring-ember/10">
                                @error('announcementTitle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <div class="flex items-center justify-between"><label class="text-sm font-semibold">Message</label><span class="text-xs text-mist">Up to 5,000 characters</span></div>
                                <textarea wire:model="announcementBody" rows="6" maxlength="5000" placeholder="Include the details students need, key dates, and the next action." class="mt-1.5 w-full rounded-xl border border-ink/15 px-4 py-3 text-sm leading-6 outline-none focus:border-ember focus:ring-2 focus:ring-ember/10"></textarea>
                                @error('announcementBody')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold">Priority</label>
                                    <select wire:model="announcementPriority" class="mt-1.5 w-full rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm outline-none focus:border-ember">
                                        @foreach (\App\Enums\AnnouncementPriority::cases() as $priority)<option value="{{ $priority->value }}">{{ $priority->label() }}</option>@endforeach
                                    </select>
                                    @error('announcementPriority')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold">Audience</label>
                                    <select wire:model.live="announcementAudience" class="mt-1.5 w-full rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm outline-none focus:border-ember">
                                        @foreach (\App\Enums\AnnouncementAudience::cases() as $audience)<option value="{{ $audience->value }}">{{ $audience->label() }}</option>@endforeach
                                    </select>
                                    @error('announcementAudience')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            @if ($announcementAudience !== \App\Enums\AnnouncementAudience::Everyone->value)
                                <div>
                                    <label class="text-sm font-semibold">Choose target group</label>
                                    <select wire:model="announcementAudienceValue" class="mt-1.5 w-full rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm outline-none focus:border-ember">
                                        <option value="">Select a group</option>
                                        @foreach ($announcementAudienceValues as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach
                                    </select>
                                    @if ($announcementAudienceValues === [])<p class="mt-1 text-xs text-amber-700">No students currently have this profile field completed.</p>@endif
                                    @error('announcementAudienceValue')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            @endif
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div><label class="text-sm font-semibold">Starts at <span class="font-normal text-mist">(optional)</span></label><input wire:model="announcementStartsAt" type="datetime-local" class="mt-1.5 w-full rounded-xl border border-ink/15 px-4 py-3 text-sm outline-none focus:border-ember">@error('announcementStartsAt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                                <div><label class="text-sm font-semibold">Expires at <span class="font-normal text-mist">(optional)</span></label><input wire:model="announcementExpiresAt" type="datetime-local" class="mt-1.5 w-full rounded-xl border border-ink/15 px-4 py-3 text-sm outline-none focus:border-ember">@error('announcementExpiresAt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div><label class="text-sm font-semibold">Action link <span class="font-normal text-mist">(optional)</span></label><input wire:model="announcementLinkUrl" type="url" placeholder="https://…" class="mt-1.5 w-full rounded-xl border border-ink/15 px-4 py-3 text-sm outline-none focus:border-ember">@error('announcementLinkUrl')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                                <div><label class="text-sm font-semibold">Button label</label><input wire:model="announcementLinkLabel" maxlength="60" placeholder="Register now" class="mt-1.5 w-full rounded-xl border border-ink/15 px-4 py-3 text-sm outline-none focus:border-ember">@error('announcementLinkLabel')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                            </div>
                            <label class="flex items-start gap-3 rounded-xl border border-ink/10 bg-wall/60 p-4">
                                <input wire:model="announcementPinned" type="checkbox" class="mt-0.5 rounded border-ink/20 text-ember focus:ring-ember">
                                <span><span class="block text-sm font-semibold">Pin this announcement</span><span class="block text-xs text-mist">Pinned announcements stay above standard updates.</span></span>
                            </label>
                            <div class="flex flex-col-reverse gap-2 border-t border-ink/8 pt-5 sm:flex-row sm:justify-end">
                                <button wire:click="closeAnnouncementForm" type="button" class="rounded-xl border border-ink/10 px-5 py-2.5 text-sm font-semibold hover:bg-wall">Cancel</button>
                                <button wire:click="saveAnnouncement(false)" type="button" wire:loading.attr="disabled" class="rounded-xl border border-ink/15 px-5 py-2.5 text-sm font-semibold hover:bg-wall">Save draft</button>
                                <button wire:click="saveAnnouncement(true)" type="button" wire:loading.attr="disabled" class="btn-primary">{{ $announcementStartsAt ? 'Publish / schedule' : 'Publish now' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        @endif
    </div>
</div>
