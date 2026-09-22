<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where the organization slug lives in Keeper (admin) panel URLs.
 */
enum OrganizationRouting: string
{
    /** https://{slug}.keeper.test/admin */
    case Subdomain = 'subdomain';

    /** https://keeper.test/{slug}/admin */
    case Path = 'path';
}
