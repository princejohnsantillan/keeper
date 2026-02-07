<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Schemas;

use App\Filament\Actions\EditOrganizationNoteAction;
use App\Filament\Actions\EditOrganizationTagsAction;
use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Models\Guardian;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

final class ChildInfolist
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
                AppTextEntry::nickname(),
                AppTextEntry::birthDate(),
                AppIconEntry::gender(),
                AppTextEntry::notes('notes', "Guardian's notes")
                    ->columnSpanFull(),
                self::guardiansSection(),
                AppTextEntry::createdAt(),
                AppTextEntry::updatedAt(),
                self::organizationSection(),
            ]);
    }

    private static function guardiansSection(): Section
    {
        return Section::make('Guardians')
            ->icon('entypo-shield')
            ->compact()
            ->extraAttributes(['class' => 'max-w-3xl'])
            ->collapsible()
            ->columnSpanFull()
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
