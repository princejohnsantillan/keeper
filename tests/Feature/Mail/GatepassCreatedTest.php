<?php

declare(strict_types=1);

use App\Mail\GatepassCreated;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Message;
use App\Models\Organization;

it('contains the gatepass code', function () {
    $gatepass = Gatepass::factory()->create(['code' => 'ABC12']);

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('ABC12');
});

it('contains the child name', function () {
    $child = Child::factory()->create([
        'first_name' => 'John',
        'middle_name' => null,
        'last_name' => 'Doe',
    ]);
    $gatepass = Gatepass::factory()->for($child)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('John Doe');
});

it('contains the guardian name', function () {
    $guardian = Guardian::factory()->create([
        'first_name' => 'Jane',
        'middle_name' => null,
        'last_name' => 'Smith',
    ]);
    $gatepass = Gatepass::factory()->for($guardian)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('Jane Smith');
});

it('contains the activity title', function () {
    $activity = Activity::factory()->create(['title' => 'Summer Camp 2026']);
    $gatepass = Gatepass::factory()->for($activity)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('Summer Camp 2026');
});

it('contains the activity start date and time', function () {
    $activity = Activity::factory()->create([
        'starts_at' => '2026-07-15 09:00:00',
    ]);
    $gatepass = Gatepass::factory()->for($activity)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('Wednesday, July 15, 2026 at 9:00 AM');
});

it('contains the activity location', function () {
    $activity = Activity::factory()->create([
        'location' => 'City Park Recreation Center',
    ]);
    $gatepass = Gatepass::factory()->for($activity)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('City Park Recreation Center');
});

it('contains the registration success message', function () {
    $gatepass = Gatepass::factory()->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('You have successfully registered');
});

it('contains the keep code safe message', function () {
    $gatepass = Gatepass::factory()->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('Please keep this code safe');
});

it('has the correct subject', function () {
    $gatepass = Gatepass::factory()->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertHasSubject('Activity Registration Confirmed');
});

it('contains the organizer message when activity has a message template', function () {
    $organization = Organization::factory()->create(['name' => 'Test Organization']);
    $message = Message::factory()->for($organization)->create([
        'content' => '<p>Please bring sunscreen and water bottles.</p>',
    ]);
    $activity = Activity::factory()->for($organization)->for($message)->create();
    $gatepass = Gatepass::factory()->for($activity)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertSeeInHtml('Please bring sunscreen and water bottles.');
    $mailable->assertSeeInHtml('A message from Test Organization');
    $mailable->assertSeeInHtml('This message is from the event organizer (Test Organization), not Keeper.');
});

it('does not show organizer message section when activity has no message template', function () {
    $activity = Activity::factory()->create(['message_id' => null]);
    $gatepass = Gatepass::factory()->for($activity)->create();

    $mailable = new GatepassCreated($gatepass);

    $mailable->assertDontSeeInHtml('A message from');
    $mailable->assertDontSeeInHtml('This message is from the event organizer');
});
