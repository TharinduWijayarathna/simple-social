<?php

namespace App\Traits;

use App\Enums\Role;
use App\Models\Collaboration;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\User;
use App\Support\CampusScopeGuard;
use Illuminate\Database\Eloquent\Builder;

trait HasCampusScope
{
    /**
     * Boot the campus scope for the model.
     */
    public static function bootHasCampusScope(): void
    {
        static::addGlobalScope('campus_scope', function (Builder $builder) {
            if (CampusScopeGuard::$active) {
                return;
            }

            CampusScopeGuard::$active = true;

            try {
                if (! auth()->check()) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                if ($user->isSuperAdmin()) {
                    return;
                }

                $campusId = $user->role === Role::CampusAdmin ? $user->id : $user->campus_id;

                if ($campusId === null) {
                    return;
                }

                $modelClass = static::class;

                if ($modelClass === User::class) {
                    $builder->where(function (Builder $query) use ($campusId) {
                        $query->where('campus_id', $campusId)
                            ->orWhere('id', $campusId);
                    });
                } elseif ($modelClass === Event::class) {
                    $builder->where('organizer_id', $campusId);
                } elseif ($modelClass === PortfolioItem::class) {
                    $builder->whereHas('user', function (Builder $query) use ($campusId) {
                        $query->where('campus_id', $campusId);
                    });
                } elseif ($modelClass === Status::class) {
                    $builder->whereHas('user', function (Builder $query) use ($campusId) {
                        $query->where('campus_id', $campusId);
                    });
                } elseif ($modelClass === Collaboration::class) {
                    $builder->whereHas('owner', function (Builder $query) use ($campusId) {
                        $query->where('campus_id', $campusId);
                    });
                }
            } finally {
                CampusScopeGuard::$active = false;
            }
        });
    }
}
