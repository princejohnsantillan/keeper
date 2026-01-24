<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\GatepassCreated;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\TermAcceptance;
use App\ReadableCode;
use App\Services\Contracts\GatepassServiceInterface;
use Illuminate\Support\Facades\Mail;

final class GatepassService implements GatepassServiceInterface
{
    public function create(Activity $activity, Child $child, Guardian $guardian, ?TermAcceptance $termAcceptance = null): Gatepass
    {
        $code = $this->generateUniqueCode($activity);

        $gatepass = Gatepass::query()->create([
            'child_id' => $child->id,
            'guardian_id' => $guardian->id,
            'activity_id' => $activity->id,
            'code' => $code,
            'term_acceptance_id' => $termAcceptance?->id,
        ]);

        $this->sendCreatedEmail($gatepass);

        return $gatepass;
    }

    public function sendCreatedEmail(Gatepass $gatepass): void
    {
        $email = $gatepass->guardian->user?->email;

        if ($email === null) {
            return;
        }

        Mail::to($email)->queue(new GatepassCreated($gatepass));
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

    public function findByCodeAndActivity(string $code, string $activityId): ?Gatepass
    {
        return Gatepass::query()
            ->with('child')
            ->where('code', $code)
            ->where('activity_id', $activityId)
            ->first();
    }

    public function findExisting(Activity $activity, Child $child, Guardian $guardian): ?Gatepass
    {
        return Gatepass::query()
            ->where('activity_id', $activity->id)
            ->where('child_id', $child->id)
            ->where('guardian_id', $guardian->id)
            ->first();
    }
}
