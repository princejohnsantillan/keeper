<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Ownership;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasOwnership
{
    /**
     * @return MorphMany<Ownership, $this>
     */
    public function ownerships(): MorphMany
    {
        return $this->morphMany(Ownership::class, 'model');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOwnedBy(Builder $query, Model $owner): Builder
    {
        return $query->whereHas('ownerships', function (Builder $ownershipQuery) use ($owner): void {
            $ownershipQuery
                ->where('owner_type', $owner->getMorphClass())
                ->where('owner_id', (string) $owner->getKey());
        });
    }
}
