<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Models\Guardian;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                self::profileSection(),
                self::guardiansSection(),
            ]);
    }

    private static function profileSection(): Section
    {
        return Section::make()
            ->compact()
            ->extraAttributes(['class' => 'max-w-3xl'])
            ->schema([
                Flex::make([
                    AppSpatieMediaLibraryImageEntry::avatar()
                        ->grow(false),
                    Group::make([
                        AppTextEntry::firstName()
                            ->inlineLabel(),
                        AppTextEntry::middleName()
                            ->inlineLabel(),
                        AppTextEntry::lastName()
                            ->inlineLabel(),
                        AppTextEntry::nickname()
                            ->inlineLabel(),
                        AppIconEntry::gender()
                            ->inlineLabel(),
                        self::birthDateWithAgeEntry()
                            ->inlineLabel(),
                    ]),
                ])->from('md'),
                AppTextEntry::notes(),
            ]);
    }

    private static function birthDateWithAgeEntry(): TextEntry
    {
        return TextEntry::make('birth_date')
            ->label('Birth date')
            ->icon('heroicon-o-cake')
            ->formatStateUsing(function (CarbonImmutable $state): string {
                $formattedDate = $state->format('F j, Y');
                $age = $state->age;
                $years = $age === 1 ? 'year' : 'years';

                return "{$formattedDate} ({$age} {$years} old)";
            });
    }

    private static function guardiansSection(): Section
    {
        return Section::make('Guardians')
            ->icon('entypo-shield')
            ->compact()
            ->extraAttributes(['class' => 'max-w-3xl'])
            ->collapsible()
            ->schema([
                self::guardiansRepeatable(),
            ]);
    }

    private static function guardiansRepeatable(): RepeatableEntry
    {
        return RepeatableEntry::make('guardians')
            ->hiddenLabel()
            ->contained(false)
            ->schema([
                Flex::make([
                    self::guardianNameEntry(),
                    self::relationshipEntry(),
                    self::guardianPhoneEntry(),
                    self::guardianEmailEntry(),
                ])->from('md'),
            ]);
    }

    private static function guardianNameEntry(): TextEntry
    {
        return TextEntry::make('full_name')
            ->hiddenLabel()
            ->weight(FontWeight::Medium)
            ->icon(fn (Guardian $record): string => $record->gender->getIcon())
            ->iconColor(fn (Guardian $record): array => $record->gender->getColor())
            ->grow(false);
    }

    private static function relationshipEntry(): TextEntry
    {
        return TextEntry::make('pivot.relationship')
            ->hiddenLabel()
            ->badge()
            ->grow(false);
    }

    private static function guardianPhoneEntry(): TextEntry
    {
        return TextEntry::make('phone')
            ->hiddenLabel()
            ->icon('heroicon-o-phone')
            ->copyable()
            ->placeholder('—')
            ->grow(false);
    }

    private static function guardianEmailEntry(): TextEntry
    {
        return TextEntry::make('email')
            ->hiddenLabel()
            ->icon('heroicon-o-envelope')
            ->copyable()
            ->placeholder('—');
    }
}
