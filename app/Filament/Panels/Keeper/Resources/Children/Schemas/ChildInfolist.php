<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Schemas;

use App\Avatar;
use App\Models\Child;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryImageEntry::make('avatar')
                    ->collection('avatar')
                    ->circular()
                    ->defaultImageUrl(fn (Child $record): string => Avatar::generateUrl($record->full_name))
                    ->columnSpanFull(),
                TextEntry::make('first_name'),
                TextEntry::make('middle_name')
                    ->placeholder('-'),
                TextEntry::make('last_name'),
                TextEntry::make('nickname')
                    ->placeholder('-'),
                TextEntry::make('birth_date')
                    ->date(),
                IconEntry::make('gender')
                    ->boolean(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
