<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class CannotDemoteLastAdminException extends Exception
{
    public function __construct()
    {
        parent::__construct('You cannot demote the last admin.');
    }
}
