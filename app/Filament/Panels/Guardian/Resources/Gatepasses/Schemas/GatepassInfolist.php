<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Schemas;

use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Models\Gatepass;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class GatepassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        self::codeEntry()
                            ->columnSpanFull(),
                        self::activityEntry(),
                        self::startsAtEntry(),
                        self::locationEntry()
                            ->columnSpanFull(),
                    ]),
                Section::make('Guardian')
                    ->icon(Heroicon::User)
                    ->collapsible()
                    ->schema([
                        self::guardianNameEntry(),
                        AppTextEntry::email('guardian.email'),
                        AppTextEntry::phone('guardian.phone'),
                    ])
                    ->columns(2),
                Section::make('Child')
                    ->icon(Heroicon::FaceSmile)
                    ->collapsible()
                    ->schema([
                        self::childNameEntry(),
                        AppTextEntry::birthday('child.birth_date'),
                        AppIconEntry::gender('child.gender'),
                    ])
                    ->columns(2),
            ]);
    }

    private static function codeEntry(): TextEntry
    {
        return TextEntry::make('code')
            ->label('Gate Pass Code')
            ->badge()
            ->size('lg')
            ->copyable();
    }

    private static function activityEntry(): TextEntry
    {
        return TextEntry::make('activity.title')
            ->label('Activity')
            ->icon(Heroicon::Play);
    }

    private static function startsAtEntry(): TextEntry
    {
        return TextEntry::make('activity.starts_at')
            ->label('Date & Time')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::Calendar);
    }

    private static function locationEntry(): TextEntry
    {
        return TextEntry::make('activity.location')
            ->label('Location')
            ->icon(Heroicon::MapPin);
    }

    private static function guardianNameEntry(): TextEntry
    {
        return TextEntry::make('guardian.full_name')
            ->label('Name')
            ->icon(fn (Gatepass $record) => $record->guardian->gender->getIcon())
            ->iconColor(fn (Gatepass $record) => $record->guardian->gender->getColor());
    }

    private static function childNameEntry(): TextEntry
    {
        return TextEntry::make('child.full_name')
            ->label('Name')
            ->icon(fn (Gatepass $record) => $record->child->gender->getIcon())
            ->iconColor(fn (Gatepass $record) => $record->child->gender->getColor());
    }
}
