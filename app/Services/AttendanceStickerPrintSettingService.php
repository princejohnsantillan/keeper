<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AttendanceStickerPrintSetting;
use App\Models\User;
use App\Services\Contracts\AttendanceStickerPrintSettingServiceInterface;
use Illuminate\Support\Collection;

final class AttendanceStickerPrintSettingService implements AttendanceStickerPrintSettingServiceInterface
{
    /**
     * @return Collection<int, AttendanceStickerPrintSetting>
     */
    public function allForUser(User $user): Collection
    {
        return AttendanceStickerPrintSetting::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();
    }

    public function findForUser(User $user, string $settingId): ?AttendanceStickerPrintSetting
    {
        return AttendanceStickerPrintSetting::query()
            ->where('user_id', $user->id)
            ->where('id', $settingId)
            ->first();
    }

    /**
     * @param  array{
     *     width_mm: float,
     *     height_mm: float,
     *     margin_top_mm: float,
     *     margin_right_mm: float,
     *     margin_bottom_mm: float,
     *     margin_left_mm: float
     * }  $settings
     */
    public function saveForUser(User $user, string $name, array $settings): AttendanceStickerPrintSetting
    {
        return AttendanceStickerPrintSetting::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'name' => $name,
            ],
            $settings,
        );
    }
}
