<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\TermAcceptance;
use App\ReadableCode;
use App\Services\Contracts\GatepassServiceInterface;

final class GatepassService implements GatepassServiceInterface
{
    public function create(Activity $activity, Child $child, Guardian $guardian, ?TermAcceptance $termAcceptance = null): Gatepass
    {
        $code = $this->generateUniqueCode($activity);

        return Gatepass::query()->create([
            'child_id' => $child->id,
            'guardian_id' => $guardian->id,
            'activity_id' => $activity->id,
            'code' => $code,
            'term_acceptance_id' => $termAcceptance?->id,
        ]);
    }

    public function generateUniqueCode(Activity $activity): string
    {
        do {
            $code = ReadableCode::generate();
        } while (Gatepass::query()
            ->where('activity_id', $activity->id)
            ->where('code', $code)
            ->exists());

        return $code;
    }
}
