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
 * @property string|null $batch
 * @property string|null $program
 * @property string $profile_type
 * @property int|null $primary_talent_id
 * @property Carbon|null $birthday
 * @property string|null $location
 * @property ExperienceLevel $experience_level
 * @property string|null $avatar_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'headline', 'bio', 'faculty', 'department', 'batch', 'program', 'profile_type', 'primary_talent_id', 'birthday', 'location', 'experience_level', 'avatar_path'])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'experience_level' => 'beginner',
        'profile_type' => 'General Student Account',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience_level' => ExperienceLevel::class,
            'birthday' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function primaryTalentModel(): BelongsTo
    {
        return $this->belongsTo(Talent::class, 'primary_talent_id');
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
        if ($this->primary_talent_id && $this->primaryTalentModel) {
            return $this->primaryTalentModel;
        }

        $favorite = $this->talents->first(
            fn (Talent $talent): bool => (bool) $talent->pivot->is_favorite,
        );

        return $favorite ?? $this->talents->first();
    }
}
