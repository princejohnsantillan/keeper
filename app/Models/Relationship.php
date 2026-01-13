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
