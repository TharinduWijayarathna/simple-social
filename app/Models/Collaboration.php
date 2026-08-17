<?php

namespace App\Models;

use App\Enums\CollaborationStatus;
use Database\Factories\CollaborationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $owner_id
 * @property int|null $talent_id
 * @property string $title
 * @property string $description
 * @property CollaborationStatus $status
 * @property string|null $credit_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['owner_id', 'talent_id', 'title', 'description', 'status', 'credit_notes'])]
class Collaboration extends Model
{
    /** @use HasFactory<CollaborationFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'open',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CollaborationStatus::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CollaborationMember::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(CollaborationRequest::class);
    }
}
