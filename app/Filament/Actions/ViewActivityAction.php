<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;

final class ViewActivityAction
{
    public static function make(?string $name = 'view_activity', string $label = 'View Activity'): Action
    {
        return Action::make($name)->label($label)
            ->hiddenLabel()
            ->link()
            ->extraAttributes(['class' => '!hidden'])
            ->modalHeading('')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->slideOver(false)
            ->schema([
                AppSpatieMediaLibraryImageEntry::thumbnail()
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover w-full aspect-video'])
                    ->columnSpanFull(),

                AppTextEntry::title()
                    ->columnSpanFull(),

                self::descriptionEntry()
                    ->columnSpanFull(),

                Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        self::startsAtEntry(),
                        self::endsAtEntry(),
                    ]),

                Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        self::locationEntry(),
                        self::organizationEntry(),
                    ]),

                RegisterActivityAction::make('register_from_view', 'Register')
                    ->extraAttributes(['class' => 'w-full justify-center']),
            ]);
    }

    private static function descriptionEntry(): TextEntry
    {
        return TextEntry::make('description')
            ->hiddenLabel()
            ->prose()
            ->markdown()
            ->placeholder('No description available.');
    }

    private static function startsAtEntry(): TextEntry
    {
        return TextEntry::make('starts_at')
            ->label('Starts')
            ->icon(Heroicon::Calendar)
            ->dateTime('F j, Y \a\t g:i A');
    }

    private static function endsAtEntry(): TextEntry
    {
        return TextEntry::make('ends_at')
            ->label('Ends')
            ->icon(Heroicon::Calendar)
            ->dateTime('F j, Y \a\t g:i A');
    }

    private static function locationEntry(): TextEntry
    {
        return TextEntry::make('location')
            ->icon(Heroicon::MapPin);
    }

    private static function organizationEntry(): TextEntry
    {
        return TextEntry::make('organization.name')
            ->label('Organized by')
            ->icon(Heroicon::BuildingOffice);
    }
}
