<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Traits\HasCampusScope;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $university_id
 * @property int|null $campus_id
 * @property string|null $campus_name
 * @property string|null $campus_phone
 * @property string|null $campus_address
 * @property string|null $campus_website
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Role $role
 * @property UserStatus $status
 * @property int $xp
 * @property int|null $current_rank
 * @property int|null $previous_rank
 * @property-read int|null $talent_xp
 * @property-read int|null $published_posts_total
 * @property-read int|null $talent_likes_total
 * @property-read int|null $followers_total
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile|null $profile
 * @property-read User|null $campus
 */
#[Fillable(['name', 'email', 'password', 'role', 'status', 'university_id', 'campus_id', 'campus_name', 'campus_phone', 'campus_address', 'campus_website', 'xp', 'current_rank', 'previous_rank'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasCampusScope, HasFactory, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'student',
        'status' => 'approved',
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
            'status' => UserStatus::class,
            'xp' => 'integer',
            'current_rank' => 'integer',
            'previous_rank' => 'integer',
        ];
    }

    /**
     * The institution name for a campus (e.g. "ICBT"), falling back to
     * their personal name for accounts created before campus_name existed.
     */
    public function displayCampusName(): string
    {
        return $this->campus_name ?? $this->name;
    }

    public function initials(): string
    {
        $initials = Str::initials($this->isCampus() ? $this->displayCampusName() : $this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function avatarUrl(): ?string
    {
        if ($this->relationLoaded('profile')) {
            return $this->profile?->avatarUrl();
        }

        if ($this->preventsLazyLoading) {
            return null;
        }

        return $this->profile?->avatarUrl();
    }

    public function isStudent(): bool
    {
        return $this->role === Role::Student;
    }

    public function isCampus(): bool
    {
        return $this->role === Role::Campus;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SuperAdmin;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === UserStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === UserStatus::Rejected;
    }

    public function isOrganizer(): bool
    {
        return $this->isCampus();
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canOrganizeEvents(): bool
    {
        return $this->isCampus() || $this->isSuperAdmin();
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

    /**
     * The campus this student belongs to.
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'campus_id');
    }

    /**
     * Students that belong to this campus.
     */
    public function campusStudents(): HasMany
    {
        return $this->hasMany(User::class, 'campus_id');
    }

    public function campusPortfolioItems(): HasManyThrough
    {
        return $this->hasManyThrough(PortfolioItem::class, User::class, 'campus_id', 'user_id');
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

    /** @return HasMany<Announcement, $this> */
    public function campusAnnouncements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'campus_id');
    }

    /** @return BelongsToMany<Announcement, $this, Pivot, 'pivot'> */
    public function announcementEngagements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class)
            ->withoutGlobalScopes()
            ->withPivot(['read_at', 'dismissed_at'])
            ->withTimestamps();
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

    #[Scope]
    protected function pendingStudents(Builder $query): Builder
    {
        return $query->where('role', Role::Student)->where('status', UserStatus::Pending);
    }

    #[Scope]
    protected function pendingCampuses(Builder $query): Builder
    {
        return $query->where('role', Role::Campus)->where('status', UserStatus::Pending);
    }

    /**
     * Pending students belonging to a specific campus.
     */
    #[Scope]
    protected function pendingStudentsForCampus(Builder $query, int $campusId): Builder
    {
        return $query->where('role', Role::Student)
            ->where('status', UserStatus::Pending)
            ->where('campus_id', $campusId);
    }

    /**
     * Approved students belonging to a specific campus.
     */
    #[Scope]
    protected function approvedStudentsForCampus(Builder $query, int $campusId): Builder
    {
        return $query->where('role', Role::Student)
            ->where('status', UserStatus::Approved)
            ->where('campus_id', $campusId);
    }

    /**
     * Banned students belonging to a specific campus.
     */
    #[Scope]
    protected function bannedStudentsForCampus(Builder $query, int $campusId): Builder
    {
        return $query->where('role', Role::Student)
            ->where('status', UserStatus::Banned)
            ->where('campus_id', $campusId);
    }
}
