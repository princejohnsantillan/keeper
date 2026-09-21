<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\OrganizationRouting;
use App\Models\Organization;
use Illuminate\Http\Request;

interface SubdomainInterface
{
    /**
     * The configured organization routing strategy.
     */
    public function routing(): OrganizationRouting;

    /**
     * Get the organization for the current request.
     */
    public function organization(): ?Organization;

    /**
     * Resolve (and remember) the organization from the given request.
     */
    public function resolve(Request $request): ?Organization;

    /**
     * Check if the current request identifies an organization (by subdomain or path).
     */
    public function defined(): bool;

    /**
     * Route URI for the admin panel, optionally with a sub-path appended.
     *
     * Returns "admin" in subdomain mode and "{organization}/admin" in path mode.
     */
    public function adminPath(string $path = ''): string;

    /**
     * Absolute URL to the given organization's admin area, optionally with a sub-path appended.
     */
    public function url(Organization $organization, string $path = ''): string;
}
