<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin-panel')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'overview';

    #[Url(as: 'q')]
    public string $userSearch = '';

    public string $announcementMessage = '';

    public bool $announcementEnabled = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->announcementMessage = Setting::get('announcement_message', '') ?? '';
        $this->announcementEnabled = Setting::get('announcement_enabled') === '1';
    }

    public function moderate(int $reportId, string $status): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $report = Report::query()->findOrFail($reportId);
        $report->update(['status' => ReportStatus::from($status)]);

        if ($report->status === ReportStatus::Actioned) {
            $report->loadMissing('reportable');

            if ($report->reportable instanceof PortfolioItem) {
                $report->reportable->update(['published_at' => null]);
            }
        }
    }

    public function assignRole(int $userId, string $role): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);

        abort_if($user->is(auth()->user()), 403);

        $user->update(['role' => Role::from($role)]);
    }

    public function approveCampusAdmin(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::CampusAdmin)->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function rejectCampusAdmin(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::CampusAdmin)->findOrFail($userId);
        $user->update(['status' => UserStatus::Rejected]);
    }

    public function approveStudent(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::Student)->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function rejectStudent(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::Student)->findOrFail($userId);
        $user->update(['status' => UserStatus::Rejected]);
    }

    public function banStudent(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::Student)->findOrFail($userId);
        $user->update(['status' => UserStatus::Banned]);
    }

    public function unbanStudent(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::Student)->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function banUser(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);

        abort_if($user->is(auth()->user()), 403);

        $user->update(['status' => UserStatus::Banned]);
    }

    public function unbanUser(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function deleteUser(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);

        abort_if($user->is(auth()->user()), 403);

        $user->delete();
    }

    public function unpublishItem(int $itemId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        PortfolioItem::query()->findOrFail($itemId)->update(['published_at' => null]);
    }

    public function deleteItem(int $itemId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        PortfolioItem::query()->findOrFail($itemId)->delete();
    }

    public function saveSettings(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->validate([
            'announcementMessage' => ['nullable', 'string', 'max:280'],
        ]);

        Setting::set('announcement_message', $this->announcementMessage);
        Setting::set('announcement_enabled', $this->announcementEnabled ? '1' : '0');
    }

    public function render(): View
    {
        $pendingStudents = User::query()
            ->where('role', Role::Student)
            ->where('status', UserStatus::Pending)
            ->with(['campus', 'profile.primaryTalentModel'])
            ->latest()
            ->get();

        $approvedStudents = User::query()
            ->where('role', Role::Student)
            ->where('status', UserStatus::Approved)
            ->with(['campus', 'profile.primaryTalentModel'])
            ->latest()
            ->get();

        $bannedStudents = User::query()
            ->where('role', Role::Student)
            ->where('status', UserStatus::Banned)
            ->with(['campus', 'profile.primaryTalentModel'])
            ->latest()
            ->get();

        /** @var Collection<int, User> $users */
        $users = User::query()
            ->when($this->userSearch !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$this->userSearch}%")
                ->orWhere('email', 'like', "%{$this->userSearch}%")))
            ->latest()
            ->paginate(15, pageName: 'usersPage');

        return view('livewire.admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'totalStudents' => User::query()->students()->count(),
            'totalCampusAdmins' => User::query()->where('role', Role::CampusAdmin)->where('status', UserStatus::Approved)->count(),
            'totalItems' => PortfolioItem::query()->published()->count(),
            'totalEvents' => Event::query()->published()->count(),
            'totalBanned' => User::query()->where('status', UserStatus::Banned)->count(),
            'pendingCampusAdmins' => User::query()->pendingCampusAdmins()->with('profile')->latest()->get(),
            'approvedCampusAdmins' => User::query()
                ->where('role', Role::CampusAdmin)
                ->where('status', UserStatus::Approved)
                ->with('profile')
                ->latest()
                ->get(),
            'pendingStudents' => $pendingStudents,
            'approvedStudents' => $approvedStudents,
            'bannedStudents' => $bannedStudents,
            'categories' => Talent::query()
                ->withCount(['portfolioItems as published_items_count' => fn ($query) => $query->published()])
                ->orderByDesc('published_items_count')
                ->get(),
            'reports' => Report::query()->pending()->with(['reporter:id,name', 'reportable'])->latest()->limit(20)->get(),
            'users' => $users,
            'recentItems' => PortfolioItem::query()->published()->with('user:id,name')->latest('published_at')->limit(20)->get(),
            'newUsersLast7Days' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'newUsersLast30Days' => User::query()->where('created_at', '>=', now()->subDays(30))->count(),
        ]);
    }
}
