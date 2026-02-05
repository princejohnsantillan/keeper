<?php

declare(strict_types=1);

namespace App\Services\Fakes;

use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;

final class FakeSubdomain implements SubdomainInterface
{
    public function __construct(
        private ?Organization $organization = null,
    ) {}

    public function organization(): ?Organization
    {
        return $this->organization;
    }

    public function defined(): bool
    {
        return $this->organization !== null;
    }
}
