<div class="mx-auto max-w-xl px-4 py-10 lg:px-0">
    <h1 class="font-display text-4xl">Post an event</h1>
    <p class="mt-2 text-mist">Campus admins can publish nights, exhibitions, and open calls.</p>
    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">Title
            <input wire:model="title" class="field">
            @error('title') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Description
            <textarea wire:model="description" rows="4" class="field"></textarea>
            @error('description') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Location
            <input wire:model="location" class="field">
        </label>
        <label class="flex flex-col gap-1 text-sm">Starts
            <input type="datetime-local" wire:model="starts_at" class="field">
            @error('starts_at') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Category
            <select wire:model="talent_id" class="field">
                <option value="">Any</option>
                @foreach ($talents as $talent)
                    <option value="{{ $talent->id }}">{{ $talent->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="is_published"> Publish now
        </label>
        <button type="submit" class="btn-dark">Save event</button>
    </form>
</div>
