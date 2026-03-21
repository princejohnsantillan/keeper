<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\AttendanceStickerPrintSetting;
use App\Models\User;
use Illuminate\Support\Collection;

interface AttendanceStickerPrintSettingServiceInterface
{
    /**
     * @return Collection<int, AttendanceStickerPrintSetting>
     */
    public function allForUser(User $user): Collection;

    public function findForUser(User $user, string $settingId): ?AttendanceStickerPrintSetting;

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
    public function saveForUser(User $user, string $name, array $settings): AttendanceStickerPrintSetting;
}
