<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Relationship as RelationshipEnum;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Services\Contracts\GatepassServiceInterface;
use App\Services\Contracts\TermAcceptanceServiceInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class WalkInRegistrationAction
{
    public function __construct(
        private CreateOwnershipAction $createOwnershipAction,
        private GatepassServiceInterface $gatepassService,
        private TermAcceptanceServiceInterface $termAcceptanceService,
    ) {}

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
        bool $agreeToTerms,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): Gatepass {
        return DB::transaction(function () use (
            $guardianData,
            $childData,
            $relationship,
            $activity,
            $agreeToTerms,
            $ipAddress,
            $userAgent,
        ): Gatepass {
            if ($activity->term !== null && ! $agreeToTerms) {
                throw new InvalidArgumentException('Terms must be accepted before completing walk-in registration.');
            }

            $guardian = Guardian::query()->create($guardianData);
            ($this->createOwnershipAction)($guardian, $activity->organization);

            $child = Child::query()->create($childData);
            ($this->createOwnershipAction)($child, $activity->organization);

            $guardian->children()->attach($child->id, [
                'relationship' => $relationship->value,
            ]);

            $termAcceptance = null;

            if ($activity->term !== null) {
                $termAcceptance = $this->termAcceptanceService->accept(
                    $activity->term,
                    $guardian,
                    $ipAddress,
                    $userAgent,
                );
            }

            return $this->gatepassService->create($activity, $child, $guardian, $termAcceptance);
        });
    }
}
