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

    public static function termsNotAgreed(): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Agreement Required')
            ->body('You must agree to the terms and conditions before requesting a gate pass.');
    }
}
