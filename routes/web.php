<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Campus\Dashboard as CampusDashboard;
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
use App\Livewire\Students\Index as StudentsIndex;
use App\Livewire\Students\Show as StudentsShow;
use App\Livewire\Wearable\Glance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Feed::class)->name('home');
Route::permanentRedirect('/feed', '/');
Route::permanentRedirect('/studio', '/');

Route::middleware('guest')->group(function (): void {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::livewire('/campus', CampusDashboard::class)->name('campus.dashboard');
    Route::get('/profile', function () {
        return redirect()->route('students.show', auth()->user());
    })->name('profile.show');
    Route::livewire('/profile/edit', ProfileEdit::class)->name('profile.edit');
    Route::livewire('/portfolio', PortfolioIndex::class)->name('portfolio.index');
    Route::livewire('/portfolio/create', PortfolioCreate::class)->name('portfolio.create');
    Route::livewire('/portfolio/{item}', PortfolioShow::class)->name('portfolio.show');
    Route::livewire('/students', StudentsIndex::class)->name('students.index');
    Route::livewire('/students/{user}', StudentsShow::class)->name('students.show');
    Route::livewire('/events', EventsIndex::class)->name('events.index');
    Route::livewire('/events/create', EventsCreate::class)->name('events.create');
    Route::livewire('/events/{event}', EventsShow::class)->name('events.show');
    Route::livewire('/collaborations', CollaborationsIndex::class)->name('collaborations.index');
    Route::livewire('/collaborations/{collaboration}', CollaborationsShow::class)->name('collaborations.show');
    Route::livewire('/leaderboard', Leaderboard::class)->name('leaderboard');
    Route::livewire('/watch', Glance::class)->name('wearable.glance');
    Route::livewire('/admin', AdminDashboard::class)->name('admin.dashboard');
});
