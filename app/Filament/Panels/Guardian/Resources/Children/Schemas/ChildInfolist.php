<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use App\Avatar;
use App\Models\Child;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                self::profileHeader(),
                self::guardiansSection(),
                self::notesSection(),
            ]);
    }

    private static function profileHeader(): Section
    {
        return Section::make()
            ->compact()
            ->schema([
                Flex::make([
                    self::avatarEntry(),
                    Group::make([
                        self::nameWithGenderEntry(),
                        Flex::make([
                            self::nicknameEntry(),
                            self::ageEntry(),
                            self::birthdayEntry(),
                        ])->from('sm'),
                    ])->grow(),
                ])->from('sm'),
            ]);
    }

    private static function avatarEntry(): SpatieMediaLibraryImageEntry
    {
        return SpatieMediaLibraryImageEntry::make('avatar')
            ->hiddenLabel()
            ->collection('avatar')
            ->circular()
            ->size(72)
            ->defaultImageUrl(fn (Child $record): string => Avatar::generateUrl($record->full_name))
            ->grow(false);
    }

    private static function nameWithGenderEntry(): TextEntry
    {
        return TextEntry::make('full_name')
            ->hiddenLabel()
            ->size(TextSize::Large)
            ->weight(FontWeight::Bold)
            ->icon(fn (Child $record) => $record->gender->getIcon())
            ->iconColor(fn (Child $record) => $record->gender->getColor());
    }

    private static function nicknameEntry(): TextEntry
    {
        return TextEntry::make('nickname')
            ->label('Known as')
            ->inlineLabel()
            ->placeholder('—')
            ->grow(false);
    }

    private static function ageEntry(): TextEntry
    {
        return TextEntry::make('birth_date')
            ->label('Age')
            ->inlineLabel()
            ->formatStateUsing(function (CarbonImmutable $state): string {
                $age = $state->age;

                return $age === 1 ? '1 yr' : "{$age} yrs";
            })
            ->grow(false);
    }

    private static function birthdayEntry(): TextEntry
    {
        return TextEntry::make('birth_date')
            ->label('Birthday')
            ->inlineLabel()
            ->date('M j, Y')
            ->grow(false);
    }

    private static function guardiansSection(): Section
    {
        return Section::make('Guardians')
            ->icon('heroicon-o-users')
            ->compact()
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
            ->icon('heroicon-o-user')
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

    private static function notesSection(): Section
    {
        return Section::make('Notes')
            ->icon('heroicon-o-document-text')
            ->compact()
            ->collapsible()
            ->collapsed(fn (Child $record): bool => empty($record->notes))
            ->schema([
                TextEntry::make('notes')
                    ->hiddenLabel()
                    ->placeholder('No notes recorded.')
                    ->prose()
                    ->markdown(),
            ]);
    }
}
