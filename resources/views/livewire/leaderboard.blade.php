<div class="px-4 py-8 lg:px-10">
    <h1 class="font-display text-4xl">Campus leaderboard</h1>
    <p class="mt-1 text-mist">XP from likes, follows, comments, and showing up to events.</p>

    <ol class="mt-6 divide-y divide-ink/10 overflow-hidden rounded-[1.5rem] bg-white">
        @foreach ($students as $student)
            <li class="flex items-center justify-between px-5 py-4" wire:key="lb-{{ $student->id }}">
                <div>
                    <p class="font-medium">{{ $student->current_rank ?? $loop->iteration }}. {{ $student->name }}</p>
                    <p class="text-sm text-mist">{{ $student->profile?->faculty }}</p>
                </div>
                <p class="text-ember">{{ $student->xp }} XP</p>
            </li>
        @endforeach
    </ol>

    <div class="mt-6">{{ $students->links() }}</div>
</div>
