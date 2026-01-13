<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AttendanceService;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\GatepassServiceInterface;
use App\Services\Contracts\TermAcceptanceServiceInterface;
use App\Services\GatepassService;
use App\Services\TermAcceptanceService;
use Illuminate\Support\ServiceProvider;

/**
 * Binds service interfaces to their implementations.
 *
 * @see .ai/guidelines/actions-and-services.md
 */
final class ServiceServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        AttendanceServiceInterface::class => AttendanceService::class,
        TermAcceptanceServiceInterface::class => TermAcceptanceService::class,
        GatepassServiceInterface::class => GatepassService::class,
    ];
}
