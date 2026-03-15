<?php

declare(strict_types=1);

namespace App\Enums;

enum RateLimiterName: string
{
    case ResendApi = 'resend-api';
}
