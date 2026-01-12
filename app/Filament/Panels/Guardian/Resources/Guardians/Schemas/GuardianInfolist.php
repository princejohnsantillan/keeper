<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Schemas;

use App\Avatar;
use App\Enums\Relationship as RelationshipEnum;
use App\Models\Guardian;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

final class GuardianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('avatar')
                            ->hiddenLabel()
                            ->collection('avatar')
                            ->circular()
                            ->size(120)
                            ->defaultImageUrl(fn (Guardian $record): string => Avatar::generateUrl($record->full_name)),

                        Group::make([
                            TextEntry::make('full_name')
                                ->hiddenLabel()
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold),

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

                            TextEntry::make('email')
                                ->label('Email')
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->placeholder('—'),

                            TextEntry::make('phone')
                                ->label('Phone')
                                ->icon('heroicon-o-phone')
                                ->copyable()
                                ->placeholder('—'),
                        ]),
                    ]),

                Section::make('Children')
                    ->icon('fas-children')
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('children')
                            ->hiddenLabel()
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                            ])
                            ->schema([
                                Group::make([
                                    TextEntry::make('full_name')
                                        ->label('Name')
                                        ->icon(fn ($record) => $record->gender->getIcon())
                                        ->iconColor(fn ($record) => $record->gender->getColor()),

                                    TextEntry::make('pivot.relationship')
                                        ->label('Relationship')
                                        ->formatStateUsing(fn (RelationshipEnum $state, $record): string => $state->inverse($record->gender)->getLabel())
                                        ->badge(),
                                ]),

                                Group::make([
                                    TextEntry::make('birth_date')
                                        ->label('Age')
                                        ->icon('heroicon-o-cake')
                                        ->formatStateUsing(function (CarbonImmutable $state): string {
                                            $age = $state->age;
                                            $years = $age === 1 ? 'year' : 'years';

                                            return "{$age} {$years} old";
                                        }),

                                    TextEntry::make('nickname')
                                        ->label('Known as')
                                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                        ->placeholder('—'),
                                ]),
                            ]),
                    ]),
            ]);
    }
}
