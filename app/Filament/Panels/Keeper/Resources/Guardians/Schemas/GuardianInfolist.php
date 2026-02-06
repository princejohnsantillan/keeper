<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Schemas;

use App\Filament\Actions\EditOrganizationNoteAction;
use App\Filament\Actions\EditOrganizationTagsAction;
use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

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
                self::tagsEntry()
                    ->columnSpanFull(),
                self::organizationNoteEntry()
                    ->columnSpanFull(),
                AppTextEntry::createdAt(),
                AppTextEntry::updatedAt(),
            ]);
    }

    private static function tagsEntry(): TextEntry
    {
        return AppTextEntry::tags('organizationTags.name', 'Tags')
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
