<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Panels\Keeper\Pages\Login;
use App\Http\Middleware\RequireOrganizationSubdomain;
use App\Subdomain;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class KeeperPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('keeper')
            ->path('admin')
            ->brandName(Subdomain::organization()?->name ?: 'Keeper')
            ->login(Login::class)
            ->passwordReset()
            ->emailVerification()
            ->emailChangeVerification()
            ->profile()
            ->revealablePasswords(false)
            ->viteTheme('resources/css/filament/keeper/theme.css')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Neutral,
                'info' => Color::Sky,
                'primary' => Color::Fuchsia,
                'success' => Color::Green,
                'warning' => Color::Orange,
            ])
            ->breadcrumbs(false)
            ->sidebarWidth('14rem')
            ->discoverResources(in: app_path('Filament/Panels/Keeper/Resources'), for: 'App\Filament\Panels\Keeper\Resources')
            ->discoverPages(in: app_path('Filament/Panels/Keeper/Pages'), for: 'App\Filament\Panels\Keeper\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Panels/Keeper/Widgets'), for: 'App\Filament\Panels\Keeper\Widgets')
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
            ])
            ->renderHook(
                PanelsRenderHook::SCRIPTS_AFTER,
                fn (): string => Blade::render(<<<'BLADE'
                    <script>
                        document.addEventListener('livewire:init', () => {
                            Livewire.on('open-print-sticker', (event) => {
                                window.open(event.url, '_blank');
                            });
                        });
                    </script>
                    BLADE),
            );
    }
}
