<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Relationship as RelationshipEnum;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\ReadableCode;
use Illuminate\Support\Facades\DB;

final class WalkInRegistrationAction
{
    /**
     * Register a walk-in guardian and child for an activity.
     *
     * @param  array<string, mixed>  $guardianData
     * @param  array<string, mixed>  $childData
     */
    public function __invoke(
        array $guardianData,
        array $childData,
        RelationshipEnum $relationship,
        Activity $activity,
    ): Gatepass {
        return DB::transaction(function () use ($guardianData, $childData, $relationship, $activity): Gatepass {
            $guardian = Guardian::query()->create($guardianData);

            $child = Child::query()->create($childData);

            $guardian->children()->attach($child->id, [
                'relationship' => $relationship->value,
            ]);

            return Gatepass::query()->create([
                'guardian_id' => $guardian->id,
                'child_id' => $child->id,
                'activity_id' => $activity->id,
                'code' => ReadableCode::generate(),
            ]);
        });
    }
}
