<div class="flex w-full flex-col gap-4 px-4 py-8 text-left">
    <p class="text-center text-xs font-semibold uppercase tracking-[0.3em] text-yellow-300">VibeCraft</p>

    <section class="rounded-3xl border-2 border-white bg-black p-4">
        <p class="text-xs uppercase tracking-widest text-yellow-300">XP</p>
        <p class="text-4xl font-black">{{ $user->xp }}</p>
        <p class="text-sm">Rank {{ $user->current_rank ?? '—' }} · {{ $user->rankChange() >= 0 ? '▲' : '▼' }}{{ abs($user->rankChange()) }}</p>
    </section>

    @if ($latestItem)
        <section class="rounded-3xl bg-yellow-300 p-4 text-black">
            <p class="text-xs font-bold uppercase">Latest work</p>
            <p class="text-lg font-black">{{ $latestItem->title }}</p>
        </section>
    @endif

    @if ($nextEvent)
        <section class="rounded-3xl border-2 border-yellow-300 p-4">
            <p class="text-xs uppercase tracking-widest">Next event</p>
            <p class="font-bold">{{ $nextEvent->title }}</p>
            <p class="text-sm">{{ $nextEvent->starts_at->diffForHumans() }}</p>
            <button wire:click="rsvp({{ $nextEvent->id }})" class="mt-2 w-full rounded-full bg-yellow-300 py-2 font-black text-black">Confirm RSVP</button>
        </section>
    @endif

    <section class="rounded-3xl bg-white p-4 text-black">
        <p class="text-xs font-bold uppercase">Top 5</p>
        <ol class="mt-2 space-y-1 text-sm font-semibold">
            @foreach ($topFive as $student)
                <li wire:key="w-{{ $student->id }}">{{ $loop->iteration }}. {{ $student->name }}</li>
            @endforeach
        </ol>
    </section>

    <section class="flex flex-col gap-2">
        @foreach ($notifications as $notification)
            <article class="rounded-3xl border border-white p-3" wire:key="n-{{ $notification->id }}">
                <p class="font-bold">{{ $notification->data['title'] ?? 'Update' }}</p>
                <div class="mt-2 flex flex-wrap gap-2 text-xs font-black uppercase">
                    @if (($notification->data['action'] ?? null) === 'like')
                        <button wire:click="likeNotification('{{ $notification->id }}')" class="rounded-full bg-yellow-300 px-3 py-1 text-black">Like</button>
                    @endif
                    @if (($notification->data['type'] ?? null) === 'collaboration_request')
                        <button wire:click="acceptNotification('{{ $notification->id }}')" class="rounded-full bg-yellow-300 px-3 py-1 text-black">Accept</button>
                        <button wire:click="declineNotification('{{ $notification->id }}')" class="rounded-full border px-3 py-1">Decline</button>
                    @endif
                    <button wire:click="dismissNotification('{{ $notification->id }}')" class="rounded-full border px-3 py-1">Dismiss</button>
                </div>
            </article>
        @endforeach
    </section>
</div>
