<?php

namespace App\Models;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperAttendance
 */
final class Attendance extends Pivot
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    public $incrementing = true;

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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
