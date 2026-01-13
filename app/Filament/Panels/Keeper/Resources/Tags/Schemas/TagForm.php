<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags\Schemas;

use App\Filament\Components\Forms\AppTextInput;
use Filament\Schemas\Schema;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppTextInput::name(),
                AppTextInput::type(),
            ]);
    }
}
