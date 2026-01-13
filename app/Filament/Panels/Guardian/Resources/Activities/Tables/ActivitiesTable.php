<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Tables;

use App\Filament\Actions\AttendActivityAction;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('starts_at', 'asc')
            ->columns([
                Stack::make([
                    AppSpatieMediaLibraryImageColumn::thumbnail()
                        ->height(200)
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'rounded-t-xl object-cover w-full']),
                    Stack::make([
                        self::titleColumn(),
                        self::startsAtColumn(),
                        self::locationColumn(),
                    ])->space(2)->extraAttributes(['class' => 'p-4']),
                ]),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'lg' => 3,
            ])
            ->recordAction('view_activity')
            ->recordActions([
                self::viewActivityAction(),
                AttendActivityAction::make(),
            ]);
    }

    private static function titleColumn(): TextColumn
    {
        return TextColumn::make('title')
            ->weight(FontWeight::Bold)
            ->size(TextSize::Large);
    }

    private static function startsAtColumn(): TextColumn
    {
        return TextColumn::make('starts_at')
            ->icon(Heroicon::Calendar)
            ->dateTime('M d, Y \a\t h:i A')
            ->size(TextSize::Small);
    }

    private static function locationColumn(): TextColumn
    {
        return TextColumn::make('location')
            ->icon(Heroicon::MapPin)
            ->size(TextSize::Small)
            ->color('gray');
    }

    private static function viewActivityAction(): Action
    {
        return Action::make('view_activity')
            ->hiddenLabel()
            ->link()
            ->extraAttributes(['class' => '!hidden'])
            ->modalHeading('')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->slideOver(false)
            ->schema([
                AppSpatieMediaLibraryImageEntry::thumbnail()
                    ->height(300)
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover w-full'])
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

                AttendActivityAction::make('attend_from_view', 'Attend Activity')
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
