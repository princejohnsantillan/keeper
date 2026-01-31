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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                'qrCode' => $this->generateQrCode(),
                'childName' => $this->gatepass->child->full_name,
                'guardianName' => $this->gatepass->guardian->full_name,
                'activityTitle' => $this->gatepass->activity->title,
                'activityStartsAt' => $this->gatepass->activity->starts_at,
                'activityLocation' => $this->gatepass->activity->location,
                'organizerName' => $this->gatepass->activity->organization?->name,
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

    private function generateQrCode(): string
    {
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($this->gatepass->code);

        return 'data:image/png;base64,'.base64_encode((string) $qrCode);
    }
}
