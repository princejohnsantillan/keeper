<?php

declare(strict_types=1);

namespace App\Facades;

use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;
use App\Services\Fakes\FakeSubdomain;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Organization|null organization()
 * @method static bool defined()
 *
 * @see \App\Services\SubdomainService
 */
final class Subdomain extends Facade
{
    public static function fake(?Organization $organization = null): FakeSubdomain
    {
        $fake = new FakeSubdomain($organization);
        self::swap($fake);

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return SubdomainInterface::class;
    }
}
