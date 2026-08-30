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
        <h1 class="font-display text-4xl">Register as Student or Campus</h1>
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

            <label class="flex flex-col gap-1 text-sm font-medium">
                Full name
                <input wire:model="name" type="text" class="field" placeholder="e.g. Alex Morgan" required>
                @error('name') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            <label class="flex flex-col gap-1 text-sm font-medium">
                {{ $accountType === 'campus' ? 'Work email' : 'University email' }}
                <input wire:model.live="email" type="email" class="field" placeholder="alex@campus.edu" {{ $otpSent && !$otpVerified ? 'readonly' : '' }} required>
                @error('email') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            @if ($accountType === 'student')
                {{-- Email OTP verification --}}
                <div class="p-4 rounded-2xl bg-ink/5 border border-ink/8 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-mist">Email Verification</h3>

                    @if ($otpVerified)
                        <p class="flex items-center gap-2 text-sm text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $email }} is verified.
                        </p>
                    @else
                        @if (! $otpSent)
                            <button type="button" wire:click="sendOtp" wire:loading.attr="disabled" wire:target="sendOtp" class="btn-dark text-sm">
                                <span wire:loading.remove wire:target="sendOtp">Send verification code</span>
                                <span wire:loading wire:target="sendOtp">Sending…</span>
                            </button>
                        @else
                            <div class="flex flex-col gap-2 md:flex-row md:items-start">
                                <label class="flex flex-1 flex-col gap-1 text-sm font-medium">
                                    Verification code
                                    <input wire:model="otp" type="text" inputmode="numeric" maxlength="6" class="field" placeholder="6-digit code">
                                    @error('otp') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                                </label>
                                <div class="flex gap-2 md:mt-6">
                                    <button type="button" wire:click="verifyOtp" wire:loading.attr="disabled" wire:target="verifyOtp" class="btn-primary text-sm whitespace-nowrap">Verify</button>
                                    <button type="button" wire:click="sendOtp" wire:loading.attr="disabled" wire:target="sendOtp" class="text-sm text-mist underline whitespace-nowrap">Resend</button>
                                </div>
                            </div>
                        @endif

                        @if ($otpStatus)
                            <p class="text-xs text-emerald-600">{{ $otpStatus }}</p>
                        @endif
                        @if ($otpError)
                            <p class="text-xs text-ember">{{ $otpError }}</p>
                        @endif
                    @endif
                </div>
            @endif

            @if ($accountType === 'student')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex flex-col gap-1 text-sm font-medium">
                        University / Student ID
                        <input wire:model="universityId" type="text" class="field" placeholder="e.g. 2024CS0012" required>
                        @error('universityId') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                    </label>

                    <label class="flex flex-col gap-1 text-sm font-medium">
                        Your Campus
                        <select wire:model="campusId" class="field" required>
                            <option value="">Select your campus…</option>
                            @foreach ($this->campuses as $campus)
                                <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        </select>
                        @error('campusId') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                    </label>
                </div>

                {{-- Campus & Academic Details --}}
                <div class="p-4 rounded-2xl bg-wall/60 border border-ink/8 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ember">Campus & Program Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex flex-col gap-1 text-xs font-medium text-ink">
                            Batch / Intake Year
                            <input wire:model="batch" type="text" class="field text-xs" placeholder="e.g. Batch 2024 / 2024-2025">
                            @error('batch') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                        </label>

                        <label class="flex flex-col gap-1 text-xs font-medium text-ink">
                            Degree Program / Course
                            <input wire:model="program" type="text" class="field text-xs" placeholder="e.g. BSc Software Engineering">
                            @error('program') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex flex-col gap-1 text-xs font-medium text-ink">
                            Faculty
                            <input wire:model="faculty" type="text" class="field text-xs" placeholder="e.g. Faculty of Computing">
                            @error('faculty') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                        </label>

                        <label class="flex flex-col gap-1 text-xs font-medium text-ink">
                            Department
                            <input wire:model="department" type="text" class="field text-xs" placeholder="e.g. Software Engineering">
                            @error('department') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </div>

                {{-- Profile Categorization --}}
                <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-700">Profile Categorization</h3>

                    <label class="flex flex-col gap-1 text-xs font-medium text-ink">
                        What type of creator profile is this?
                        <select wire:model="profileType" class="field text-xs" required>
                            <option value="🎤 Performing Arts Creator Account">Performing Arts Creator Account (Singing, Music, Dance, Comedy)</option>
                            <option value="🎨 Creative & Visual Arts Creator Account">Creative & Visual Arts Creator Account (Art, Photo, Design)</option>
                            <option value="🏆 Sports & Physical Creator Account">Sports & Physical Creator Account (Cricket, Football, Yoga)</option>
                            <option value="✨ Unique & Hidden Talents Creator Account">Unique & Hidden Talents Creator Account (Cooking, Magic, Chess)</option>
                            <option value="👤 General Student Account">General Student Account</option>
                        </select>
                        @error('profileType') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                    </label>

                    <div>
                        <label class="block text-xs font-medium text-ink mb-1">Primary Talent / Main Focus</label>
                        <x-searchable-talent-select wire:model="primaryTalentId" :talents="$this->allTalents" :selectedId="$primaryTalentId" :activeCategory="$profileType" placeholder="Type to search primary talent (e.g. Singing, Photography, Cricket)..." />
                        @error('primaryTalentId') <span class="text-ember text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            <label class="flex flex-col gap-1 text-sm font-medium">
                Password
                <input wire:model="password" type="password" class="field" required>
                @error('password') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            <label class="flex flex-col gap-1 text-sm font-medium">
                Confirm password
                <input wire:model="password_confirmation" type="password" class="field" required>
            </label>

            <button type="submit" class="btn-primary mt-2" {{ $accountType === 'student' && ! $otpVerified ? 'disabled' : '' }}>
                {{ $accountType === 'campus' ? 'Request campus access' : 'Create your studio' }}
            </button>
            @if ($accountType === 'student' && ! $otpVerified)
                <p class="text-xs text-mist -mt-2">Verify your email above to enable registration.</p>
            @endif

            <p class="text-sm text-mist">Already here? <a href="{{ route('login') }}" class="text-ember" wire:navigate>Sign in</a></p>
        </form>
    @endif
</div>
