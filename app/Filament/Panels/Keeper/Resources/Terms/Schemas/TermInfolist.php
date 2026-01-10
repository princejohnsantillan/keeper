<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

final class TermInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->hiddenLabel()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Bold)
                    ->columnSpanFull(),
                TextEntry::make('content')
                    ->hiddenLabel()
                    ->markdown()
                    ->columnSpanFull(),
            ]);
    }
}
