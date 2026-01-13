<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Term;
use App\Models\TermAcceptance;
use App\Services\Contracts\TermAcceptanceServiceInterface;

final class TermAcceptanceService implements TermAcceptanceServiceInterface
{
    public function accept(Term $term, Guardian $guardian, ?string $ipAddress = null, ?string $userAgent = null): TermAcceptance
    {
        return TermAcceptance::query()->firstOrCreate(
            [
                'term_id' => $term->id,
                'guardian_id' => $guardian->id,
            ],
            [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]
        );
    }

    public function revoke(Term $term, Guardian $guardian): bool
    {
        $termAcceptance = $this->getAcceptance($term, $guardian);

        if ($termAcceptance === null) {
            return true;
        }

        if ($this->isLocked($termAcceptance)) {
            return false;
        }

        $termAcceptance->delete();

        return true;
    }

    public function hasAcceptance(Term $term, Guardian $guardian): bool
    {
        return TermAcceptance::query()
            ->where('term_id', $term->id)
            ->where('guardian_id', $guardian->id)
            ->exists();
    }

    public function isLocked(TermAcceptance $termAcceptance): bool
    {
        return Gatepass::query()
            ->where('term_acceptance_id', $termAcceptance->id)
            ->exists();
    }

    public function getAcceptance(Term $term, Guardian $guardian): ?TermAcceptance
    {
        return TermAcceptance::query()
            ->where('term_id', $term->id)
            ->where('guardian_id', $guardian->id)
            ->first();
    }
}
