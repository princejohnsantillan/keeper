<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use Carbon\CarbonImmutable;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('first_name'),
                    TextEntry::make('middle_name'),
                    TextEntry::make('last_name'),
                    TextEntry::make('nickname'),
                    IconEntry::make('gender'),
                    TextEntry::make('birth_date')
                        ->formatStateUsing(function (CarbonImmutable $state) {
                            $date = $state->format('d M Y');
                            $age = $state->age;

                            return "{$date} ({$age} yrs)";
                        }),

                ])->columns(3)->columnSpanFull(),
                Section::make([
                    TextEntry::make('notes'),
                ])->columnSpanFull(),
            ]);
    }
}
