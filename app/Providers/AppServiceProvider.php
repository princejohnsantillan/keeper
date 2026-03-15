<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\RateLimiterName;
use App\Models\Organization;
use App\Observers\OrganizationObserver;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for(RateLimiterName::ResendApi, function (object $job): Limit {
            return Limit::perSecond(2);
        });
    }
}
