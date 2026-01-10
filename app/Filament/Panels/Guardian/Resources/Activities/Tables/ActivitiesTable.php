<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Tables;

use App\Filament\Actions\AttendActivityAction;
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
                        TextColumn::make('description')
                            ->color('gray')
                            ->size(TextSize::Small)
                            ->limit(100),
                        TextColumn::make('starts_at')
                            ->icon(Heroicon::Calendar)
                            ->dateTime('M d, Y \a\t h:i A')
                            ->size(TextSize::Small),
                        TextColumn::make('location')
                            ->icon(Heroicon::MapPin)
                            ->size(TextSize::Small)
                            ->color('gray'),
                        TextColumn::make('organization.name')
                            ->icon(Heroicon::BuildingOffice)
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
            ->recordActions([
                AttendActivityAction::make(),
            ]);
    }
}
