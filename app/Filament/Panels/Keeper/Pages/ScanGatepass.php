<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Pages;

use App\Actions\GetCurrentKeeperAction;
use App\Facades\Subdomain;
use App\Filament\Notifications\AppNotification;
use App\Models\Attendance;
use App\Models\Gatepass;
use App\Models\Scopes\OrganizationScope;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\GatepassServiceInterface;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
final class ScanGatepass extends Page
{
    protected string $view = 'filament.panels.keeper.pages.scan-gatepass';

    protected static ?string $title = 'Scan Gatepass';

    protected static ?string $navigationLabel = 'Scan';

    protected static ?int $navigationSort = -1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var array{status: 'not_checked_in'|'checked_in'|'checked_out', can_check_in: bool, can_check_out: bool, reason: null|'not_published'|'event_ended'|'checkin_not_open'|'checkin_closed'}|null
     */
    public ?array $attendanceState = null;

    public ?string $gatepassId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Gatepass Code')
                    ->placeholder('Enter or scan gatepass code')
                    ->autofocus()
                    ->extraInputAttributes(['autocomplete' => 'off']),
            ])
            ->statePath('data');
    }

    public function lookup(GatepassServiceInterface $gatepassService, AttendanceServiceInterface $attendanceService): void
    {
        $data = $this->form->getState();
        $input = trim($data['code'] ?? '');

        if ($input === '') {
            return;
        }

        // ULID is 26 characters, code is 5 characters
        if (strlen($input) === 26) {
            // QR scan - search by ULID
            $gatepass = $gatepassService->findByUlid($input);
        } else {
            // Manual entry - search by code within organization
            $organization = Subdomain::organization();

            if ($organization === null) {
                $gatepass = null;
            } else {
                $gatepass = $gatepassService->findByCode($input, $organization);
            }
        }

        if ($gatepass === null) {
            AppNotification::gatepassNotFound()->send();
            $this->gatepassId = null;
            $this->attendanceState = null;

            return;
        }

        $this->gatepassId = $gatepass->id;
        $this->refreshAttendanceState($attendanceService);

        $this->form->fill(['code' => '']);
    }

    public function getGatepass(): ?Gatepass
    {
        if ($this->gatepassId === null) {
            return null;
        }

        return Gatepass::query()
            ->with([
                'child.organizationTags',
                'guardian.organizationTags',
                'activity' => fn ($query) => $query->withoutGlobalScope(OrganizationScope::class),
            ])
            ->find($this->gatepassId);
    }

    public function checkIn(
        AttendanceServiceInterface $attendanceService,
        GetCurrentKeeperAction $getCurrentKeeper,
    ): void {
        $this->performCheckIn($attendanceService, $getCurrentKeeper);
    }

    public function checkInAndPrint(
        AttendanceServiceInterface $attendanceService,
        GetCurrentKeeperAction $getCurrentKeeper,
    ): void {
        $attendance = $this->performCheckIn($attendanceService, $getCurrentKeeper);

        if ($attendance === null) {
            return;
        }

        $this->openPrintSticker($attendance);
    }

    public function print(AttendanceServiceInterface $attendanceService): void
    {
        $gatepass = $this->getGatepass();

        if ($gatepass === null) {
            return;
        }

        $attendance = $attendanceService->findActiveAttendance($gatepass->activity_id, $gatepass->child_id)
            ?? $attendanceService->findLatestAttendance($gatepass->activity_id, $gatepass->child_id);

        if ($attendance === null) {
            AppNotification::error('No check-in record found to print.')->send();

            return;
        }

        $this->openPrintSticker($attendance);
    }

    public function checkOut(
        AttendanceServiceInterface $attendanceService,
        GetCurrentKeeperAction $getCurrentKeeper,
    ): void {
        $gatepass = $this->getGatepass();

        if ($gatepass === null) {
            return;
        }

        $keeper = $getCurrentKeeper();
        $result = $attendanceService->checkOut($gatepass->activity, $gatepass, $keeper);

        if (! $result['success']) {
            match ($result['message']) {
                'no_check_in_found' => AppNotification::noCheckInFound($result['child_name'])->send(),
                default => AppNotification::error('Check-out failed.')->send(),
            };

            return;
        }

        AppNotification::checkedOut($result['child_name'])->send();
        $this->refreshAttendanceState($attendanceService);
    }

    public function clearGatepass(): void
    {
        $this->gatepassId = null;
        $this->attendanceState = null;
        $this->form->fill(['code' => '']);
    }

    private function refreshAttendanceState(AttendanceServiceInterface $attendanceService): void
    {
        $gatepass = $this->getGatepass();

        if ($gatepass === null) {
            $this->attendanceState = null;

            return;
        }

        $this->attendanceState = $attendanceService->resolveGatepassActionState($gatepass);
    }

    private function performCheckIn(
        AttendanceServiceInterface $attendanceService,
        GetCurrentKeeperAction $getCurrentKeeper,
    ): ?Attendance {
        $gatepass = $this->getGatepass();

        if ($gatepass === null) {
            return null;
        }

        $keeper = $getCurrentKeeper();
        $result = $attendanceService->checkIn($gatepass->activity, $gatepass, $keeper);

        if (! $result['success']) {
            match ($result['message']) {
                'already_checked_in' => AppNotification::alreadyCheckedIn($result['child_name'])->send(),
                'activity_not_published' => AppNotification::activityNotPublished($gatepass->activity->title)->send(),
                'activity_ended' => AppNotification::activityEnded($gatepass->activity->title)->send(),
                'checkin_not_open' => AppNotification::checkInNotOpen($gatepass->activity->title)->send(),
                'checkin_closed' => AppNotification::checkInAlreadyClosed($gatepass->activity->title)->send(),
                default => AppNotification::error('Check-in failed.')->send(),
            };

            return null;
        }

        AppNotification::checkedIn($result['child_name'])->send();
        $this->refreshAttendanceState($attendanceService);

        return $result['attendance'];
    }

    private function openPrintSticker(Attendance $attendance): void
    {
        $printUrl = route('filament.keeper.attendance.print', $attendance);

        $this->dispatch('open-print-sticker', url: $printUrl);
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::QrCode;
    }
}
