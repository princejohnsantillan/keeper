<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Pages;

use App\Actions\RegisterGuardianAction;
use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
use Filament\Auth\Pages\Register as AuthRegister;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

final class Register extends AuthRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Guardian Details')
                    ->schema([
                        AppTextInput::firstName()->autofocus(),
                        AppTextInput::middleName(),
                        AppTextInput::lastName(),
                        AppToggleButtons::gender(),
                        AppDatePicker::birthDate(),
                        AppTextInput::phone(),
                    ])
                    ->columns(3),
                Fieldset::make('Account Details')
                    ->schema([
                        $this->getEmailFormComponent()->columnSpanFull(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(2),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        return app(RegisterGuardianAction::class)($data);
    }

    public function getMaxContentWidth(): Width
    {
        return Width::FourExtraLarge;
    }
}
