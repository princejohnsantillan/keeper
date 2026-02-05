<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Services\Contracts\SubdomainInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Once;

beforeEach(function () {
    Once::flush();
});

it('returns organization when valid subdomain', function () {
    $organization = Organization::factory()->create(['slug' => 'testorg']);
    $host = $organization->slug.'.'.Config::string('app.domain');
    request()->headers->set('Host', $host);

    $service = app(SubdomainInterface::class);

    expect($service->organization())
        ->not->toBeNull()
        ->id->toBe($organization->id);
});

it('returns null when invalid subdomain', function () {
    request()->headers->set('Host', 'nonexistent.'.Config::string('app.domain'));

    $service = app(SubdomainInterface::class);

    expect($service->organization())->toBeNull();
});

it('returns true for defined when on subdomain', function () {
    $organization = Organization::factory()->create(['slug' => 'testorg']);
    $host = $organization->slug.'.'.Config::string('app.domain');
    request()->headers->set('Host', $host);

    $service = app(SubdomainInterface::class);

    expect($service->defined())->toBeTrue();
});

it('returns false for defined when on main domain', function () {
    request()->headers->set('Host', Config::string('app.domain'));

    $service = app(SubdomainInterface::class);

    expect($service->defined())->toBeFalse();
});

it('returns false for defined when on www subdomain', function () {
    request()->headers->set('Host', 'www.'.Config::string('app.domain'));

    $service = app(SubdomainInterface::class);

    expect($service->defined())->toBeFalse();
});
