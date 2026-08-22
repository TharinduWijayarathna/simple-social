<?php

namespace App\Models;

use App\Enums\PortfolioMediaType;
use App\Enums\TalentTheme;
use App\Traits\HasCampusScope;
use Database\Factories\PortfolioItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $talent_id
 * @property string $title
 * @property string|null $description
 * @property PortfolioMediaType $media_type
 * @property string $file_path
 * @property string|null $thumbnail_path
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'talent_id', 'title', 'description', 'media_type', 'file_path', 'thumbnail_path', 'mime_type', 'file_size', 'published_at'])]
class PortfolioItem extends Model
{
    /** @use HasFactory<PortfolioItemFactory> */
    use HasCampusScope, HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'media_type' => PortfolioMediaType::class,
            'published_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->thumbnail_path === null) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function displayUrl(): string
    {
        $path = $this->thumbnail_path ?? $this->file_path;

        if ($path !== null && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $theme = $this->talent?->theme ?? TalentTheme::Gallery;

        [$width, $height] = match ($theme) {
            TalentTheme::Cinema => [1600, 900],
            TalentTheme::Vinyl => [900, 900],
            TalentTheme::Editorial => [800, 1200],
            default => [900, 1100],
        };

        return 'https://picsum.photos/seed/vibecraft'.$this->id.'/'.$width.'/'.$height;
    }

    public function feedAspectClass(): string
    {
        return ($this->talent?->theme ?? TalentTheme::Gallery)->feedAspectClass();
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
