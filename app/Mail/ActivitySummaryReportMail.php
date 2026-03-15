<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ActivitySummaryReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $stats
     */
    public function __construct(
        public Activity $activity,
        public array $stats,
        public string $csvContent,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Activity Summary Report: {$this->activity->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.activity-summary-report',
            with: $this->stats,
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $filename = str($this->activity->title)->slug().'-attendance-report.csv';

        return [
            Attachment::fromData(fn (): string => $this->csvContent, $filename)
                ->withMime('text/csv'),
        ];
    }
}
