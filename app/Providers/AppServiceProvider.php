<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Organization;
use App\Observers\OrganizationObserver;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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

        FilamentTimezone::set('Asia/Manila');

        Organization::observe(OrganizationObserver::class);

        // Preserve v4 behavior: Grid/Section/Fieldset span full width by default
        Fieldset::configureUsing(fn (Fieldset $fieldset): Fieldset => $fieldset->columnSpanFull());
        Grid::configureUsing(fn (Grid $grid): Grid => $grid->columnSpanFull());
        Section::configureUsing(fn (Section $section): Section => $section->columnSpanFull());
    }
}
