<?php

declare(strict_types=1);

namespace App\Services\Fakes;

use App\Enums\OrganizationRouting;
use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;
use App\Services\SubdomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

final class FakeSubdomain implements SubdomainInterface
{
    public function __construct(
        private ?Organization $organization = null,
    ) {
        if ($this->organization !== null && $this->routing() === OrganizationRouting::Path) {
            URL::defaults(['organization' => $this->organization->slug]);
        }
    }

    public function routing(): OrganizationRouting
    {
        return $this->real()->routing();
    }

    public function organization(): ?Organization
    {
        return $this->organization;
    }

    public function resolve(Request $request): ?Organization
    {
        return $this->organization;
    }

    public function defined(): bool
    {
        return $this->organization !== null;
    }

    public function onRootDomain(Request $request): bool
    {
        return $this->organization === null;
    }

    public function adminPath(string $path = ''): string
    {
        return $this->real()->adminPath($path);
    }

    public function url(Organization $organization, string $path = ''): string
    {
        return $this->real()->url($organization, $path);
    }

    private function real(): SubdomainService
    {
        return new SubdomainService;
    }
}
