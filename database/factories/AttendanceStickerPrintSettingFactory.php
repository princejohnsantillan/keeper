<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AttendanceStickerPrintSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceStickerPrintSetting>
 */
final class AttendanceStickerPrintSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'width_mm' => 90,
            'height_mm' => 30,
            'margin_top_mm' => 1,
            'margin_right_mm' => 1,
            'margin_bottom_mm' => 1,
            'margin_left_mm' => 1,
        ];
    }
}
