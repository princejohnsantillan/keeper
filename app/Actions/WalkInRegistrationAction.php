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
     * Register a walk-in guardian and children for an activity.
     *
     * @param  array<string, mixed>  $guardianData
     * @param  array<int, array{data: array<string, mixed>, relationship: RelationshipEnum}>  $childrenData
     * @return array<int, Gatepass>
     */
    public function __invoke(
        array $guardianData,
        array $childrenData,
        Activity $activity,
        bool $agreeToTerms,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): array {
        return DB::transaction(function () use (
            $guardianData,
            $childrenData,
            $activity,
            $agreeToTerms,
            $ipAddress,
            $userAgent,
        ): array {
            if ($activity->term !== null && ! $agreeToTerms) {
                throw new InvalidArgumentException('Terms must be accepted before completing walk-in registration.');
            }

            $guardian = Guardian::query()->create($guardianData);
            ($this->createOwnershipAction)($guardian, $activity->organization);

            $termAcceptance = null;

            if ($activity->term !== null) {
                $termAcceptance = $this->termAcceptanceService->accept(
                    $activity->term,
                    $guardian,
                    $ipAddress,
                    $userAgent,
                );
            }

            $gatepasses = [];

            foreach ($childrenData as $entry) {
                $child = Child::query()->create($entry['data']);
                ($this->createOwnershipAction)($child, $activity->organization);

                $guardian->children()->attach($child->id, [
                    'relationship' => $entry['relationship']->value,
                ]);

                $gatepasses[] = $this->gatepassService->create($activity, $child, $guardian, $termAcceptance);
            }

            return $gatepasses;
        });
    }
}
