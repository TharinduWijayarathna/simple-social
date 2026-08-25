<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $campus_id
 * @property int $talent_id
 * @property string $title
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $campus
 * @property-read Talent $talent
 */
#[Fillable(['campus_id', 'talent_id', 'title', 'is_active'])]
class CampusRanking extends Model
{
    public function campus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'campus_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}
