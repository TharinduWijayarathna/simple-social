<?php

namespace App\Models;

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $campus_id
 * @property string $title
 * @property string $body
 * @property AnnouncementPriority $priority
 * @property AnnouncementAudience $audience
 * @property string|null $audience_value
 * @property Carbon|null $starts_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $published_at
 * @property bool $is_pinned
 * @property string|null $link_url
 * @property string|null $link_label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['campus_id', 'title', 'body', 'priority', 'audience', 'audience_value', 'starts_at', 'expires_at', 'published_at', 'is_pinned', 'link_url', 'link_label'])]
class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'priority' => AnnouncementPriority::class,
            'audience' => AnnouncementAudience::class,
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'published_at' => 'datetime',
            'is_pinned' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'campus_id');
    }

    /** @return BelongsToMany<User, $this, Pivot, 'pivot'> */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withoutGlobalScopes()
            ->withPivot(['read_at', 'dismissed_at'])
            ->withTimestamps();
    }

    /**
     * @param  Builder<Announcement>  $query
     * @return Builder<Announcement>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    /**
     * @param  Builder<Announcement>  $query
     * @return Builder<Announcement>
     */
    #[Scope]
    protected function visibleTo(Builder $query, User $user): Builder
    {
        $user->loadMissing('profile');

        return $query
            ->where('campus_id', $user->campus_id)
            ->where(function (Builder $query) use ($user): void {
                $query->where('audience', AnnouncementAudience::Everyone)
                    ->when($user->profile?->batch, fn (Builder $query, string $batch) => $query->orWhere(fn (Builder $query) => $query
                        ->where('audience', AnnouncementAudience::Batch)
                        ->where('audience_value', $batch)))
                    ->when($user->profile?->faculty, fn (Builder $query, string $faculty) => $query->orWhere(fn (Builder $query) => $query
                        ->where('audience', AnnouncementAudience::Faculty)
                        ->where('audience_value', $faculty)))
                    ->when($user->profile?->department, fn (Builder $query, string $department) => $query->orWhere(fn (Builder $query) => $query
                        ->where('audience', AnnouncementAudience::Department)
                        ->where('audience_value', $department)))
                    ->when($user->profile?->program, fn (Builder $query, string $program) => $query->orWhere(fn (Builder $query) => $query
                        ->where('audience', AnnouncementAudience::Program)
                        ->where('audience_value', $program)));
            });
    }

    public function status(): string
    {
        if ($this->published_at === null) {
            return 'draft';
        }

        if (($this->starts_at ?? $this->published_at)->isFuture()) {
            return 'scheduled';
        }

        if ($this->expires_at?->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    public function isReadBy(User $user): bool
    {
        $recipient = $this->recipients->firstWhere('id', $user->id);

        return $recipient !== null && $recipient->getRelation('pivot')->getAttribute('read_at') !== null;
    }

    public function isDismissedBy(User $user): bool
    {
        $recipient = $this->recipients->firstWhere('id', $user->id);

        return $recipient !== null && $recipient->getRelation('pivot')->getAttribute('dismissed_at') !== null;
    }
}
