<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Message;

final class ArchiveMessageAction
{
    /**
     * Archive a message.
     */
    public function __invoke(Message $message): void
    {
        $message->update(['archived_at' => now()]);
    }
}
