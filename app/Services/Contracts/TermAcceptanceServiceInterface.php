<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Guardian;
use App\Models\Term;
use App\Models\TermAcceptance;

interface TermAcceptanceServiceInterface
{
    /**
     * Accept terms for a guardian. Creates a new acceptance or returns existing one.
     */
    public function accept(Term $term, Guardian $guardian, ?string $ipAddress = null, ?string $userAgent = null): TermAcceptance;

    /**
     * Revoke term acceptance for a guardian if no gatepasses depend on it.
     *
     * @return bool True if revoked successfully, false if locked by gatepasses
     */
    public function revoke(Term $term, Guardian $guardian): bool;

    /**
     * Check if a guardian has accepted the terms.
     */
    public function hasAcceptance(Term $term, Guardian $guardian): bool;

    /**
     * Check if a term acceptance is locked (has gatepasses depending on it).
     */
    public function isLocked(TermAcceptance $termAcceptance): bool;

    /**
     * Get the term acceptance for a guardian if it exists.
     */
    public function getAcceptance(Term $term, Guardian $guardian): ?TermAcceptance;
}
