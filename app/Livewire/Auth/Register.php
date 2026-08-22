<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        return Talent::query()->orderBy('category')->orderBy('name')->get();
    }

    public function register(): void
    {
        $isStudent = $this->accountType === 'student';

        $this->validate($this->validationRules($isStudent));

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
