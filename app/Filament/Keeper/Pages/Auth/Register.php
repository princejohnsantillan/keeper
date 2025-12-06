<?php

namespace App\Filament\Keeper\Pages\Auth;

use App\Models\Keeper;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Keeper Details')
                    ->schema([
                        $this->getFirstNameFormComponent(),
                        $this->getMiddleNameFormComponent(),
                        $this->getLastNameFormComponent(),
                        $this->getGenderFormComponent(),
                        $this->getBirthDateFormComponent(),
                        $this->getPhoneFormComponent(),
                    ])
                    ->columns(3),
                Fieldset::make('Account Details')
                    ->schema([
                        $this->getEmailFormComponent()->columnSpan(2),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(2),
            ]);
    }

    protected function getFirstNameFormComponent(): Component
    {
        return TextInput::make('first_name')
            ->label('First Name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getMiddleNameFormComponent(): Component
    {
        return TextInput::make('middle_name')
            ->label('Middle Name')
            ->maxLength(255);
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->label('Last Name')
            ->required()
            ->maxLength(255);
    }

    protected function getBirthDateFormComponent(): Component
    {
        return DatePicker::make('birth_date')
            ->label('Birth Date')
            ->native(false)
            ->maxDate(now());
    }

    protected function getGenderFormComponent(): Component
    {
        return ToggleButtons::make('gender')
            ->label('Gender')
            ->options([
                true => 'Male',
                false => 'Female',
            ])
            ->required()
            ->inline();
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Phone')
            ->tel()
            ->maxLength(255);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        $keeper = Keeper::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        return $this->getUserModel()::create([
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'password' => $data['password'],
            'keeper_id' => $keeper->id,
        ]);
    }

    public function getMaxContentWidth(): Width
    {
        return Width::ThreeExtraLarge;
    }
}
