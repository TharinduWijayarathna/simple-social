<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Enums\UserStatus;
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

    public function register(): void
    {
        $isStudent = $this->accountType === 'student';

        $this->validate($this->validationRules($isStudent));

        $role = $isStudent ? Role::Student : Role::CampusAdmin;

        User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $role,
            'status' => UserStatus::Pending,
            'university_id' => $isStudent ? $this->universityId : null,
            'campus_id' => $isStudent ? $this->campusId : null,
        ]);

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
        }

        return $rules;
    }
}
