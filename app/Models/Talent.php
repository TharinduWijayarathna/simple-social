<?php

namespace App\Models;

use App\Enums\TalentTheme;
use Database\Factories\TalentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property TalentTheme $theme
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'description', 'theme'])]
class Talent extends Model
{
    /** @use HasFactory<TalentFactory> */
    use HasFactory;

    protected $table = 'talents';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'theme' => 'gallery',
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
