<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\RateLimiterName;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

final class InvitationCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invitation $invitation) {}

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
            subject: 'You\'re Invited to '.$this->invitation->activity->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invitation-created',
            with: [
                'inviteeName' => $this->invitation->invitee_fullname,
                'activityTitle' => $this->invitation->activity->title,
                'activityStartsAt' => $this->invitation->activity->starts_at,
                'activityEndsAt' => $this->invitation->activity->ends_at,
                'activityLocation' => $this->invitation->activity->location,
                'invitationCode' => $this->invitation->code,
                'organizationName' => $this->invitation->organization->name,
                'customMessage' => $this->invitation->message?->content,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
