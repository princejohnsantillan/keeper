<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $attendee_code
 * @property int $activity_id
 * @property int $child_id
 * @property int|null $checkin_keeper_id
 * @property int|null $checkin_gatepass_id
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 * @property int|null $checkout_keeper_id
 * @property int|null $checkout_gatepass_id
 * @property \Illuminate\Support\Carbon|null $checked_out_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity $activity
 * @property-read \App\Models\Gatepass|null $checkinGatepass
 * @property-read \App\Models\Keeper|null $checkinKeeper
 * @property-read \App\Models\Gatepass|null $checkoutGatepass
 * @property-read \App\Models\Keeper|null $checkoutKeeper
 * @property-read \App\Models\Child $child
 *
 * @method static \Database\Factories\AttendanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAttendeeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckedOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckinGatepassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckinKeeperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckoutGatepassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckoutKeeperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 * @mixin IdeHelperAttendance
 */
final class Attendance extends Pivot
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    protected $table = 'attendances';

    public $incrementing = true;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Activity, $this> */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /** @return BelongsTo<Child, $this> */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /** @return BelongsTo<Keeper, $this> */
    public function checkinKeeper(): BelongsTo
    {
        return $this->belongsTo(Keeper::class, 'checkin_keeper_id');
    }

    /** @return BelongsTo<Gatepass, $this> */
    public function checkinGatepass(): BelongsTo
    {
        return $this->belongsTo(Gatepass::class, 'checkin_gatepass_id');
    }

    /** @return BelongsTo<Keeper, $this> */
    public function checkoutKeeper(): BelongsTo
    {
        return $this->belongsTo(Keeper::class, 'checkout_keeper_id');
    }

    /** @return BelongsTo<Gatepass, $this> */
    public function checkoutGatepass(): BelongsTo
    {
        return $this->belongsTo(Gatepass::class, 'checkout_gatepass_id');
    }
}
