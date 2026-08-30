<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int|null $campus_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'campus_id'])]
class TalentCategory extends Model
{
    use HasFactory;

    protected $table = 'talent_categories';

    public function campus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'campus_id');
    }

    /**
     * @param  Builder<TalentCategory>  $query
     * @return Builder<TalentCategory>
     */
    public function scopeForCampus(Builder $query, ?int $campusId): Builder
    {
        return $query->where(function (Builder $q) use ($campusId) {
            $q->whereNull('campus_id')
                ->when($campusId, fn ($sub) => $sub->orWhere('campus_id', $campusId));
        });
    }

    public function talents(): HasMany
    {
        return $this->hasMany(Talent::class, 'category', 'name');
    }
}
