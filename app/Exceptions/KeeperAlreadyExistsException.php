<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class KeeperAlreadyExistsException extends Exception
{
    public function __construct(string $email)
    {
        parent::__construct("User with email {$email} is already a keeper for this organization.");
    }
}
