<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\View\Components\ModalComponent;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        ModalComponent::closedByEscaping(false);
        ModalComponent::closeButton(false);

        Action::configureUsing(function (Action $action) {
            $action->slideOver();
        });

        DeleteAction::configureUsing(function (DeleteAction $action) {
            $action->slideOver(false);
        });
    }
}
