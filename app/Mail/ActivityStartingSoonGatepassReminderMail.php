<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\RateLimiterName;
use App\Models\Gatepass;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

final class ActivityStartingSoonGatepassReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Gatepass $gatepass)
    {
        $this->gatepass->loadMissing([
            'activity' => fn (BelongsTo $query): BelongsTo => $query->withoutGlobalScope(OrganizationScope::class),
            'child' => fn (BelongsTo $query): BelongsTo => $query->withTrashed(),
            'guardian' => fn (BelongsTo $query): BelongsTo => $query->withTrashed()->with('user'),
        ]);

        $this->afterCommit();
    }

    /** @return array<int, RateLimited> */
    public function middleware(): array
    {
        return [new RateLimited(RateLimiterName::ResendApi)];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(30);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activity Starting Soon - Gatepass Reminder',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.activity-starting-soon-gatepass-reminder',
            with: [
                'code' => $this->gatepass->code,
                'qrImageUrl' => $this->gatepass->getSignedQrImageUrl(),
                'gatepassUrl' => $this->gatepass->getSignedUrl(),
                'childName' => $this->gatepass->child->full_name,
                'guardianName' => $this->gatepass->guardian->full_name,
                'activityTitle' => $this->gatepass->activity->title,
                'activityStartsAt' => $this->gatepass->activity->starts_at,
                'activityLocation' => $this->gatepass->activity->location,
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }

}
