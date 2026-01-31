<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\KeeperInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class KeeperInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public KeeperInvitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Keeper Invitation - '.$this->invitation->organization->name,
        );
    }

    public function content(): Content
    {
        $organization = $this->invitation->organization;
        $acceptUrl = sprintf(
            '%s://%s.%s/admin/invitation/accept?token=%s',
            config('app.url_scheme', 'https'),
            $organization->slug,
            config('app.domain'),
            $this->invitation->token
        );

        return new Content(
            markdown: 'mail.keeper-invitation',
            with: [
                'userName' => $this->invitation->user->name,
                'organizationName' => $organization->name,
                'inviterName' => $this->invitation->invitedBy->name,
                'role' => $this->invitation->role->getLabel(),
                'acceptUrl' => $acceptUrl,
                'expiresAt' => $this->invitation->expires_at,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
