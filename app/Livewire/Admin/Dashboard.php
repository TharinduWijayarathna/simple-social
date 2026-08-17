<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Models\Event;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Super admin')]
class Dashboard extends Component
{
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

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'users' => User::query()->count(),
            'students' => User::query()->students()->count(),
            'campusAdmins' => User::query()->where('role', Role::CampusAdmin)->count(),
            'items' => PortfolioItem::query()->published()->count(),
            'likes' => Like::query()->count(),
            'events' => Event::query()->published()->count(),
            'categories' => Talent::query()
                ->withCount(['portfolioItems as published_items_count' => fn ($query) => $query->published()])
                ->orderByDesc('published_items_count')
                ->get(),
            'reports' => Report::query()->pending()->with(['reporter:id,name', 'reportable'])->latest()->limit(20)->get(),
            'staff' => User::query()
                ->whereIn('role', [Role::CampusAdmin, Role::SuperAdmin, Role::Student])
                ->latest()
                ->limit(12)
                ->get(),
        ]);
    }
}
