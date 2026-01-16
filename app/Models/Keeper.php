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
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property string|null $permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $checkinAttendance
 * @property-read int|null $checkin_attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $checkoutAttendance
 * @property-read int|null $checkout_attendance_count
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\KeeperFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper withoutTrashed()
 *
 * @mixin \Eloquent
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
