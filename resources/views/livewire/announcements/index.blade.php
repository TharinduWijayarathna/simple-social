<div class="page-shell py-8 lg:py-12">
    <div class="mx-auto max-w-5xl">
        <div class="overflow-hidden rounded-3xl bg-studio px-6 py-8 text-white shadow-sm sm:px-9">
            <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <div class="mb-4 flex size-11 items-center justify-center rounded-2xl bg-white/10 text-gold"><x-icon name="megaphone" class="size-6" /></div>
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-gold">Campus noticeboard</p>
                    <h1 class="mt-2 font-display text-4xl">Announcements</h1>
                    <p class="mt-2 max-w-xl text-sm text-white/65">Official updates, deadlines, opportunities, and urgent notices from {{ auth()->user()->campus?->displayCampusName() ?? 'your campus' }}.</p>
                </div>
                <div class="rounded-2xl bg-white/10 px-5 py-3 text-center">
                    <div class="text-2xl font-bold">{{ $unreadCount }}</div>
                    <div class="text-xs text-white/60">Unread {{ Str::plural('update', $unreadCount) }}</div>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-ink/8 bg-white p-3 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex gap-2 overflow-x-auto">
                    @foreach (['all' => 'Current', 'unread' => 'Unread', 'important' => 'Important', 'dismissed' => 'Archived'] as $value => $label)
                        <button wire:click="$set('filter', '{{ $value }}')" class="shrink-0 rounded-xl px-4 py-2 text-sm font-semibold transition {{ $filter === $value ? 'bg-ink text-white' : 'text-mist hover:bg-wall hover:text-ink' }}">
                            {{ $label }}
                            @if ($value === 'unread' && $unreadCount > 0)<span class="ml-1 rounded-full bg-ember px-1.5 py-0.5 text-[10px] text-white">{{ $unreadCount }}</span>@endif
                        </button>
                    @endforeach
                </div>
                <label class="relative block lg:w-80">
                    <span class="sr-only">Search announcements</span>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search announcements…" class="w-full rounded-xl border border-ink/10 bg-wall py-2.5 pl-4 pr-10 text-sm outline-none transition focus:border-ember focus:ring-2 focus:ring-ember/10">
                    <x-icon name="sparkles" class="pointer-events-none absolute right-3 top-3 size-4 text-mist" />
                </label>
            </div>
        </div>

        <div id="announcements-list" class="mt-6 space-y-4">
            @forelse ($announcements as $announcement)
                @php
                    $isRead = $announcement->isReadBy(auth()->user());
                    $isDismissed = $announcement->isDismissedBy(auth()->user());
                    $tone = match ($announcement->priority) {
                        \App\Enums\AnnouncementPriority::Urgent => 'border-red-200 bg-red-50/40',
                        \App\Enums\AnnouncementPriority::Important => 'border-amber-200 bg-amber-50/40',
                        default => 'border-ink/8 bg-white',
                    };
                @endphp
                <article wire:key="announcement-{{ $announcement->id }}" class="relative overflow-hidden rounded-2xl border {{ $tone }} p-5 shadow-sm sm:p-6">
                    @if (! $isRead && ! $isDismissed)<span class="absolute right-5 top-5 size-2 rounded-full bg-ember" title="Unread"></span>@endif
                    <div class="flex flex-wrap items-center gap-2 pr-5 text-[11px] font-bold uppercase tracking-wider">
                        <span class="rounded-full px-2.5 py-1 {{ $announcement->priority === \App\Enums\AnnouncementPriority::Urgent ? 'bg-red-100 text-red-700' : ($announcement->priority === \App\Enums\AnnouncementPriority::Important ? 'bg-amber-100 text-amber-700' : 'bg-studio/8 text-studio') }}">{{ $announcement->priority->label() }}</span>
                        @if ($announcement->is_pinned)<span class="rounded-full bg-gold/20 px-2.5 py-1 text-ink">Pinned</span>@endif
                        <span class="text-mist">{{ $announcement->published_at->diffForHumans() }}</span>
                    </div>
                    <h2 class="mt-3 font-display text-2xl text-ink">{{ $announcement->title }}</h2>
                    <p class="mt-1 text-xs font-medium text-mist">{{ $announcement->campus->displayCampusName() }} · {{ $announcement->audience->label() }}{{ $announcement->audience_value ? ': '.$announcement->audience_value : '' }}</p>
                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-ink/80">{{ $announcement->body }}</div>
                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-ink/8 pt-4">
                        @if ($announcement->link_url)<a href="{{ $announcement->link_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary text-xs">{{ $announcement->link_label ?: 'Learn more' }}</a>@endif
                        @if (! $isRead)<button wire:click="markAsRead({{ $announcement->id }})" class="rounded-lg px-3 py-2 text-xs font-semibold text-ember hover:bg-ember/8">Mark as read</button>@endif
                        @if ($isDismissed)
                            <button wire:click="restore({{ $announcement->id }})" class="rounded-lg px-3 py-2 text-xs font-semibold text-ink hover:bg-ink/5">Restore</button>
                        @else
                            <button wire:click="dismiss({{ $announcement->id }})" class="rounded-lg px-3 py-2 text-xs font-semibold text-mist hover:bg-ink/5 hover:text-ink">Archive</button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-ink/15 bg-white px-6 py-16 text-center">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-wall text-mist"><x-icon name="megaphone" class="size-6" /></div>
                    <h2 class="mt-4 font-display text-2xl">Nothing here right now</h2>
                    <p class="mt-1 text-sm text-mist">{{ $search !== '' ? 'Try a different search.' : 'New campus announcements will appear here.' }}</p>
                </div>
            @endforelse
        </div>

        @if ($announcements->hasPages())<div class="mt-6">{{ $announcements->links(data: ['scrollTo' => '#announcements-list']) }}</div>@endif
    </div>
</div>
