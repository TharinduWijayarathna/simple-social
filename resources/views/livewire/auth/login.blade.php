<div>
    <h1 class="font-display text-4xl">Sign in</h1>
    <p class="mt-2 text-mist">Enter the campus gallery with your student account.</p>

    <form wire:submit="login" class="mt-8 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">
            Email
            <input wire:model="email" type="email" class="field" required>
            @error('email') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">
            Password
            <input wire:model="password" type="password" class="field" required>
            @error('password') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <button type="submit" class="btn-dark">
            Open the feed
        </button>
        <p class="text-sm text-mist">New on campus? <a href="{{ route('register') }}" class="text-ember" wire:navigate>Create a studio</a></p>
    </form>
</div>
