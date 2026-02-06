<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Schemas;

use App\Filament\Actions\EditOrganizationNoteAction;
use App\Filament\Actions\EditOrganizationTagsAction;
use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                AppTextEntry::createdAt(),
                AppTextEntry::updatedAt(),
                self::organizationSection(),
            ]);
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
