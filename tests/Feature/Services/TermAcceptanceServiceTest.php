<?php

declare(strict_types=1);

use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Term;
use App\Models\TermAcceptance;
use App\Services\Contracts\TermAcceptanceServiceInterface;

it('accepts terms for a guardian', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    $termAcceptance = $service->accept($term, $guardian, '127.0.0.1', 'Test Agent');

    expect($termAcceptance)
        ->toBeInstanceOf(TermAcceptance::class)
        ->term_id->toBe($term->id)
        ->guardian_id->toBe($guardian->id)
        ->ip_address->toBe('127.0.0.1')
        ->user_agent->toBe('Test Agent');

    $this->assertDatabaseHas(TermAcceptance::class, [
        'term_id' => $term->id,
        'guardian_id' => $guardian->id,
    ]);
});

it('returns existing acceptance if already accepted', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    $first = $service->accept($term, $guardian, '127.0.0.1', 'First Agent');
    $second = $service->accept($term, $guardian, '192.168.1.1', 'Second Agent');

    expect($first->id)->toBe($second->id);
    expect($second->ip_address)->toBe('127.0.0.1');
    expect($second->user_agent)->toBe('First Agent');
});

it('revokes term acceptance when no gatepasses depend on it', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    $service->accept($term, $guardian);

    $result = $service->revoke($term, $guardian);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing(TermAcceptance::class, [
        'term_id' => $term->id,
        'guardian_id' => $guardian->id,
    ]);
});

it('does not revoke term acceptance when gatepasses depend on it', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    $termAcceptance = $service->accept($term, $guardian);

    Gatepass::factory()->create([
        'term_acceptance_id' => $termAcceptance->id,
    ]);

    $result = $service->revoke($term, $guardian);

    expect($result)->toBeFalse();
    $this->assertDatabaseHas(TermAcceptance::class, [
        'term_id' => $term->id,
        'guardian_id' => $guardian->id,
    ]);
});

it('returns true when revoking non-existent acceptance', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    $result = $service->revoke($term, $guardian);

    expect($result)->toBeTrue();
});

it('checks if guardian has acceptance', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    expect($service->hasAcceptance($term, $guardian))->toBeFalse();

    $service->accept($term, $guardian);

    expect($service->hasAcceptance($term, $guardian))->toBeTrue();
});

it('checks if term acceptance is locked', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    $termAcceptance = $service->accept($term, $guardian);

    expect($service->isLocked($termAcceptance))->toBeFalse();

    Gatepass::factory()->create([
        'term_acceptance_id' => $termAcceptance->id,
    ]);

    expect($service->isLocked($termAcceptance))->toBeTrue();
});

it('gets acceptance for a guardian', function () {
    $service = app(TermAcceptanceServiceInterface::class);
    $term = Term::factory()->published()->create();
    $guardian = Guardian::factory()->create();

    expect($service->getAcceptance($term, $guardian))->toBeNull();

    $created = $service->accept($term, $guardian);

    $retrieved = $service->getAcceptance($term, $guardian);

    expect($retrieved)
        ->not->toBeNull()
        ->id->toBe($created->id);
});
