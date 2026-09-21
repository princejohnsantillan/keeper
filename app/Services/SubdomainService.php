<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrganizationRouting;
use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

final class SubdomainService implements SubdomainInterface
{
    private const string ADMIN_PATH = 'admin';

    private const string ROUTE_PARAMETER = 'organization';

    private bool $resolved = false;

    private ?string $slug = null;

    private ?Organization $organization = null;

    public function routing(): OrganizationRouting
    {
        return OrganizationRouting::from(Config::string('organization.routing'));
    }

    public function organization(): ?Organization
    {
        if (! $this->resolved) {
            $this->resolve(request());
        }

        return $this->organization;
    }

    public function resolve(Request $request): ?Organization
    {
        $this->slug = $this->slugFrom($request);
        $this->organization = $this->slug === null
            ? null
            : rescue(fn (): ?Organization => Organization::query()->where('slug', $this->slug)->first());
        $this->resolved = true;

        return $this->organization;
    }

    public function defined(): bool
    {
        if (! $this->resolved) {
            $this->resolve(request());
        }

        return $this->slug !== null;
    }

    public function adminPath(string $path = ''): string
    {
        $adminPath = match ($this->routing()) {
            OrganizationRouting::Subdomain => self::ADMIN_PATH,
            OrganizationRouting::Path => '{'.self::ROUTE_PARAMETER.'}/'.self::ADMIN_PATH,
        };

        return $path === '' ? $adminPath : $adminPath.'/'.ltrim($path, '/');
    }

    public function url(Organization $organization, string $path = ''): string
    {
        $base = match ($this->routing()) {
            OrganizationRouting::Subdomain => sprintf(
                '%s://%s.%s/%s',
                $this->scheme(),
                $organization->slug,
                Config::string('app.domain'),
                self::ADMIN_PATH,
            ),
            OrganizationRouting::Path => sprintf(
                '%s/%s/%s',
                rtrim(Config::string('app.url'), '/'),
                $organization->slug,
                self::ADMIN_PATH,
            ),
        };

        return $path === '' ? $base : $base.'/'.ltrim($path, '/');
    }

    private function slugFrom(Request $request): ?string
    {
        return match ($this->routing()) {
            OrganizationRouting::Subdomain => $this->slugFromHost($request),
            OrganizationRouting::Path => $this->slugFromRoute($request),
        };
    }

    private function slugFromHost(Request $request): ?string
    {
        $domain = Config::string('app.domain');
        $host = $request->host();

        if (in_array($host, [$domain, 'www.'.$domain], true)) {
            return null;
        }

        if (! Str::endsWith($host, '.'.$domain)) {
            return null;
        }

        return Str::beforeLast($host, '.'.$domain);
    }

    private function slugFromRoute(Request $request): ?string
    {
        $slug = $request->route()?->parameter(self::ROUTE_PARAMETER);

        return is_string($slug) && $slug !== '' ? $slug : null;
    }

    private function scheme(): string
    {
        $scheme = Config::get('app.url_scheme') ?? parse_url(Config::string('app.url'), PHP_URL_SCHEME);

        return is_string($scheme) && $scheme !== '' ? $scheme : 'https';
    }
}
