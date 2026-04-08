<?php

declare(strict_types=1);

namespace App\Filament\Notifications;

use App\Enums\KeeperRole;
use Filament\Actions\Action;
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

    public static function tagsUpdated(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Tags updated');
    }

    public static function noteUpdated(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Note updated');
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

    public static function checkInOpened(string $activityTitle): Notification
    {
        return Notification::make()
            ->success()
            ->title('Check-in opened')
            ->body("Check-in is now open for {$activityTitle}.");
    }

    public static function checkInReopened(string $activityTitle): Notification
    {
        return Notification::make()
            ->success()
            ->title('Check-in reopened')
            ->body("Check-in has been reopened for {$activityTitle}.");
    }

    public static function checkInClosed(string $activityTitle): Notification
    {
        return Notification::make()
            ->success()
            ->title('Check-in closed')
            ->body("Check-in has been closed for {$activityTitle}.");
    }

    public static function walkInRegistered(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Walk-in registered successfully');
    }

    public static function keeperRoleUpdated(string $keeperName, KeeperRole $role): Notification
    {
        return Notification::make()
            ->success()
            ->title('Role updated')
            ->body("{$keeperName} is now {$role->getLabel()}.");
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

    public static function alreadyRegisteredForActivity(string $gatepassUrl): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Already registered')
            ->body('This child is already registered for this activity with this guardian.')
            ->actions([
                Action::make('view_gatepass')
                    ->label('View Gate Pass')
                    ->url($gatepassUrl),
            ]);
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

    public static function gatepassNotFound(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Gatepass not found')
            ->body('No gatepass was found with that code.');
    }

    public static function noCheckInFound(string $childName): Notification
    {
        return Notification::make()
            ->danger()
            ->title('No check-in found')
            ->body("{$childName} has not been checked in to this activity.");
    }

    public static function activityNotPublished(string $activityTitle): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Activity not published')
            ->body("{$activityTitle} is not published yet. Attendance actions are unavailable.");
    }

    public static function activityEnded(string $activityTitle): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Activity ended')
            ->body("{$activityTitle} has already ended. Check-in is no longer available.");
    }

    public static function checkInNotOpen(string $activityTitle): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Check-in not open')
            ->body("{$activityTitle} is not open for check-in yet.");
    }

    public static function checkInAlreadyClosed(string $activityTitle): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Check-in closed')
            ->body("{$activityTitle} is already closed for check-in.");
    }

    public static function childRelationshipRequired(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('At least one child relationship is required.');
    }

    public static function guardianRelationshipRequired(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('At least one guardian relationship is required.');
    }

    public static function archived(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Archived')
            ->body('This item has been archived and can no longer be used.');
    }

    public static function invalidInvitationCode(): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Invalid invitation code')
            ->body('The invitation code is invalid, not for this activity, or has already been used for a different child.');
    }

    public static function invitationSent(string $email): Notification
    {
        return Notification::make()
            ->success()
            ->title('Invitation sent')
            ->body("An invitation has been sent to {$email}.");
    }

    public static function error(string $message): Notification
    {
        return Notification::make()
            ->danger()
            ->title('Error')
            ->body($message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Keeper Invitation Notifications
    // ─────────────────────────────────────────────────────────────────────────

    public static function keeperInvited(string $email): Notification
    {
        return Notification::make()
            ->success()
            ->title('Invitation sent')
            ->body("An invitation has been sent to {$email}.");
    }

    public static function keeperAlreadyExists(string $email): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Already a member')
            ->body("{$email} is already a keeper for this organization.");
    }

    public static function invitationAlreadyPending(string $email): Notification
    {
        return Notification::make()
            ->warning()
            ->title('Invitation already sent')
            ->body("A pending invitation already exists for {$email}.");
    }

    public static function invitationAccepted(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Welcome!')
            ->body('You have successfully joined the organization.');
    }
}
