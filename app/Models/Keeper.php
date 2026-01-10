<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\KeeperFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperKeeper
 */
final class Keeper extends Model
{
    /** @use HasFactory<KeeperFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function checkinAttendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'checkin_keeper_id');
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function checkoutAttendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'checkout_keeper_id');
    }
}
