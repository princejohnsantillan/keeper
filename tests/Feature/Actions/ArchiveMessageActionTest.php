<?php

declare(strict_types=1);

use App\Actions\ArchiveMessageAction;
use App\Models\Message;

it('archives a message', function () {
    $message = Message::factory()->create([
        'archived_at' => null,
    ]);
    $action = app(ArchiveMessageAction::class);

    $action($message);

    $message->refresh();

    expect($message->archived_at)->not->toBeNull();

    $this->assertDatabaseHas(Message::class, [
        'id' => $message->id,
    ]);

    $this->assertDatabaseMissing(Message::class, [
        'id' => $message->id,
        'archived_at' => null,
    ]);
});
