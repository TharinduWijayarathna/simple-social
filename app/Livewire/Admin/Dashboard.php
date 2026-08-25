<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::admin-panel')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    #[Url(as: 'tab')]
    public string $activeTab = 'overview';

    public function mount(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
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

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'totalStudents' => User::query()->students()->count(),
            'totalCampusAdmins' => User::query()->where('role', Role::CampusAdmin)->where('status', UserStatus::Approved)->count(),
            'totalItems' => PortfolioItem::query()->published()->count(),
            'totalEvents' => Event::query()->published()->count(),
            'pendingCampusAdmins' => User::query()->pendingCampusAdmins()->with('profile')->latest()->get(),
            'approvedCampusAdmins' => User::query()
                ->where('role', Role::CampusAdmin)
                ->where('status', UserStatus::Approved)
                ->with('profile')
                ->latest()
                ->get(),
            'categories' => Talent::query()
                ->withCount(['portfolioItems as published_items_count' => fn ($query) => $query->published()])
                ->orderByDesc('published_items_count')
                ->get(),
            'reports' => Report::query()->pending()->with(['reporter.profile', 'reportable'])->latest()->limit(20)->get(),
        ]);
    }
}
