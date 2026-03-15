<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\DateTimePicker;

final class AppDateTimePicker
{
    public static function startsAt(string $field = 'starts_at', string $label = 'Starts at'): DateTimePicker
    {
        return DateTimePicker::make($field)->label($label)
            ->displayFormat('d M Y (h:i A)')
            ->required();
    }

    public static function endsAt(string $field = 'ends_at', string $label = 'Ends at'): DateTimePicker
    {
        return DateTimePicker::make($field)->label($label)
            ->displayFormat('d M Y (h:i A)')
            ->afterOrEqual('starts_at')
            ->validationMessages([
                'after_or_equal' => 'Ends at must be after or equal to Starts at.',
            ])
            ->required();
    }

    public static function publishAt(string $field = 'publish_at', string $label = 'Publish at'): DateTimePicker
    {
        return DateTimePicker::make($field)->label($label)
            ->displayFormat('d M Y (h:i A)')
            ->helperText('Leave empty to keep as draft. Set a date to publish the activity to guardians.');
    }
}
