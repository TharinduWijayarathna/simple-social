<div class="mx-auto max-w-2xl px-4 py-10 lg:px-0">
    <h1 class="font-display text-4xl">Your studio</h1>
    <p class="mt-2 text-mist">Pick the talents that shape your profile. The favorite one sets the gallery look.</p>

    @if (session('status'))
        <p class="mt-4 rounded-2xl bg-gold/20 px-4 py-3 text-sm">{{ session('status') }}</p>
    @endif

    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">Name
            <input wire:model="name" class="field">
            @error('name') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Headline
            <input wire:model="headline" class="field">
        </label>
        <label class="flex flex-col gap-1 text-sm">Bio
            <textarea wire:model="bio" rows="4" class="field"></textarea>
        </label>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="flex flex-col gap-1 text-sm">Faculty
                <input wire:model="faculty" class="field">
            </label>
            <label class="flex flex-col gap-1 text-sm">Department
                <input wire:model="department" class="field">
            </label>
        </div>
        <label class="flex flex-col gap-1 text-sm">Experience
            <select wire:model="experience_level" class="field">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </label>

        <fieldset class="rounded-2xl border border-ink/10 bg-white p-4">
            <legend class="px-1 text-sm font-medium">Talent categories</legend>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($talents as $talent)
                    <label class="flex items-center gap-2 text-sm" wire:key="talent-{{ $talent->id }}">
                        <input type="checkbox" value="{{ $talent->id }}" wire:model="talent_ids">
                        {{ $talent->name }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset class="rounded-2xl border border-ink/10 bg-white p-4">
            <legend class="px-1 text-sm font-medium">Primary talent (profile look)</legend>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($talents as $talent)
                    <label class="flex items-center gap-2 text-sm" wire:key="fav-{{ $talent->id }}">
                        <input type="checkbox" value="{{ $talent->id }}" wire:model="favorite_talent_ids">
                        {{ $talent->name }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <button type="submit" class="btn-dark">Save profile</button>
    </form>
</div>
