<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use App\Avatar;
use App\Models\Child;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 4])
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('avatar')
                                    ->hiddenLabel()
                                    ->collection('avatar')
                                    ->circular()
                                    ->size(120)
                                    ->defaultImageUrl(fn (Child $record): string => Avatar::generateUrl($record->full_name))
                                    ->columnSpan(['default' => 1, 'md' => 1]),

                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('full_name')
                                            ->hiddenLabel()
                                            ->size(TextSize::Large)
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('nickname')
                                            ->label('Known as')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-chat-bubble-bottom-center-text'),

                                        Grid::make(['default' => 1, 'sm' => 3])
                                            ->schema([
                                                TextEntry::make('birth_date')
                                                    ->label('Age')
                                                    ->icon('heroicon-o-cake')
                                                    ->formatStateUsing(function (CarbonImmutable $state): string {
                                                        $age = $state->age;
                                                        $years = $age === 1 ? 'year' : 'years';

                                                        return "{$age} {$years} old";
                                                    }),

                                                TextEntry::make('birth_date')
                                                    ->label('Birthday')
                                                    ->icon('heroicon-o-calendar')
                                                    ->date('F j, Y'),

                                                IconEntry::make('gender')
                                                    ->label('Gender'),
                                            ]),
                                    ])
                                    ->columnSpan(['default' => 1, 'md' => 3]),
                            ]),
                    ]),

                Section::make('Guardians')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('guardians')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('full_name')
                                    ->label('Name')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('pivot.relationship')
                                    ->label('Relationship')
                                    ->badge(),

                                TextEntry::make('phone')
                                    ->label('Phone')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('—'),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->placeholder('—'),
                            ])
                            ->columns(['default' => 1, 'sm' => 2, 'lg' => 4]),
                    ]),

                Section::make('Notes')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(fn (Child $record): bool => empty($record->notes))
                    ->schema([
                        TextEntry::make('notes')
                            ->hiddenLabel()
                            ->placeholder('No notes recorded.')
                            ->prose()
                            ->markdown(),
                    ]),
            ]);
    }
}
