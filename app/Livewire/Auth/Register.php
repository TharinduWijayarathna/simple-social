<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Join VibeCraft')]
class Register extends Component
{
    public string $accountType = 'student';

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public bool $submitted = false;

    public function register(): void
    {
        $this->validate();

        $role = $this->accountType === 'campus' ? Role::CampusAdmin : Role::Student;

        User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $role,
            'status' => UserStatus::Pending,
        ]);

        // Do NOT log the user in — they must be approved first.
        $this->submitted = true;
    }
}
