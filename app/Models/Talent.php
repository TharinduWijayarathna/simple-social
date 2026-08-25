<?php

namespace App\Models;

use App\Enums\TalentTheme;
use Database\Factories\TalentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property TalentTheme $theme
 * @property int|null $campus_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'category', 'description', 'theme', 'campus_id'])]
class Talent extends Model
{
    /** @use HasFactory<TalentFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Talent $talent) {
            $slug = Str::slug($talent->name);
            if ($talent->campus_id) {
                $slug .= '-'.$talent->campus_id;
            }
            $talent->slug = $slug;
        });
    }

    protected $table = 'talents';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'theme' => 'gallery',
        'category' => 'General User',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'theme' => TalentTheme::class,
        ];
    }

    /**
     * @param  Builder<Talent>  $query
     * @return Builder<Talent>
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * @param  Builder<Talent>  $query
     * @return Builder<Talent>
     */
    public function scopeForCampus(Builder $query, ?int $campusId): Builder
    {
        return $query->where(function (Builder $q) use ($campusId) {
            $q->whereNull('campus_id')
                ->when($campusId, fn ($sub) => $sub->orWhere('campus_id', $campusId));
        });
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'campus_id');
    }

    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class)
            ->withPivot('is_favorite')
            ->withTimestamps();
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
