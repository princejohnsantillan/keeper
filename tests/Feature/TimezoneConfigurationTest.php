<?php

declare(strict_types=1);

use Filament\Support\Facades\FilamentTimezone;

it('has app timezone set to UTC for database storage', function () {
    expect(config('app.timezone'))->toBe('UTC');
});

it('has Filament timezone set to Asia/Manila for display', function () {
    expect(FilamentTimezone::get())->toBe('Asia/Manila');
});
