<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GatepassFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * @mixin IdeHelperGatepass
 */
final class Gatepass extends Model
{
    /** @use HasFactory<GatepassFactory> */
    use HasFactory;

    use HasUlids;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pickup_reminder_sent_at' => 'immutable_datetime',
            'start_reminder_sent_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return BelongsTo<Child, $this>
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * @return BelongsTo<Activity, $this>
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * @return BelongsTo<TermAcceptance, $this>
     */
    public function termAcceptance(): BelongsTo
    {
        return $this->belongsTo(TermAcceptance::class);
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function getSignedUrl(): string
    {
        return URL::signedRoute('gatepass.show', ['gatepass' => $this->id]);
    }

    public function getSignedQrImageUrl(): string
    {
        return URL::signedRoute('gatepass.qr-image', ['gatepass' => $this->id]);
    }
}
