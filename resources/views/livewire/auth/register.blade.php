<div>
    @if ($submitted)
        <div class="text-center">
            <div class="mx-auto mb-6 flex size-16 items-center justify-center rounded-full bg-ember/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-ember" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="font-display text-3xl">Application submitted</h1>
            @if ($accountType === 'campus')
                <p class="mt-3 text-mist">Your campus account is pending super admin approval. You'll be able to log in once it's approved.</p>
            @else
                <p class="mt-3 text-mist">Your account is pending campus approval. You'll be able to log in once your campus admin approves you.</p>
            @endif
            <a href="{{ route('login') }}" class="btn-dark mt-8 inline-block" wire:navigate>Back to sign in</a>
        </div>
    @else
        <h1 class="font-display text-4xl">Join campus</h1>
        <p class="mt-2 text-mist">Create your account to get started.</p>

        {{-- Account type tabs --}}
        <div class="mt-6 flex rounded-xl bg-ink/5 p-1">
            <button type="button"
                wire:click="$set('accountType', 'student')"
                class="flex-1 rounded-lg py-2 text-sm font-medium transition {{ $accountType === 'student' ? 'bg-white text-ink shadow-sm' : 'text-mist hover:text-ink' }}">
                Student
            </button>
            <button type="button"
                wire:click="$set('accountType', 'campus')"
                class="flex-1 rounded-lg py-2 text-sm font-medium transition {{ $accountType === 'campus' ? 'bg-white text-ink shadow-sm' : 'text-mist hover:text-ink' }}">
                Campus Admin
            </button>
        </div>

        @if ($accountType === 'campus')
            <p class="mt-3 rounded-lg bg-ember/8 px-4 py-3 text-sm text-ember">
                Campus admin accounts require super admin approval before you can log in.
            </p>
        @else
            <p class="mt-3 rounded-lg bg-ink/5 px-4 py-3 text-sm text-mist">
                Student accounts require campus admin approval before you can log in.
            </p>
        @endif

        <form wire:submit="register" class="mt-6 flex flex-col gap-4">

            <label class="flex flex-col gap-1 text-sm">
                Full name
                <input wire:model="name" type="text" class="field" required>
                @error('name') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            <label class="flex flex-col gap-1 text-sm">
                {{ $accountType === 'campus' ? 'Work email' : 'University email' }}
                <input wire:model="email" type="email" class="field" required>
                @error('email') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            @if ($accountType === 'student')
                <label class="flex flex-col gap-1 text-sm">
                    University / student ID
                    <input wire:model="universityId" type="text" class="field" placeholder="e.g. 2024CS0012" required>
                    @error('universityId') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                </label>

                <label class="flex flex-col gap-1 text-sm">
                    Your campus
                    <select wire:model="campusId" class="field" required>
                        <option value="">Select your campus…</option>
                        @foreach ($this->campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                    @error('campusId')
                        <span class="text-ember text-xs">{{ $message }}</span>
                    @enderror
                    @if ($this->campuses->isEmpty())
                        <span class="text-xs text-mist">No campuses available yet. Please check back later.</span>
                    @endif
                </label>
            @endif

            <label class="flex flex-col gap-1 text-sm">
                Password
                <input wire:model="password" type="password" class="field" required>
                @error('password') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            <label class="flex flex-col gap-1 text-sm">
                Confirm password
                <input wire:model="password_confirmation" type="password" class="field" required>
            </label>

            <button type="submit" class="btn-primary mt-2">
                {{ $accountType === 'campus' ? 'Request campus access' : 'Create your studio' }}
            </button>

            <p class="text-sm text-mist">Already here? <a href="{{ route('login') }}" class="text-ember" wire:navigate>Sign in</a></p>
        </form>
    @endif
</div>
