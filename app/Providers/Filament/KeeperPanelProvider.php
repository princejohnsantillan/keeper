<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\RequireOrganizationSubdomain;
use App\Subdomain;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class KeeperPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('keeper')
            ->path('admin')
            ->brandName(Subdomain::organization()?->name ?: 'Keeper')
            ->login()
            ->revealablePasswords(false)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Neutral,
                'info' => Color::Sky,
                'primary' => Color::Fuchsia,
                'success' => Color::Green,
                'warning' => Color::Orange,
            ])
            ->breadcrumbs(false)
            ->discoverResources(in: app_path('Filament/Keeper/Resources'), for: 'App\Filament\Keeper\Resources')
            ->discoverPages(in: app_path('Filament/Keeper/Pages'), for: 'App\Filament\Keeper\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Keeper/Widgets'), for: 'App\Filament\Keeper\Widgets')
            ->middleware([
                RequireOrganizationSubdomain::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
