<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\TermAcceptance;

interface GatepassServiceInterface
{
    /**
     * Create a new gatepass for a child attending an activity.
     */
    public function create(Activity $activity, Child $child, Guardian $guardian, ?TermAcceptance $termAcceptance = null): Gatepass;

    /**
     * Send email notification to guardian about the created gatepass.
     */
    public function sendCreatedEmail(Gatepass $gatepass): void;

    /**
     * Generate a unique code within an organization.
     */
    public function generateUniqueCode(Organization $organization): string;

    /**
     * Find a gatepass by code within an organization.
     */
    public function findByCode(string $code, Organization $organization): ?Gatepass;

    /**
     * Find a gatepass by its ULID (for QR code scanning).
     */
    public function findByUlid(string $ulid): ?Gatepass;

    /**
     * Find a gatepass by code and activity.
     *
     * @deprecated Use findByCode() instead. Codes are now globally unique.
     */
    public function findByCodeAndActivity(string $code, string $activityId): ?Gatepass;

    /**
     * Find an existing gatepass for a child, guardian, and activity combination.
     */
    public function findExisting(Activity $activity, Child $child, Guardian $guardian): ?Gatepass;
}
