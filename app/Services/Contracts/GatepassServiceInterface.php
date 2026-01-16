<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
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
     * Generate a unique code for an activity.
     */
    public function generateUniqueCode(Activity $activity): string;
}
