<?php

declare(strict_types=1);

namespace App\Filament\Notifications;

use Filament\Notifications\Notification;

final class AppNotification
{
    // ─────────────────────────────────────────────────────────────────────────
    // Success Notifications
    // ─────────────────────────────────────────────────────────────────────────

    public static function registeredToActivity(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Successfully registered');
    }

    public static function guardiansUpdated(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Guardians updated');
    }

    public static function checkedIn(string $childName): Notification
    {
        return Notification::make()
            ->success()
            ->title('Checked in')
            ->body("{$childName} has been checked in successfully.");
    }

    public static function checkedOut(string $childName): Notification
    {
        return Notification::make()
            ->success()
            ->title('Checked out')
            ->body("{$childName} has been checked out successfully.");
    }

    public static function walkInRegistered(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Walk-in registered successfully');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Warning Notifications
    // ─────────────────────────────────────────────────────────────────────────

    public static function termsNotAgreed(): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Agreement Required')
            ->body('You must agree to the terms and conditions before requesting a gate pass.');
    }

    public static function alreadyCheckedIn(string $childName): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Already checked in')
            ->body("{$childName} is already checked in to this activity.");
    }

    public static function alreadyCheckedOut(string $childName): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Already checked out')
            ->body("{$childName} has already been checked out of this activity.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Danger/Error Notifications
    // ─────────────────────────────────────────────────────────────────────────

    public static function deleted(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Deleted');
    }

    public static function invalidGatepassCode(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Invalid code')
            ->body('The gatepass code does not match this activity.');
    }

    public static function noCheckInFound(string $childName): Notification
    {
        return Notification::make()
            ->danger()
            ->title('No check-in found')
            ->body("{$childName} has not been checked in to this activity.");
    }

    public static function childRelationshipRequired(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('At least one child relationship is required.');
    }

    public static function archived(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Archived')
            ->body('This item has been archived and can no longer be used.');
    }
}
