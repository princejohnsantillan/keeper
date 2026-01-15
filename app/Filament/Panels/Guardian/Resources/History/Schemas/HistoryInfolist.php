<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History\Schemas;

use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use App\Models\Attendance;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class HistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Activity')
                    ->icon(Heroicon::Play)
                    ->columns(2)
                    ->schema([
                        self::activityEntry(),
                        self::startsAtEntry(),
                        self::locationEntry()
                            ->columnSpanFull(),
                    ]),
                Section::make('Attendance')
                    ->icon(Heroicon::Clock)
                    ->columns(2)
                    ->schema([
                        self::checkedInAtEntry(),
                        self::checkedOutAtEntry(),
                        AppTextEntry::notes('notes')
                            ->columnSpanFull(),
                    ]),
                Section::make('Child')
                    ->icon(Heroicon::FaceSmile)
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        self::childNameEntry(),
                        AppTextEntry::birthday('child.birth_date'),
                        AppIconEntry::gender('child.gender'),
                    ]),
            ]);
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

    private static function checkedInAtEntry(): TextEntry
    {
        return TextEntry::make('checked_in_at')
            ->label('Checked in')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->iconColor('success')
            ->placeholder('Not checked in');
    }

    private static function checkedOutAtEntry(): TextEntry
    {
        return TextEntry::make('checked_out_at')
            ->label('Checked out')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->iconColor('danger')
            ->placeholder('Not checked out');
    }

    private static function childNameEntry(): TextEntry
    {
        return TextEntry::make('child.full_name')
            ->label('Name')
            ->icon(fn (Attendance $record) => $record->child->gender->getIcon())
            ->iconColor(fn (Attendance $record) => $record->child->gender->getColor());
    }
}
