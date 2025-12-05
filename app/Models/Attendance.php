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

    /** @return BelongsTo<User, $this> */
    public function checkedInProcessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checkin_processed_by');
    }

    /** @return BelongsTo<Keeper, $this> */
    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(Keeper::class, 'checked_in_by');
    }

    /** @return BelongsTo<User, $this> */
    public function checkedOutProcessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checkout_processed_by');
    }

    /** @return BelongsTo<Keeper, $this> */
    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(Keeper::class, 'checked_out_by');
    }
}
