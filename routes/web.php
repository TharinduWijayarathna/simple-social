<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Auth\AdminLogin;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Campus\Dashboard as CampusDashboard;
use App\Livewire\Campus\Rankings as CampusRankings;
use App\Livewire\Collaborations\Index as CollaborationsIndex;
use App\Livewire\Collaborations\Show as CollaborationsShow;
use App\Livewire\Events\Create as EventsCreate;
use App\Livewire\Events\Index as EventsIndex;
use App\Livewire\Events\Show as EventsShow;
use App\Livewire\Feed;
use App\Livewire\Leaderboard;
use App\Livewire\Portfolio\Create as PortfolioCreate;
use App\Livewire\Portfolio\Index as PortfolioIndex;
use App\Livewire\Portfolio\Show as PortfolioShow;
use App\Livewire\Profile\Edit as ProfileEdit;
use App\Livewire\Rankings;
use App\Livewire\Statuses\Create as StatusCreate;
use App\Livewire\Statuses\Show as StatusShow;
use App\Livewire\Students\Index as StudentsIndex;
use App\Livewire\Students\Show as StudentsShow;
use App\Livewire\Wearable\Glance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public home (campus/super admins redirected via student-only middleware)
Route::livewire('/', Feed::class)->middleware('student-only')->name('home');
Route::permanentRedirect('/feed', '/');
Route::permanentRedirect('/studio', '/');

// Guest-only routes
Route::middleware('guest')->group(function (): void {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
    Route::livewire('/admin/login', AdminLogin::class)->name('admin.login');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

// ── Campus Admin / Event Creation Routes ──
Route::middleware(['auth', 'role:campus_admin,super_admin'])->group(function (): void {
    Route::livewire('/campus', CampusDashboard::class)->name('campus.dashboard');
    Route::livewire('/campus/rankings', CampusRankings::class)->name('campus.rankings');
    Route::livewire('/events/create', EventsCreate::class)->name('events.create');
});

// ── Shared Authenticated Events Routes ──
Route::middleware('auth')->group(function (): void {
    Route::livewire('/events', EventsIndex::class)->name('events.index');
    Route::livewire('/events/{event}', EventsShow::class)->name('events.show');
});

// ── Student-only social routes (campus and super admins are redirected away) ──
Route::middleware(['auth', 'student-only'])->group(function (): void {
    Route::get('/profile', function () {
        return redirect()->route('students.show', auth()->user());
    })->name('profile.show');
    Route::livewire('/profile/edit', ProfileEdit::class)->name('profile.edit');
    Route::livewire('/portfolio', PortfolioIndex::class)->name('portfolio.index');
    Route::livewire('/portfolio/create', PortfolioCreate::class)->name('portfolio.create');
    Route::livewire('/portfolio/{item}', PortfolioShow::class)->name('portfolio.show');
    Route::livewire('/status/create', StatusCreate::class)->name('status.create');
    Route::livewire('/status/{status}', StatusShow::class)->name('status.show');
    Route::livewire('/students', StudentsIndex::class)->name('students.index');
    Route::livewire('/students/{user}', StudentsShow::class)->name('students.show');
    Route::livewire('/collaborations', CollaborationsIndex::class)->name('collaborations.index');
    Route::livewire('/collaborations/{collaboration}', CollaborationsShow::class)->name('collaborations.show');
    Route::livewire('/leaderboard', Leaderboard::class)->name('leaderboard');
    Route::livewire('/rankings', Rankings::class)->name('rankings');
    Route::livewire('/watch', Glance::class)->name('wearable.glance');
});

// ── Super admin panel routes ──
Route::middleware(['auth', 'role:super_admin'])->group(function (): void {
    Route::livewire('/admin', AdminDashboard::class)->name('admin.dashboard');
});
