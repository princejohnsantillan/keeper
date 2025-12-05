<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperRelationship
 */
final class Relationship extends Pivot
{
    /** @return BelongsTo<Keeper, $this> */
    public function keeper(): BelongsTo
    {
        return $this->belongsTo(Keeper::class);
    }

    /**
     * @return BelongsTo<Child, $this>
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
