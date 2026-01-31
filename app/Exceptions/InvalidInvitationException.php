<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InvalidInvitationException extends Exception
{
    public function __construct(string $message = 'The invitation token is invalid or has expired.')
    {
        parent::__construct($message);
    }
}
