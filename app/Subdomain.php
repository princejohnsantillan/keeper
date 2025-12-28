<?php

declare(strict_types=1);

namespace App;

use App\Models\Organization;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

final class Subdomain
{
    public static function organization(): ?Organization
    {
        return once(function (): ?Organization {
            $slug = Str::beforeLast(request()->host(), '.'.Config::string('app.domain'));

            return Organization::query()->where('slug', $slug)->first();
        });
    }

    public static function defined(): bool
    {
        $domains = [
            'www.'.Config::string('app.domain'),
            Config::string('app.domain'),
        ];

        return !in_array(request()->host(), $domains);
    }
}
