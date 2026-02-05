<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Organization;

interface SubdomainInterface
{
    /**
     * Get the organization for the current subdomain.
     */
    public function organization(): ?Organization;

    /**
     * Check if the request is on a subdomain.
     */
    public function defined(): bool;
}
