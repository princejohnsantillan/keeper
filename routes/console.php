<?php

declare(strict_types=1);

use App\Actions\SendActivitySummaryReportsAction;
use App\Actions\SendEndedActivityPickupEmailsAction;
use App\Actions\SendPublishedActivityPromotionBroadcastAction;
use App\Actions\SendStartingSoonGatepassEmailsAction;
use App\Actions\TrashExpiredGatepassesAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('activities:send-ended-pickup-reminders', function (SendEndedActivityPickupEmailsAction $sendEndedActivityPickupEmailsAction): void {
    $queuedEmails = $sendEndedActivityPickupEmailsAction();

    $this->info("Queued {$queuedEmails} pickup reminder email(s).");
})->purpose('Queue pickup reminder emails for activities ending within 15 minutes')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('activities:send-starting-soon-gatepass-reminders', function (SendStartingSoonGatepassEmailsAction $sendStartingSoonGatepassEmailsAction): void {
    $queuedEmails = $sendStartingSoonGatepassEmailsAction();

    $this->info("Queued {$queuedEmails} starting-soon gatepass reminder email(s).");
})->purpose('Queue gatepass reminder emails for activities starting within 15 minutes')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('activities:send-summary-reports', function (SendActivitySummaryReportsAction $sendActivitySummaryReportsAction): void {
    $queuedEmails = $sendActivitySummaryReportsAction();

    $this->info("Queued {$queuedEmails} activity summary report email(s).");
})->purpose('Queue activity summary report emails for activities that ended 1+ hour ago')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('activities:send-published-promotion-broadcasts', function (SendPublishedActivityPromotionBroadcastAction $sendPublishedActivityPromotionBroadcastAction): void {
    $queuedBroadcasts = $sendPublishedActivityPromotionBroadcastAction();

    $this->info("Queued {$queuedBroadcasts} activity promotion broadcast(s).");
})->purpose('Queue promotion broadcasts for published activities that have not yet been sent')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('gatepasses:trash-expired', function (TrashExpiredGatepassesAction $trashExpiredGatepassesAction): void {
    $trashed = $trashExpiredGatepassesAction();

    $this->info("Trashed {$trashed} expired gatepass(es).");
})->purpose('Soft-delete gatepasses whose activity ended more than 24 hours ago')
    ->everyMinute()
    ->withoutOverlapping();
