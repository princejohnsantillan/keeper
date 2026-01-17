<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;

final class UpdateChildAction
{
    /**
     * Update a child's data.
     *
     * @param  array<string, mixed>  $childData
     */
    public function __invoke(Child $child, array $childData): Child
    {
        $child->update($childData);

        return $child;
    }
}
