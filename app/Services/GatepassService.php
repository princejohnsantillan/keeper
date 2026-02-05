<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\GatepassCreated;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Scopes\OrganizationScope;
use App\Models\TermAcceptance;
use App\ReadableCode;
use App\Services\Contracts\GatepassServiceInterface;
use Illuminate\Support\Facades\Mail;

final class GatepassService implements GatepassServiceInterface
{
    public function create(Activity $activity, Child $child, Guardian $guardian, ?TermAcceptance $termAcceptance = null): Gatepass
    {
        /** @var Organization $organization */
        $organization = Organization::query()->findOrFail($activity->organization_id);
        $code = $this->generateUniqueCode($organization);

        $gatepass = Gatepass::query()->create([
            'organization_id' => $organization->id,
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

    public function generateUniqueCode(Organization $organization): string
    {
        do {
            $code = ReadableCode::generate();
        } while (Gatepass::query()
            ->where('organization_id', $organization->id)
            ->where('code', $code)
            ->exists());

        return $code;
    }

    public function findByCode(string $code, Organization $organization): ?Gatepass
    {
        return Gatepass::query()
            ->with([
                'child',
                'guardian',
                'activity' => fn ($query) => $query->withoutGlobalScope(OrganizationScope::class),
            ])
            ->where('code', $code)
            ->where('organization_id', $organization->id)
            ->first();
    }

    public function findByUlid(string $ulid): ?Gatepass
    {
        return Gatepass::query()
            ->with([
                'child',
                'guardian',
                'activity' => fn ($query) => $query->withoutGlobalScope(OrganizationScope::class),
            ])
            ->find($ulid);
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
