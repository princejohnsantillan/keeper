<?php

namespace App\Models;

use Database\Factories\ChildFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperChild
 */
final class Child extends Model
{
    /** @use HasFactory<ChildFactory> */
    use HasFactory;

    /** @return BelongsToMany<Keeper, $this> */
    public function keepers(?bool $authorized = null): BelongsToMany
    {
        $keepers = $this->belongsToMany(Keeper::class, Relationship::class);

        if ($authorized !== null) {
            return $keepers->wherePivot('is_authorized_guardian', $authorized);
        }

        return $keepers;
    }

    /** @return BelongsToMany<Service, $this> */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, Attendance::class);
    }

    /** @return BelongsTo<Keeper, $this> */
    public function primaryKeeper(): BelongsTo
    {
        return $this->belongsTo(Keeper::class, 'primary_keeper_id');
    }
}
