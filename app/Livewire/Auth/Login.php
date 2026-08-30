<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Sign in')]
class Login extends Component
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

        if ($user->status === UserStatus::Pending) {
            Auth::logout();
            $this->addError('email', 'Your account is pending approval. You will be notified once approved.');

            return;
        }

        if ($user->status === UserStatus::Rejected) {
            Auth::logout();
            $this->addError('email', 'Your account application was not approved. Please contact support.');

            return;
        }

        if ($user->status === UserStatus::Banned) {
            Auth::logout();
            $this->addError('email', 'Your account has been suspended. Please contact support.');

            return;
        }

        session()->regenerate();

        $redirect = match ($user->role) {
            Role::SuperAdmin => route('admin.dashboard'),
            Role::CampusAdmin => route('campus.dashboard'),
            default => route('home'),
        };

        $this->redirect($redirect);
    }
}
