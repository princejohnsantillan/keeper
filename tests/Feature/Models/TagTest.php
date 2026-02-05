<?php

declare(strict_types=1);

use App\Facades\Subdomain;
use App\Models\Organization;
use App\Models\Tag;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    Subdomain::fake($this->organization);
});

it('automatically sets organization_id when creating a tag', function () {
    $tag = Tag::create([
        'name' => 'VIP',
    ]);

    expect($tag->organization_id)->toBe($this->organization->id);
});

it('does not override organization_id if explicitly provided', function () {
    $otherOrganization = Organization::factory()->create();

    $tag = Tag::create([
        'name' => 'Premium',
        'organization_id' => $otherOrganization->id,
    ]);

    expect($tag->organization_id)->toBe($otherOrganization->id);
});

it('finds tags scoped to the current organization', function () {
    $tag = Tag::create([
        'name' => 'Test Tag',
        'organization_id' => $this->organization->id,
    ]);

    $otherOrg = Organization::factory()->create();
    Tag::withoutGlobalScopes()->create([
        'name' => 'Other Tag',
        'organization_id' => $otherOrg->id,
    ]);

    $tags = Tag::all();

    expect($tags)->toHaveCount(1)
        ->and($tags->first()->id)->toBe($tag->id);
});

it('finds tag from string within current organization', function () {
    Tag::create([
        'name' => 'VIP',
        'organization_id' => $this->organization->id,
    ]);

    $found = Tag::findFromString('VIP');

    expect($found)->not->toBeNull()
        ->and($found->name)->toBe('vip');
});

it('creates tag with organization_id via findOrCreateFromString', function () {
    $tag = Tag::findOrCreateFromString('NewTag');

    expect($tag->organization_id)->toBe($this->organization->id)
        ->and($tag->name)->toBe('newtag');
});

it('finds tags case-insensitively', function () {
    Tag::create([
        'name' => 'VIP',
        'organization_id' => $this->organization->id,
    ]);

    expect(Tag::findFromString('vip'))->not->toBeNull()
        ->and(Tag::findFromString('VIP'))->not->toBeNull()
        ->and(Tag::findFromString('Vip'))->not->toBeNull();
});

it('returns existing tag when creating with different case', function () {
    $original = Tag::findOrCreateFromString('VIP');
    $found = Tag::findOrCreateFromString('vip');

    expect($found->id)->toBe($original->id)
        ->and(Tag::count())->toBe(1);
});

it('allows same tag name in different organizations', function () {
    $otherOrg = Organization::factory()->create();

    Tag::create([
        'name' => 'VIP',
        'organization_id' => $this->organization->id,
    ]);

    Tag::withoutGlobalScopes()->create([
        'name' => 'VIP',
        'organization_id' => $otherOrg->id,
    ]);

    expect(Tag::withoutGlobalScopes()->where('name', 'vip')->count())->toBe(2);
});

it('requires an organization when no subdomain is defined', function () {
    Subdomain::fake();

    expect(fn () => Tag::create([
        'name' => 'VIP',
    ]))->toThrow(QueryException::class);
});
