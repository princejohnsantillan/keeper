<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AttendanceService;
use App\Services\AttendanceStickerPrintSettingService;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\AttendanceStickerPrintSettingServiceInterface;
use App\Services\Contracts\GatepassServiceInterface;
use App\Services\Contracts\InvitationServiceInterface;
use App\Services\Contracts\KeeperInvitationServiceInterface;
use App\Services\Contracts\SubdomainInterface;
use App\Services\Contracts\TermAcceptanceServiceInterface;
use App\Services\GatepassService;
use App\Services\InvitationService;
use App\Services\KeeperInvitationService;
use App\Services\SubdomainService;
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
        AttendanceStickerPrintSettingServiceInterface::class => AttendanceStickerPrintSettingService::class,
        TermAcceptanceServiceInterface::class => TermAcceptanceService::class,
        GatepassServiceInterface::class => GatepassService::class,
        InvitationServiceInterface::class => InvitationService::class,
        KeeperInvitationServiceInterface::class => KeeperInvitationService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Scoped (not singleton) so the resolved organization never leaks between
        // requests under long-running workers such as Octane.
        $this->app->scoped(SubdomainInterface::class, SubdomainService::class);
    }
}
