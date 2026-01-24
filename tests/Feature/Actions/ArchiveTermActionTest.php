<?php

declare(strict_types=1);

use App\Actions\ArchiveTermAction;
use App\Models\Term;

it('archives a term', function () {
    $term = Term::factory()->create([
        'archived_at' => null,
    ]);
    $action = app(ArchiveTermAction::class);

    $action($term);

    $term->refresh();

    expect($term->archived_at)->not->toBeNull();

    $this->assertDatabaseHas(Term::class, [
        'id' => $term->id,
    ]);

    $this->assertDatabaseMissing(Term::class, [
        'id' => $term->id,
        'archived_at' => null,
    ]);
});
