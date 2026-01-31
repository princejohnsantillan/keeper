<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use Database\Factories\KeeperFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
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

    use HasUlids;
    use SoftDeletes;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'role' => KeeperRole::class,
            'status' => KeeperStatus::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === KeeperRole::Admin;
    }

    public function isGatekeeper(): bool
    {
        return $this->role === KeeperRole::Gatekeeper;
    }

    public function isActive(): bool
    {
        return $this->status === KeeperStatus::Active;
    }

    public function isInactive(): bool
    {
        return $this->status === KeeperStatus::Inactive;
    }

    public function isPending(): bool
    {
        return $this->status === KeeperStatus::Pending;
    }

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
