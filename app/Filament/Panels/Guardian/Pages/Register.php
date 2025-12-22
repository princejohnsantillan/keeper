<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Pages;

use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
use App\Models\Guardian;
use App\Models\User;
use Exception;
use Filament\Auth\Pages\Register as AuthRegister;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();

        try{
            $user = User::create([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            Guardian::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'user_id' => $user->id,
            ]);

            DB::commit();
        }catch (Exception $exception){
            DB::rollBack();

            throw $exception;
        }

        return $user;
    }

    public function getMaxContentWidth(): Width
    {
        return Width::FourExtraLarge;
    }
}
