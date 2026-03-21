<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AttendanceStickerPrintSettingFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAttendanceStickerPrintSetting
 */
final class AttendanceStickerPrintSetting extends Model
{
    /** @use HasFactory<AttendanceStickerPrintSettingFactory> */
    use HasFactory;

    use HasUlids;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'width_mm' => 'float',
            'height_mm' => 'float',
            'margin_top_mm' => 'float',
            'margin_right_mm' => 'float',
            'margin_bottom_mm' => 'float',
            'margin_left_mm' => 'float',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
