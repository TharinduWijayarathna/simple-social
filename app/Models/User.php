<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Role $role
 * @property int $xp
 * @property int|null $current_rank
 * @property int|null $previous_rank
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile|null $profile
 */
#[Fillable(['name', 'email', 'password', 'role', 'xp', 'current_rank', 'previous_rank'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'student',
        'xp' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'xp' => 'integer',
            'current_rank' => 'integer',
            'previous_rank' => 'integer',
        ];
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function isStudent(): bool
    {
        return $this->role === Role::Student;
    }

    public function isCampusAdmin(): bool
    {
        return $this->role === Role::CampusAdmin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SuperAdmin;
    }

    public function isOrganizer(): bool
    {
        return $this->isCampusAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canOrganizeEvents(): bool
    {
        return $this->isCampusAdmin() || $this->isSuperAdmin();
    }

    public function rankChange(): int
    {
        if ($this->current_rank === null || $this->previous_rank === null) {
            return 0;
        }

        return $this->previous_rank - $this->current_rank;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function xpEvents(): HasMany
    {
        return $this->hasMany(XpEvent::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function eventApplications(): HasMany
    {
        return $this->hasMany(EventApplication::class);
    }

    public function ownedCollaborations(): HasMany
    {
        return $this->hasMany(Collaboration::class, 'owner_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    #[Scope]
    protected function students(Builder $query): Builder
    {
        return $query->where('role', Role::Student);
    }

    #[Scope]
    protected function ranked(Builder $query): Builder
    {
        return $query->whereNotNull('current_rank')->orderBy('current_rank');
    }
}
