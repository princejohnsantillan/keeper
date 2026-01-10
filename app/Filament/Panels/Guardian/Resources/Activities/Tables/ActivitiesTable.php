<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Tables;

use App\Filament\Actions\AttendActivityAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
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
                    SpatieMediaLibraryImageColumn::make('thumbnail')
                        ->collection('thumbnail')
                        ->conversion('thumbnail')
                        ->height(200)
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'rounded-t-xl object-cover w-full']),
                    Stack::make([
                        TextColumn::make('title')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextColumn::make('starts_at')
                            ->icon(Heroicon::Calendar)
                            ->dateTime('M d, Y \a\t h:i A')
                            ->size(TextSize::Small),
                        TextColumn::make('location')
                            ->icon(Heroicon::MapPin)
                            ->size(TextSize::Small)
                            ->color('gray'),
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
                SpatieMediaLibraryImageEntry::make('thumbnail')
                    ->hiddenLabel()
                    ->collection('thumbnail')
                    ->conversion('thumbnail')
                    ->height(300)
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover w-full'])
                    ->columnSpanFull(),

                TextEntry::make('title')
                    ->hiddenLabel()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Bold)
                    ->columnSpanFull(),

                TextEntry::make('description')
                    ->hiddenLabel()
                    ->prose()
                    ->markdown()
                    ->placeholder('No description available.')
                    ->columnSpanFull(),

                Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        TextEntry::make('starts_at')
                            ->label('Starts')
                            ->icon(Heroicon::Calendar)
                            ->dateTime('F j, Y \a\t g:i A'),

                        TextEntry::make('ends_at')
                            ->label('Ends')
                            ->icon(Heroicon::Calendar)
                            ->dateTime('F j, Y \a\t g:i A'),
                    ]),

                Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        TextEntry::make('location')
                            ->icon(Heroicon::MapPin),

                        TextEntry::make('organization.name')
                            ->label('Organized by')
                            ->icon(Heroicon::BuildingOffice),
                    ]),

                AttendActivityAction::make('attend_from_view', 'Attend Activity')
                    ->extraAttributes(['class' => 'w-full justify-center']),
            ]);
    }
}
