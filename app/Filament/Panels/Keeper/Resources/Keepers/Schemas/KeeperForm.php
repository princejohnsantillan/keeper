<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers\Schemas;

use App\Enums\KeeperRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class KeeperForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')
                    ->label('Name')
                    ->disabled(),

                TextInput::make('user.email')
                    ->label('Email')
                    ->disabled(),

                Select::make('role')
                    ->label('Role')
                    ->options(KeeperRole::class)
                    ->required(),
            ]);
    }
}
