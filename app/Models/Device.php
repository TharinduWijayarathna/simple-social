<?php

namespace App\Models;

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use Database\Factories\DeviceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property DeviceType $type
 * @property DevicePlatform $platform
 * @property string $token_hash
 * @property string|null $push_token
 * @property Carbon|null $last_seen_at
 * @property Carbon|null $revoked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'name', 'type', 'platform', 'token_hash', 'push_token', 'last_seen_at', 'revoked_at'])]
#[Hidden(['token_hash'])]
class Device extends Model
{
    /** @use HasFactory<DeviceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DeviceType::class,
            'platform' => DevicePlatform::class,
            'last_seen_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function revoke(): void
    {
        $this->forceFill(['revoked_at' => now()])->save();
    }

    public function touchLastSeen(): void
    {
        static::query()->whereKey($this->id)->update(['last_seen_at' => now()]);
    }

    /**
     * @return array{device: Device, plain_text_token: string}
     */
    public static function issue(User $user, string $name, DeviceType $type, DevicePlatform $platform, ?string $pushToken = null): array
    {
        $plainTextToken = Str::random(40);

        $device = $user->devices()->create([
            'name' => $name,
            'type' => $type,
            'platform' => $platform,
            'token_hash' => hash('sha256', $plainTextToken),
            'push_token' => $pushToken,
            'last_seen_at' => now(),
        ]);

        return [
            'device' => $device,
            'plain_text_token' => $device->id.'|'.$plainTextToken,
        ];
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }
}
