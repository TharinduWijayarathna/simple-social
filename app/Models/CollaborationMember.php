<?php

namespace App\Models;

use Database\Factories\CollaborationMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $collaboration_id
 * @property int $user_id
 * @property string $member_role
 * @property string|null $credit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['collaboration_id', 'user_id', 'member_role', 'credit'])]
class CollaborationMember extends Model
{
    /** @use HasFactory<CollaborationMemberFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'member_role' => 'member',
    ];

    public function collaboration(): BelongsTo
    {
        return $this->belongsTo(Collaboration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
