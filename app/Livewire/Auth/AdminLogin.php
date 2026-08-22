<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts::admin-guest')]
#[Title('Admin sign in')]
class AdminLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], remember: true)) {
            $this->addError('email', 'These credentials do not match our records.');

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== Role::SuperAdmin) {
            Auth::logout();
            $this->addError('email', 'This portal is for super administrators only.');

            return;
        }

        session()->regenerate();

        $this->redirect(route('admin.dashboard'));
    }
}
