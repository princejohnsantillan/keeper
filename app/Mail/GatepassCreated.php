<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Gatepass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class GatepassCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Gatepass $gatepass) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activity Registration Confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.gatepass-created',
            with: [
                'code' => $this->gatepass->code,
                'childName' => $this->gatepass->child->full_name,
                'guardianName' => $this->gatepass->guardian->full_name,
                'activityTitle' => $this->gatepass->activity->title,
                'organizerName' => $this->gatepass->activity->organization->name,
                'organizerMessage' => $this->gatepass->activity->message?->content,
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
