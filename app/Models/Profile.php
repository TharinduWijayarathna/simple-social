<?php

namespace App\Models;

use App\Enums\ExperienceLevel;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $headline
 * @property string|null $bio
 * @property string|null $faculty
 * @property string|null $department
 * @property ExperienceLevel $experience_level
 * @property string|null $avatar_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'headline', 'bio', 'faculty', 'department', 'experience_level', 'avatar_path'])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'experience_level' => 'beginner',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience_level' => ExperienceLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function talents(): BelongsToMany
    {
        return $this->belongsToMany(Talent::class)
            ->withPivot('is_favorite')
            ->withTimestamps();
    }

    public function favoriteTalents(): BelongsToMany
    {
        return $this->talents()->wherePivot('is_favorite', true);
    }

    public function primaryTalent(): ?Talent
    {
        $favorite = $this->talents->first(
            fn (Talent $talent): bool => (bool) $talent->pivot->is_favorite,
        );

        return $favorite ?? $this->talents->first();
    }
}
