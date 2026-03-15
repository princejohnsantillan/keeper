<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\RateLimiterName;
use App\Models\Keeper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

final class KeeperRoleChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Keeper $keeper,
        public string $previousRole,
        public string $newRole,
        public string $changedByName,
    ) {}

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
        $organizationName = $this->keeper->organization->name;

        return new Envelope(
            subject: 'Role Updated - '.$organizationName,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.keeper-role-changed',
            with: [
                'userName' => $this->keeper->user->name,
                'organizationName' => $this->keeper->organization->name,
                'previousRole' => $this->previousRole,
                'newRole' => $this->newRole,
                'changedByName' => $this->changedByName,
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
