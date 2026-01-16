<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Relationship as RelationshipEnum;
use Database\Factories\RelationshipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperRelationship
 *
 * @property int $id
 * @property int $guardian_id
 * @property int $child_id
 * @property RelationshipEnum $relationship
 * @property bool $is_primary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Child $child
 * @property-read \App\Models\Guardian $guardian
 *
 * @method static \Database\Factories\RelationshipFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class Relationship extends Pivot
{
    /** @use HasFactory<RelationshipFactory> */
    use HasFactory;

    protected $table = 'relationships';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'relationship' => RelationshipEnum::class,
        ];
    }

    /** @return BelongsTo<Guardian, $this> */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return BelongsTo<Child, $this>
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
