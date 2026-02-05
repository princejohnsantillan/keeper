<?php

declare(strict_types=1);

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Gatepass;

it('can render the print sticker page', function () {
    $child = Child::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $gatepass = Gatepass::factory()->create([
        'child_id' => $child->id,
        'code' => 'ABC123',
    ]);

    $attendance = Attendance::factory()->create([
        'child_id' => $child->id,
        'activity_id' => $gatepass->activity_id,
        'checkin_gatepass_id' => $gatepass->id,
    ]);

    $response = $this->get(route('filament.keeper.attendance.print', $attendance));

    $response->assertSuccessful()
        ->assertSee('John Doe')
        ->assertSee('ABC123');
});

it('handles case when gatepass is not available', function () {
    $child = Child::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);

    $attendance = Attendance::factory()->create([
        'child_id' => $child->id,
        'checkin_gatepass_id' => null,
    ]);

    $response = $this->get(route('filament.keeper.attendance.print', $attendance));

    $response->assertSuccessful()
        ->assertSee('Jane Smith');
});
