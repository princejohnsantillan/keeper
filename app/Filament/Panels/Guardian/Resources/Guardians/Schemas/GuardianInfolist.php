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
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

final class GuardianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                self::profileSection(),
                self::childrenSection(),
            ]);
    }

    private static function profileSection(): Section
    {
        return Section::make()
            ->compact()
            ->extraAttributes(['class' => 'max-w-2xl'])
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
                        AppIconEntry::gender()
                            ->inlineLabel(),
                        self::birthDateWithAgeEntry()
                            ->inlineLabel(),
                        AppTextEntry::email()
                            ->inlineLabel(),
                        AppTextEntry::phone()
                            ->inlineLabel(),
                    ]),
                ])->from('md'),
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

    private static function childrenSection(): Section
    {
        return Section::make('Children')
            ->icon('fas-children')
            ->compact()
            ->extraAttributes(['class' => 'max-w-2xl'])
            ->collapsible()
            ->schema([
                self::childrenRepeatable(),
            ]);
    }

    private static function childrenRepeatable(): RepeatableEntry
    {
        return RepeatableEntry::make('children')
            ->hiddenLabel()
            ->contained(false)
            ->schema([
                Flex::make([
                    self::childNameEntry(),
                    self::relationshipEntry(),
                    self::childAgeEntry(),
                    self::childNicknameEntry(),
                ])->from('md'),
            ]);
    }

    private static function childNameEntry(): TextEntry
    {
        return TextEntry::make('full_name')
            ->hiddenLabel()
            ->weight(FontWeight::Medium)
            ->icon(fn ($record) => $record->gender->getIcon())
            ->iconColor(fn ($record) => $record->gender->getColor())
            ->grow(false);
    }

    private static function relationshipEntry(): TextEntry
    {
        return TextEntry::make('pivot.relationship')
            ->hiddenLabel()
            ->formatStateUsing(fn (RelationshipEnum $state, $record): string => $state->inverse($record->gender)->getLabel())
            ->badge()
            ->grow(false);
    }

    private static function childAgeEntry(): TextEntry
    {
        return TextEntry::make('birth_date')
            ->hiddenLabel()
            ->icon('heroicon-o-cake')
            ->formatStateUsing(function (CarbonImmutable $state): string {
                $age = $state->age;
                $years = $age === 1 ? 'year' : 'years';

                return "{$age} {$years} old";
            })
            ->grow(false);
    }

    private static function childNicknameEntry(): TextEntry
    {
        return TextEntry::make('nickname')
            ->hiddenLabel()
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->placeholder('—');
    }
}
