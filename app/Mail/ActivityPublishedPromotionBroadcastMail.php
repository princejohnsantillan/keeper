<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\RateLimiterName;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

final class ActivityPublishedPromotionBroadcastMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Activity $activity)
    {
        $this->activity->loadMissing('organization');

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
            subject: 'New Activity Available: '.$this->activity->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.activity-published-promotion-broadcast',
            with: [
                'organizationName' => $this->activity->organization?->name,
                'activityTitle' => $this->activity->title,
                'activityDescription' => $this->activity->description,
                'activityStartsAt' => $this->activity->starts_at,
                'activityLocation' => $this->activity->location,
                'registerUrl' => route('filament.guardian.resources.activities.register', $this->activity),
                'signupUrl' => route('filament.guardian.auth.register'),
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
