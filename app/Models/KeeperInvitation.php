<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KeeperRole;
use Database\Factories\KeeperInvitationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperKeeperInvitation
 */
final class KeeperInvitation extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Factories\Factory<KeeperInvitation>>
     */
    protected static function newFactory(): KeeperInvitationFactory
    {
        return KeeperInvitationFactory::new();
    }

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'role' => KeeperRole::class,
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * @param  Builder<KeeperInvitation>  $query
     */
    public function scopePending(Builder $query): void
    {
        $query->whereNull('accepted_at');
    }

    /**
     * @param  Builder<KeeperInvitation>  $query
     */
    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<', now());
    }

    /**
     * @param  Builder<KeeperInvitation>  $query
     */
    public function scopeValid(Builder $query): void
    {
        $query->pending()->where('expires_at', '>=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted();
    }

    public function accept(): void
    {
        $this->accepted_at = now();
        $this->save();
    }
}
