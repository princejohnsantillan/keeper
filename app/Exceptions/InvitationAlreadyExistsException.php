<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InvitationAlreadyExistsException extends Exception
{
    public function __construct(string $email)
    {
        parent::__construct("A pending invitation already exists for {$email}.");
    }
}
