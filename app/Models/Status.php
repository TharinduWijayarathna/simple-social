<?php

namespace App\Models;

use App\Traits\HasCampusScope;
use Database\Factories\StatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $caption
 * @property string $image_path
 * @property string $media_type
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'caption', 'image_path', 'media_type', 'expires_at'])]
class Status extends Model
{
    /** @use HasFactory<StatusFactory> */
    use HasCampusScope, HasFactory;

    protected $table = 'statuses';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'media_type' => 'image',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->expires_at->isFuture();
    }

    public function isVideo(): bool
    {
        if ($this->media_type === 'video') {
            return true;
        }

        $extension = strtolower(pathinfo($this->image_path, PATHINFO_EXTENSION));

        return in_array($extension, ['mp4', 'mov', 'webm', 'mkv', 'avi'], true);
    }

    public function imageUrl(): string
    {
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return 'https://picsum.photos/seed/vc-status'.$this->id.'/720/1280';
    }

    public function mediaUrl(): string
    {
        return $this->imageUrl();
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }
}
