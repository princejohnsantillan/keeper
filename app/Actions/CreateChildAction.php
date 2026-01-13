<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;

final class CreateChildAction
{
    /**
     * Create a new child and establish a relationship with the guardian.
     *
     * @param  array<string, mixed>  $childData
     */
    public function __invoke(array $childData, Guardian $guardian, RelationshipEnum $relationship): Child
    {
        $child = Child::query()->create($childData);

        Relationship::query()->create([
            'guardian_id' => $guardian->id,
            'child_id' => $child->id,
            'relationship' => $relationship,
            'is_primary' => true,
        ]);

        return $child;
    }
}
