<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperGatepass
 *
 * @property int $id
 * @property int $guardian_id
 * @property int $child_id
 * @property int $activity_id
 * @property int|null $term_acceptance_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity $activity
 * @property-read \App\Models\Child $child
 * @property-read \App\Models\Guardian $guardian
 * @property-read \App\Models\TermAcceptance|null $termAcceptance
 *
 * @method static \Database\Factories\GatepassFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereTermAcceptanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class Gatepass extends Model
{
    /** @use HasFactory<\Database\Factories\GatepassFactory> */
    use HasFactory;

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
}
