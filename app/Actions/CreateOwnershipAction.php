<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Ownership;
use Illuminate\Database\Eloquent\Model;

final class CreateOwnershipAction
{
    /**
     * Ensure an ownership record exists for this model-owner pair.
     */
    public function __invoke(Model $model, Model $owner): void
    {
        Ownership::query()->updateOrCreate([
            'owner_type' => $owner->getMorphClass(),
            'owner_id' => (string) $owner->getKey(),
            'model_type' => $model->getMorphClass(),
            'model_id' => (string) $model->getKey(),
        ]);
    }
}
