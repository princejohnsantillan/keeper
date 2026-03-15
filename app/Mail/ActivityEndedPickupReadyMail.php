<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Gatepass;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class ActivityEndedPickupReadyMail extends Mailable implements ShouldQueue
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

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activity Ending Soon - Prepare for Pickup',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.activity-ended-pickup-ready',
            with: [
                'code' => $this->gatepass->code,
                'qrCode' => $this->generateQrCode(),
                'childName' => $this->gatepass->child->full_name,
                'guardianName' => $this->gatepass->guardian->full_name,
                'activityTitle' => $this->gatepass->activity->title,
                'activityEndsAt' => $this->gatepass->activity->ends_at,
                'activityLocation' => $this->gatepass->activity->location,
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }

    private function generateQrCode(): string
    {
        /** @var HtmlString|string $qrCode */
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($this->gatepass->id);

        return 'data:image/png;base64,'.base64_encode($qrCode instanceof HtmlString ? $qrCode->toHtml() : $qrCode);
    }
}
