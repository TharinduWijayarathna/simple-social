<div>
    @if ($announcement)
        <div class="border-b {{ $announcement->priority === \App\Enums\AnnouncementPriority::Urgent ? 'border-red-200 bg-red-50 text-red-800' : 'border-ember/15 bg-ember/8 text-ink' }}">
        <div class="page-shell flex items-center gap-3 py-2.5 text-sm">
            <x-icon name="megaphone" class="size-4 shrink-0 {{ $announcement->priority === \App\Enums\AnnouncementPriority::Urgent ? 'text-red-600' : 'text-ember' }}" />
            <a href="{{ route('announcements.index') }}" class="min-w-0 flex-1 truncate font-medium hover:underline" wire:navigate>
                <strong>{{ $announcement->title }}:</strong> {{ Str::limit($announcement->body, 120) }}
            </a>
            <a href="{{ route('announcements.index') }}" class="hidden shrink-0 font-semibold text-ember hover:underline sm:block" wire:navigate>View all</a>
            <button wire:click="dismiss({{ $announcement->id }})" type="button" class="flex size-7 shrink-0 items-center justify-center rounded-full hover:bg-black/5" aria-label="Dismiss announcement">×</button>
        </div>
        </div>
    @endif
</div>
