<div class="mx-auto max-w-2xl px-4 py-10 lg:px-0">
    <p class="text-xs uppercase tracking-widest text-ember">{{ $collaboration->status->value }}</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $collaboration->title }}</h1>
    <p class="mt-2 text-mist">Led by {{ $collaboration->owner->name }}</p>
    <p class="mt-6 whitespace-pre-wrap">{{ $collaboration->description }}</p>

    @if (session('status'))
        <p class="mt-4 rounded-lg bg-gold/20 px-3 py-2 text-sm">{{ session('status') }}</p>
    @endif

    <h2 class="mt-8 font-semibold">Members</h2>
    <ul class="mt-2 flex flex-col gap-1 text-sm">
        @foreach ($collaboration->members as $member)
            <li wire:key="member-{{ $member->id }}">{{ $member->user->name }} · {{ $member->member_role }}</li>
        @endforeach
    </ul>

    @if (! auth()->user()->is($collaboration->owner))
        <form wire:submit="requestToJoin" class="mt-8 flex flex-col gap-2">
            <textarea wire:model="message" rows="3" class="rounded-lg border border-ink/15 bg-white px-3 py-2" placeholder="Why you want in"></textarea>
            <button type="submit" class="self-start rounded-lg bg-studio px-4 py-2 text-sm text-paper">Request to join</button>
        </form>
    @endif

    @if (auth()->user()->is($collaboration->owner) || auth()->user()->isAdmin())
        <h2 class="mt-8 font-semibold">Pending requests</h2>
        <ul class="mt-2 flex flex-col gap-3">
            @forelse ($collaboration->requests as $request)
                <li class="flex items-center justify-between rounded-xl bg-white p-3 text-sm" wire:key="req-{{ $request->id }}">
                    <span>{{ $request->user->name }} — {{ $request->message }}</span>
                    <span class="flex gap-2">
                        <button wire:click="respond({{ $request->id }}, true)" class="text-ember">Accept</button>
                        <button wire:click="respond({{ $request->id }}, false)" class="text-mist">Decline</button>
                    </span>
                </li>
            @empty
                <li class="text-sm text-mist">No pending requests.</li>
            @endforelse
        </ul>
    @endif
</div>
