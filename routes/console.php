<?php

declare(strict_types=1);

use App\Actions\SendEndedActivityPickupEmailsAction;
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
