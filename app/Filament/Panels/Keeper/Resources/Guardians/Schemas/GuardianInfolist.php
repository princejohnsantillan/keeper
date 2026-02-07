<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Schemas;

use App\Enums\Relationship as RelationshipEnum;
use App\Filament\Actions\EditOrganizationNoteAction;
use App\Filament\Actions\EditOrganizationTagsAction;
use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Models\Child;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

final class GuardianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppSpatieMediaLibraryImageEntry::avatar()
                    ->columnSpanFull(),
                AppTextEntry::firstName(),
                AppTextEntry::middleName(),
                AppTextEntry::lastName(),
                AppTextEntry::birthDate(),
                AppIconEntry::gender(),
                AppTextEntry::email(),
                AppTextEntry::phone(),
                self::childrenSection(),
                AppTextEntry::createdAt(),
                AppTextEntry::updatedAt(),
                self::organizationSection(),
            ]);
    }

    private static function childrenSection(): Section
    {
        return Section::make('Children')
            ->icon('fas-children')
            ->compact()
            ->extraAttributes(['class' => 'max-w-3xl'])
            ->collapsible()
            ->columnSpanFull()
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
            ->icon(fn (Child $record): string => $record->gender->getIcon())
            ->iconColor(fn (Child $record): array => $record->gender->getColor())
            ->grow(false);
    }

    private static function relationshipEntry(): TextEntry
    {
        return TextEntry::make('pivot.relationship')
            ->hiddenLabel()
            ->formatStateUsing(fn (RelationshipEnum $state, Child $record): string => $state->inverse($record->gender)->getLabel())
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
        return TextEntry::make('known_as')
            ->hiddenLabel()
            ->icon('heroicon-o-chat-bubble-bottom-center-text');
    }

    private static function organizationSection(): Section
    {
        return Section::make()
            ->columnSpanFull()
            ->schema([
                self::tagsEntry()
                    ->columnSpanFull(),
                self::organizationNoteEntry()
                    ->columnSpanFull(),
            ]);
    }

    private static function tagsEntry(): TextEntry
    {
        return AppTextEntry::tags('organizationTags.name', 'Organization tags')
            ->afterLabel(Schema::start([
                self::editTagsInlineAction(),
            ]));
    }

    private static function organizationNoteEntry(): TextEntry
    {
        return AppTextEntry::notes('organizationNote.note', 'Organization note')
            ->afterLabel(Schema::start([
                self::editOrganizationNoteInlineAction(),
            ]));
    }

    private static function editTagsInlineAction(): Action
    {
        return EditOrganizationTagsAction::make('edit_tags_inline')
            ->label('Edit tags')
            ->icon('heroicon-o-pencil-square')
            ->iconButton();
    }

    private static function editOrganizationNoteInlineAction(): Action
    {
        return EditOrganizationNoteAction::make('edit_note_inline')
            ->label('Edit note')
            ->icon('heroicon-o-pencil-square')
            ->iconButton();
    }
}
