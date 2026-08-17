<div class="mx-auto max-w-xl px-4 py-10 lg:px-0">
    <h1 class="font-display text-4xl">Hang a new piece</h1>
    <p class="mt-2 text-mist">Publish to the campus feed. Your talent room will pick up the look.</p>

    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">Title
            <input wire:model="title" class="field">
            @error('title') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Description
            <textarea wire:model="description" rows="4" class="field"></textarea>
        </label>
        <label class="flex flex-col gap-1 text-sm">Category
            <select wire:model="talent_id" class="field">
                <option value="">Select a category</option>
                @foreach ($talents as $talent)
                    <option value="{{ $talent->id }}">{{ $talent->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex flex-col gap-1 text-sm">Media type
            <select wire:model="media_type" class="field">
                <option value="image">Image</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="document">Document</option>
            </select>
        </label>
        <label class="flex flex-col gap-1 text-sm">File
            <input type="file" wire:model="file" class="field">
            <span wire:loading wire:target="file" class="text-mist">Uploading…</span>
            @error('file') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <button type="submit" class="btn-dark">Publish to feed</button>
    </form>
</div>
