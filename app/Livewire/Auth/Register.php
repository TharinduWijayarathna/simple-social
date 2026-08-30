<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Talent;
use App\Models\User;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Join VibeCraft')]
class Register extends Component
{
    public string $accountType = 'student';

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** Student card / university ID number */
    public string $universityId = '';

    /** ID of the selected campus admin (students only) */
    public ?int $campusId = null;

    /** Campus & Batch Details */
    public string $batch = '';

    public string $program = '';

    public string $faculty = '';

    public string $department = '';

    /** Profile Categorization */
    public string $profileType = '🎤 Performing Arts Creator Account';

    public ?int $primaryTalentId = null;

    public bool $submitted = false;

    /** One-time verification code sent to the student's email */
    public string $otp = '';

    public bool $otpSent = false;

    public bool $otpVerified = false;

    public ?string $otpError = null;

    public ?string $otpStatus = null;

    private const int OTP_TTL_MINUTES = 10;

    private const int OTP_RESEND_SECONDS = 60;

    /**
     * Load approved campus admins for the dropdown.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function campuses(): Collection
    {
        return User::query()
            ->where('role', Role::CampusAdmin)
            ->where('status', UserStatus::Approved)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Load available talents grouped by category.
     *
     * @return \Illuminate\Support\Collection<string, Collection<int, Talent>>
     */
    #[Computed]
    public function talentCategories(): \Illuminate\Support\Collection
    {
        return Talent::query()
            ->forCampus($this->campusId)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    /**
     * Load all talents for searchable dropdown.
     *
     * @return Collection<int, Talent>
     */
    #[Computed]
    public function allTalents(): Collection
    {
        return Talent::query()
            ->forCampus($this->campusId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function updatedEmail(): void
    {
        $this->otpSent = false;
        $this->otpVerified = false;
        $this->otp = '';
        $this->otpError = null;
        $this->otpStatus = null;
    }

    public function sendOtp(): void
    {
        $this->otpError = null;
        $this->otpStatus = null;

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        $throttleKey = "otp-resend:{$this->email}";

        if (Cache::has($throttleKey)) {
            $this->otpError = 'Please wait a moment before requesting another code.';

            return;
        }

        $code = (string) random_int(100000, 999999);

        Cache::put("otp:{$this->email}", $code, now()->addMinutes(self::OTP_TTL_MINUTES));
        Cache::put($throttleKey, true, now()->addSeconds(self::OTP_RESEND_SECONDS));

        (new AnonymousNotifiable)
            ->route('mail', $this->email)
            ->notify(new OtpVerificationNotification($code));

        $this->otpSent = true;
        $this->otpVerified = false;
        $this->otp = '';
        $this->otpStatus = 'A verification code has been sent to your email.';
    }

    public function verifyOtp(): void
    {
        $this->otpError = null;
        $this->otpStatus = null;

        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $cacheKey = "otp:{$this->email}";
        $expected = Cache::get($cacheKey);

        if ($expected === null) {
            $this->otpError = 'This code has expired. Please request a new one.';

            return;
        }

        if (! hash_equals($expected, $this->otp)) {
            $this->otpError = 'That code is incorrect. Please try again.';

            return;
        }

        Cache::forget($cacheKey);

        $this->otpVerified = true;
        $this->otpStatus = 'Email verified.';
    }

    public function register(): void
    {
        $isStudent = $this->accountType === 'student';

        $this->validate($this->validationRules($isStudent));

        if ($isStudent && ! $this->otpVerified) {
            $this->otpError = 'Please verify your email address before registering.';

            return;
        }

        $role = $isStudent ? Role::Student : Role::CampusAdmin;

        $user = User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $role,
            'status' => UserStatus::Pending,
            'university_id' => $isStudent ? $this->universityId : null,
            'campus_id' => $isStudent ? $this->campusId : null,
        ]);

        if ($isStudent) {
            $profile = $user->profile()->create([
                'batch' => $this->batch ?: null,
                'program' => $this->program ?: null,
                'faculty' => $this->faculty ?: null,
                'department' => $this->department ?: null,
                'profile_type' => $this->profileType,
                'primary_talent_id' => $this->primaryTalentId,
            ]);

            if ($this->primaryTalentId) {
                $profile->talents()->attach($this->primaryTalentId, ['is_favorite' => true]);
            }
        }

        // Do NOT log the user in — they must be approved first.
        $this->submitted = true;
    }

    /**
     * @return array<string, mixed>
     */
    private function validationRules(bool $isStudent): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($isStudent) {
            $rules['universityId'] = ['required', 'string', 'max:50'];
            $rules['campusId'] = ['required', 'integer', Rule::exists('users', 'id')->where('role', Role::CampusAdmin->value)->where('status', UserStatus::Approved->value)];
            $rules['batch'] = ['nullable', 'string', 'max:100'];
            $rules['program'] = ['nullable', 'string', 'max:255'];
            $rules['faculty'] = ['nullable', 'string', 'max:255'];
            $rules['department'] = ['nullable', 'string', 'max:255'];
            $rules['profileType'] = ['required', 'string', 'max:255'];
            $rules['primaryTalentId'] = ['nullable', 'integer', 'exists:talents,id'];
        }

        return $rules;
    }
}
