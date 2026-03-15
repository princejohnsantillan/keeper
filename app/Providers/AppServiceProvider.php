<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Organization;
use App\Observers\OrganizationObserver;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        FilamentTimezone::set(config('app.display_timezone'));

        Organization::observe(OrganizationObserver::class);
    }
}
