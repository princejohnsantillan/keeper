<?php

declare(strict_types=1);

namespace App\Facades;

use App\Enums\OrganizationRouting;
use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;
use App\Services\Fakes\FakeSubdomain;
use App\Services\SubdomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static OrganizationRouting routing()
 * @method static Organization|null organization()
 * @method static Organization|null resolve(Request $request)
 * @method static bool defined()
 * @method static string adminPath(string $path = '')
 * @method static string url(Organization $organization, string $path = '')
 *
 * @see SubdomainService
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
