<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class CannotChangeOwnAdminRoleException extends Exception
{
    public function __construct()
    {
        parent::__construct('You cannot change your own admin role.');
    }
}
