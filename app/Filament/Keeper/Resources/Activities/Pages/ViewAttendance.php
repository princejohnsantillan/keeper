<?php

declare(strict_types=1);

namespace App\Filament\Keeper\Resources\Activities\Pages;

use App\Filament\Keeper\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use App\Models\Keeper;
use App\Subdomain;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class ViewAttendance extends ManageRelatedRecords
{
    protected static string $resource = ActivityResource::class;

    protected static string $relationship = 'attendance';

    protected static ?string $title = 'Attendance';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('child.fullName')
                    ->label('Child')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('checkinGatepass.code')
                    ->label('Check-in Code'),
                TextColumn::make('checked_in_at')
                    ->label('Checked In')
                    ->dateTime('h:i A')
                    ->description(fn (Attendance $record): ?string => $record->checked_in_at?->format('M d, Y'))
                    ->sortable(),
                TextColumn::make('checkinKeeper.user.name')
                    ->label('Checked In By'),
                TextColumn::make('checkoutGatepass.code')
                    ->label('Check-out Code'),
                TextColumn::make('checked_out_at')
                    ->label('Checked Out')
                    ->dateTime('h:i A')
                    ->description(fn (Attendance $record): ?string => $record->checked_out_at?->format('M d, Y'))
                    ->sortable(),
                TextColumn::make('checkoutKeeper.user.name')
                    ->label('Checked Out By'),
                TextColumn::make('notes')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                Action::make('check_in')
                    ->label('Check In')
                    ->icon(Heroicon::ArrowRightEndOnRectangle)
                    ->color('success')
                    ->schema([
                        TextInput::make('code')
                            ->label('Gatepass Code')
                            ->required()
                            ->autofocus(),
                    ])
                    ->action(function (array $data): void {
                        /** @var Activity $activity */
                        $activity = $this->getOwnerRecord();

                        $gatepass = Gatepass::query()
                            ->with('child')
                            ->where('code', $data['code'])
                            ->where('activity_id', $activity->id)
                            ->first();

                        if ($gatepass === null) {
                            Notification::make()
                                ->title('Invalid code')
                                ->body('The gatepass code does not match this activity.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $childName = $gatepass->child->fullName;

                        $existingAttendance = Attendance::query()
                            ->where('activity_id', $activity->id)
                            ->where('child_id', $gatepass->child_id)
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

                        $keeper = $this->getCurrentKeeper();

                        Attendance::create([
                            'activity_id' => $activity->id,
                            'child_id' => $gatepass->child_id,
                            'checkin_keeper_id' => $keeper->id,
                            'checkin_gatepass_id' => $gatepass->id,
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
                    ->schema([
                        TextInput::make('code')
                            ->label('Gatepass Code')
                            ->required()
                            ->autofocus(),
                    ])
                    ->action(function (array $data): void {
                        /** @var Activity $activity */
                        $activity = $this->getOwnerRecord();

                        $gatepass = Gatepass::query()
                            ->with('child')
                            ->where('code', $data['code'])
                            ->where('activity_id', $activity->id)
                            ->first();

                        if ($gatepass === null) {
                            Notification::make()
                                ->title('Invalid code')
                                ->body('The gatepass code does not match this activity.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $childName = $gatepass->child->fullName;

                        $alreadyCheckedOut = Attendance::query()
                            ->where('activity_id', $activity->id)
                            ->where('child_id', $gatepass->child_id)
                            ->whereNotNull('checked_out_at')
                            ->exists();

                        if ($alreadyCheckedOut) {
                            Notification::make()
                                ->title('Already checked out')
                                ->body("{$childName} has already been checked out of this activity.")
                                ->warning()
                                ->send();

                            return;
                        }

                        $attendance = Attendance::query()
                            ->where('activity_id', $activity->id)
                            ->where('child_id', $gatepass->child_id)
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

                        $keeper = $this->getCurrentKeeper();

                        $attendance->update([
                            'checkout_keeper_id' => $keeper->id,
                            'checkout_gatepass_id' => $gatepass->id,
                            'checked_out_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Checked out')
                            ->body("{$childName} has been checked out successfully.")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private function getCurrentKeeper(): Keeper
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
