<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Organization Routing
    |--------------------------------------------------------------------------
    |
    | Controls how the organization is identified in Keeper (admin) panel URLs.
    |
    | "subdomain" => https://{slug}.{APP_DOMAIN}/admin
    | "path"      => {APP_URL}/{slug}/admin
    |
    | Guardians always use the root domain regardless of this setting.
    |
    */

    'routing' => env('ORGANIZATION_ROUTING', 'subdomain'),

];
