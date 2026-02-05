<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

final class SubdomainService implements SubdomainInterface
{
    public function organization(): ?Organization
    {
        return once(function (): ?Organization {
            $slug = Str::beforeLast(request()->host(), '.'.Config::string('app.domain'));

            return rescue(fn (): ?Organization => Organization::query()->where('slug', $slug)->first());
        });
    }

    public function defined(): bool
    {
        $domains = [
            'www.'.Config::string('app.domain'),
            Config::string('app.domain'),
        ];

        return ! in_array(request()->host(), $domains);
    }
}
