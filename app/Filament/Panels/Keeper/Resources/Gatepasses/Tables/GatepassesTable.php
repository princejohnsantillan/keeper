<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Gatepasses\Tables;

use App\Models\Attendance;
use App\Models\Gatepass;
use App\Models\Keeper;
use App\Subdomain;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

final class GatepassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->badge()
                    ->copyable()
                    ->size(TextSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activity.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guardian.full_name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('guardian', function (Builder $query) use ($search): void {
                            $query->where('first_name', 'ilike', "%{$search}%")
                                ->orWhere('last_name', 'ilike', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('guardians', 'gatepasses.guardian_id', '=', 'guardians.id')
                            ->orderBy('guardians.first_name', $direction)
                            ->orderBy('guardians.last_name', $direction)
                            ->select('gatepasses.*');
                    }),
                TextColumn::make('child.full_name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('child', function (Builder $query) use ($search): void {
                            $query->where('first_name', 'ilike', "%{$search}%")
                                ->orWhere('last_name', 'ilike', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('children', 'gatepasses.child_id', '=', 'children.id')
                            ->orderBy('children.first_name', $direction)
                            ->orderBy('children.last_name', $direction)
                            ->select('gatepasses.*');
                    }),
            ])
            ->recordActions([
                Action::make('check_in')
                    ->label('Check In')
                    ->icon(Heroicon::ArrowRightEndOnRectangle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Check In Child')
                    ->modalDescription(fn (Gatepass $record): string => "Are you sure you want to check in {$record->child->full_name} for {$record->activity->title}?")
                    ->hidden(fn (Gatepass $record): bool => self::isCheckedIn($record))
                    ->action(function (Gatepass $record): void {
                        $childName = $record->child->full_name;

                        $existingAttendance = Attendance::query()
                            ->where('activity_id', $record->activity_id)
                            ->where('child_id', $record->child_id)
                            ->whereNotNull('checked_in_at')
                            ->whereNull('checked_out_at')
                            ->exists();

                        if ($existingAttendance) {
                            Notification::make()
                                ->title('Already checked in')
                                ->body("{$childName} is already checked in to this activity.")
                                ->warning()
                                ->send();

                            return;
                        }

                        $keeper = self::getCurrentKeeper();

                        Attendance::create([
                            'activity_id' => $record->activity_id,
                            'child_id' => $record->child_id,
                            'checkin_keeper_id' => $keeper->id,
                            'checkin_gatepass_id' => $record->id,
                            'checked_in_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Checked in')
                            ->body("{$childName} has been checked in successfully.")
                            ->success()
                            ->send();
                    }),
                Action::make('check_out')
                    ->label('Check Out')
                    ->icon(Heroicon::ArrowRightStartOnRectangle)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Check Out Child')
                    ->modalDescription(fn (Gatepass $record): string => "Are you sure you want to check out {$record->child->full_name} from {$record->activity->title}?")
                    ->visible(fn (Gatepass $record): bool => self::isCheckedIn($record))
                    ->action(function (Gatepass $record): void {
                        $childName = $record->child->full_name;

                        $attendance = Attendance::query()
                            ->where('activity_id', $record->activity_id)
                            ->where('child_id', $record->child_id)
                            ->whereNotNull('checked_in_at')
                            ->whereNull('checked_out_at')
                            ->first();

                        if ($attendance === null) {
                            Notification::make()
                                ->title('No check-in found')
                                ->body("{$childName} has not been checked in to this activity.")
                                ->danger()
                                ->send();

                            return;
                        }

                        $keeper = self::getCurrentKeeper();

                        $attendance->update([
                            'checkout_keeper_id' => $keeper->id,
                            'checkout_gatepass_id' => $record->id,
                            'checked_out_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Checked out')
                            ->body("{$childName} has been checked out successfully.")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    private static function isCheckedIn(Gatepass $gatepass): bool
    {
        return Attendance::query()
            ->where('activity_id', $gatepass->activity_id)
            ->where('child_id', $gatepass->child_id)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->exists();
    }

    private static function getCurrentKeeper(): Keeper
    {
        $organization = Subdomain::organization();
        $userId = Auth::id();

        $keeper = Keeper::query()
            ->where('organization_id', $organization?->id)
            ->where('user_id', $userId)
            ->first();

        if ($keeper === null) {
            abort(403, 'You are not a keeper for this organization.');
        }

        return $keeper;
    }
}
