<?php

declare(strict_types=1);

namespace App\Actions;

use App\AuthUser;
use App\Models\Child;

final class CreateChildAction
{
    /**
     * Create a new child with owner_id from the current authenticated user.
     *
     * @param  array<string, mixed>  $childData
     */
    public function __invoke(array $childData): Child
    {
        return Child::query()->create([
            ...$childData,
            'owner_id' => AuthUser::userId(),
        ]);
    }
}
