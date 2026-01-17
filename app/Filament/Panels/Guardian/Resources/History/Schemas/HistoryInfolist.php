<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History\Schemas;

use App\Models\Attendance;
use App\Models\Relationship;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class HistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(2)
                    ->schema([
                        self::activitySection(),
                        self::childSection(),
                    ]),
                Grid::make(2)
                    ->schema([
                        self::checkinSection(),
                        self::checkoutSection(),
                    ]),
            ]);
    }

    private static function activitySection(): Section
    {
        return Section::make('Activity')
            ->icon(Heroicon::Play)
            ->compact()
            ->schema([
                self::activityTitleEntry(),
                self::organizerEntry(),
                self::locationEntry(),
                Flex::make([
                    self::startsAtEntry(),
                    self::endsAtEntry(),
                ])->from('md'),
            ]);
    }

    private static function activityTitleEntry(): TextEntry
    {
        return TextEntry::make('activity.title')
            ->hiddenLabel()
            ->weight(FontWeight::Bold)
            ->size(TextSize::Large)
            ->icon(Heroicon::Play)
            ->iconColor('primary');
    }

    private static function organizerEntry(): TextEntry
    {
        return TextEntry::make('activity.organization.name')
            ->hiddenLabel()
            ->icon(Heroicon::BuildingOffice)
            ->color('gray');
    }

    private static function locationEntry(): TextEntry
    {
        return TextEntry::make('activity.location')
            ->hiddenLabel()
            ->icon(Heroicon::MapPin)
            ->color('gray')
            ->placeholder('No location');
    }

    private static function startsAtEntry(): TextEntry
    {
        return TextEntry::make('activity.starts_at')
            ->label('Starts')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::Calendar)
            ->placeholder('—');
    }

    private static function endsAtEntry(): TextEntry
    {
        return TextEntry::make('activity.ends_at')
            ->label('Ends')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::Calendar)
            ->placeholder('—');
    }

    private static function childSection(): Section
    {
        return Section::make('Child')
            ->icon(Heroicon::FaceSmile)
            ->compact()
            ->schema([
                self::childNameEntry(),
                self::birthdayWithAgeEntry(),
            ]);
    }

    private static function birthdayWithAgeEntry(): TextEntry
    {
        return TextEntry::make('child.birth_date')
            ->label('Birthday')
            ->icon(Heroicon::Cake)
            ->formatStateUsing(function (\Carbon\CarbonImmutable $state): string {
                $formattedDate = $state->format('F j, Y');
                $age = $state->age;
                $years = $age === 1 ? 'year' : 'years';

                return "{$formattedDate} ({$age} {$years} old)";
            });
    }

    private static function childNameEntry(): TextEntry
    {
        return TextEntry::make('child.full_name')
            ->hiddenLabel()
            ->weight(FontWeight::Bold)
            ->size(TextSize::Large)
            ->icon(fn (Attendance $record) => $record->child->gender->getIcon())
            ->iconColor(fn (Attendance $record) => $record->child->gender->getColor());
    }

    private static function checkinSection(): Section
    {
        return Section::make('Check-in')
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->iconColor('success')
            ->compact()
            ->schema([
                self::checkedInAtEntry(),
                self::checkinGatepassCodeEntry(),
                self::checkinKeeperEntry(),
                self::checkinGuardianEntry(),
            ]);
    }

    private static function checkedInAtEntry(): TextEntry
    {
        return TextEntry::make('checked_in_at')
            ->label('Time')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::Clock)
            ->placeholder('Not checked in');
    }

    private static function checkinGatepassCodeEntry(): TextEntry
    {
        return TextEntry::make('checkinGatepass.code')
            ->label('Gate pass')
            ->badge()
            ->fontFamily(FontFamily::Mono)
            ->copyable()
            ->placeholder('—');
    }

    private static function checkinKeeperEntry(): TextEntry
    {
        return TextEntry::make('checkinKeeper.user.name')
            ->label('Keeper')
            ->icon(Heroicon::UserCircle)
            ->placeholder('—');
    }

    private static function checkinGuardianEntry(): TextEntry
    {
        return TextEntry::make('checkinGatepass.guardian.full_name')
            ->label('Guardian')
            ->icon(Heroicon::ShieldCheck)
            ->getStateUsing(function (Attendance $record): ?string {
                $guardian = $record->checkinGatepass?->guardian;
                if (! $guardian) {
                    return null;
                }

                $relationship = Relationship::query()
                    ->where('guardian_id', $guardian->id)
                    ->where('child_id', $record->child_id)
                    ->first();

                $relationshipLabel = $relationship?->relationship?->getLabel();

                return $relationshipLabel
                    ? "{$guardian->full_name} ({$relationshipLabel})"
                    : $guardian->full_name;
            })
            ->placeholder('—');
    }

    private static function checkoutSection(): Section
    {
        return Section::make('Checkout')
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->iconColor('danger')
            ->compact()
            ->schema([
                self::checkedOutAtEntry(),
                self::checkoutGatepassCodeEntry(),
                self::checkoutKeeperEntry(),
                self::checkoutGuardianEntry(),
            ]);
    }

    private static function checkoutGatepassCodeEntry(): TextEntry
    {
        return TextEntry::make('checkoutGatepass.code')
            ->label('Gate pass')
            ->badge()
            ->fontFamily(FontFamily::Mono)
            ->copyable()
            ->placeholder('—');
    }

    private static function checkedOutAtEntry(): TextEntry
    {
        return TextEntry::make('checked_out_at')
            ->label('Time')
            ->dateTime('M d, Y \a\t h:i A')
            ->icon(Heroicon::Clock)
            ->placeholder('Not checked out');
    }

    private static function checkoutKeeperEntry(): TextEntry
    {
        return TextEntry::make('checkoutKeeper.user.name')
            ->label('Keeper')
            ->icon(Heroicon::UserCircle)
            ->placeholder('—');
    }

    private static function checkoutGuardianEntry(): TextEntry
    {
        return TextEntry::make('checkoutGatepass.guardian.full_name')
            ->label('Guardian')
            ->icon(Heroicon::ShieldCheck)
            ->getStateUsing(function (Attendance $record): ?string {
                $guardian = $record->checkoutGatepass?->guardian;
                if (! $guardian) {
                    return null;
                }

                $relationship = Relationship::query()
                    ->where('guardian_id', $guardian->id)
                    ->where('child_id', $record->child_id)
                    ->first();

                $relationshipLabel = $relationship?->relationship?->getLabel();

                return $relationshipLabel
                    ? "{$guardian->full_name} ({$relationshipLabel})"
                    : $guardian->full_name;
            })
            ->placeholder('—');
    }
}
