<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Schemas;

use App\Enums\Relationship as RelationshipEnum;
use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class GuardianInfolist
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
                            AppTextEntry::age(),
                            AppTextEntry::birthday(),
                            AppIconEntry::gender(),
                            AppTextEntry::email(),
                            AppTextEntry::phone(),
                        ]),
                    ]),
                Section::make('Children')
                    ->icon('fas-children')
                    ->collapsible()
                    ->schema([
                        self::childrenRepeatable(),
                    ]),
            ]);
    }

    private static function childrenRepeatable(): RepeatableEntry
    {
        return RepeatableEntry::make('children')
            ->hiddenLabel()
            ->columns([
                'default' => 1,
                'sm' => 2,
            ])
            ->schema([
                Group::make([
                    self::childNameEntry(),
                    self::relationshipEntry(),
                ]),
                Group::make([
                    self::childAgeEntry(),
                    AppTextEntry::nickname(),
                ]),
            ]);
    }

    private static function childNameEntry(): TextEntry
    {
        return TextEntry::make('full_name')
            ->label('Name')
            ->icon(fn ($record) => $record->gender->getIcon())
            ->iconColor(fn ($record) => $record->gender->getColor());
    }

    private static function relationshipEntry(): TextEntry
    {
        return TextEntry::make('pivot.relationship')
            ->label('Relationship')
            ->formatStateUsing(fn (RelationshipEnum $state, $record): string => $state->inverse($record->gender)->getLabel())
            ->badge();
    }

    private static function childAgeEntry(): TextEntry
    {
        return TextEntry::make('birth_date')
            ->label('Age')
            ->icon('heroicon-o-cake')
            ->formatStateUsing(function (CarbonImmutable $state): string {
                $age = $state->age;
                $years = $age === 1 ? 'year' : 'years';

                return "{$age} {$years} old";
            });
    }
}
