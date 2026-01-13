<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;

final class UpdateChildAction
{
    /**
     * Update a child and optionally update the relationship with a guardian.
     *
     * @param  array<string, mixed>  $childData
     */
    public function __invoke(
        Child $child,
        array $childData,
        ?Guardian $guardian = null,
        ?RelationshipEnum $relationship = null,
    ): Child {
        $child->update($childData);

        if ($guardian !== null && $relationship !== null) {
            Relationship::query()
                ->where('child_id', $child->id)
                ->where('guardian_id', $guardian->id)
                ->update(['relationship' => $relationship->value]);
        }

        return $child;
    }
}
