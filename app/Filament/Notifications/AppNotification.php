<?php

declare(strict_types=1);

namespace App\Filament\Notifications;

use Filament\Notifications\Notification;

final class AppNotification
{
    public static function registeredToActivity(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Successfully registered');
    }
}
