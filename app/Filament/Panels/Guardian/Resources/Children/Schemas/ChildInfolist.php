<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Models\Child;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->schema([
                        AppSpatieMediaLibraryImageEntry::avatar(),
                        Group::make([
                            AppTextEntry::fullName(),
                            AppTextEntry::nickname(),
                            AppTextEntry::age(),
                            AppTextEntry::birthday(),
                            AppIconEntry::gender(),
                        ]),
                    ]),
                Section::make('Guardians')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->schema([
                        self::guardiansRepeatable(),
                    ]),
                Section::make('Notes')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(fn (Child $record): bool => empty($record->notes))
                    ->schema([
                        AppTextEntry::notes()
                            ->hiddenLabel(),
                    ]),
            ]);
    }

    private static function guardiansRepeatable(): RepeatableEntry
    {
        return RepeatableEntry::make('guardians')
            ->hiddenLabel()
            ->columns([
                'default' => 1,
                'sm' => 2,
            ])
            ->schema([
                Group::make([
                    self::guardianNameEntry(),
                    self::relationshipEntry(),
                ]),
                Group::make([
                    AppTextEntry::phone(),
                    AppTextEntry::email(),
                ]),
            ]);
    }

    private static function guardianNameEntry(): TextEntry
    {
        return TextEntry::make('full_name')
            ->label('Name')
            ->icon('heroicon-o-user');
    }

    private static function relationshipEntry(): TextEntry
    {
        return TextEntry::make('pivot.relationship')
            ->label('Relationship')
            ->badge();
    }
}
