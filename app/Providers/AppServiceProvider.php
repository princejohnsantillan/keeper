<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\View\Components\ModalComponent;
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
        ModalComponent::closedByEscaping(false);
        ModalComponent::closeButton(false);
        DeleteAction::configureUsing(function(DeleteAction $action) {
            $action->slideOver(false);
        });
        Action::configureUsing(function(Action $action) {
            $action->slideOver();
        });
        DatePicker::configureUsing(function(DatePicker $datePicker) {
            $datePicker->native(false);
        });

    }
}
