<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Models\Organization;
use App\Models\User;

final class CreateChildAction
{
    public function __construct(
        private CreateOwnershipAction $createOwnershipAction,
    ) {}

    /**
     * @param  array<string, mixed>  $childData
     */
    public function __invoke(array $childData, User|Organization $owner): Child
    {
        $child = Child::query()->create($childData);

        ($this->createOwnershipAction)($child, $owner);

        return $child;
    }
}
