<div class="mx-auto max-w-xl px-4 py-8">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">About you</p>
    <h1 class="font-display text-4xl">Edit profile</h1>
    <p class="mt-2 text-mist">Short intro, birthday, and the talents you show on campus.</p>

    @if (session('status'))
        <p class="mt-4 rounded-2xl bg-gold/20 px-4 py-3 text-sm">{{ session('status') }}</p>
    @endif

    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">Name
            <input wire:model="name" class="field">
            @error('name') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Short description
            <input wire:model="headline" class="field" maxlength="160" placeholder="Painter · 2nd year · ICBT">
            @error('headline') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">About
            <textarea wire:model="bio" rows="4" class="field" placeholder="A little more about you"></textarea>
        </label>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="flex flex-col gap-1 text-sm">Birthday
                <input type="date" wire:model="birthday" class="field">
                @error('birthday') <span class="text-ember">{{ $message }}</span> @enderror
            </label>
            <label class="flex flex-col gap-1 text-sm">Lives in
                <input wire:model="location" class="field" placeholder="Colombo">
            </label>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="flex flex-col gap-1 text-sm">Faculty
                <input wire:model="faculty" class="field">
            </label>
            <label class="flex flex-col gap-1 text-sm">Department
                <input wire:model="department" class="field">
            </label>
        </div>

        <fieldset class="rounded-2xl border border-ink/10 bg-white p-4">
            <legend class="px-1 text-sm font-medium">Talents</legend>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($talents as $talent)
                    <label class="flex items-center gap-2 text-sm" wire:key="talent-{{ $talent->id }}">
                        <input type="checkbox" value="{{ $talent->id }}" wire:model="talent_ids">
                        {{ $talent->name }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <button type="submit" class="btn-dark">Save</button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-3">
        <a href="{{ route('profile.show') }}" class="text-sm text-mist" wire:navigate>Back to profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-mist hover:text-ink">Sign out</button>
        </form>
    </div>
</div>
