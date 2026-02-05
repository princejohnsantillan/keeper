<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Pages;

use App\Actions\GetCurrentKeeperAction;
use App\Facades\Subdomain;
use App\Filament\Notifications\AppNotification;
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

    public ?string $gatepassId = null;

    public ?string $attendanceStatus = null;

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
            $this->attendanceStatus = null;

            return;
        }

        $this->gatepassId = $gatepass->id;
        $this->determineAttendanceStatus($attendanceService, $gatepass);

        $this->form->fill(['code' => '']);
    }

    public function getGatepass(): ?Gatepass
    {
        if ($this->gatepassId === null) {
            return null;
        }

        return Gatepass::query()
            ->with([
                'child',
                'guardian',
                'activity' => fn ($query) => $query->withoutGlobalScope(OrganizationScope::class),
            ])
            ->find($this->gatepassId);
    }

    public function checkIn(
        AttendanceServiceInterface $attendanceService,
        GetCurrentKeeperAction $getCurrentKeeper,
    ): void {
        $gatepass = $this->getGatepass();

        if ($gatepass === null) {
            return;
        }

        $keeper = $getCurrentKeeper();
        $result = $attendanceService->checkIn($gatepass->activity, $gatepass, $keeper);

        if (! $result['success']) {
            match ($result['message']) {
                'already_checked_in' => AppNotification::alreadyCheckedIn($result['child_name'])->send(),
                default => AppNotification::error('Check-in failed.')->send(),
            };

            return;
        }

        AppNotification::checkedIn($result['child_name'])->send();
        $this->determineAttendanceStatus($attendanceService, $gatepass);
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
                'already_checked_out' => AppNotification::alreadyCheckedOut($result['child_name'])->send(),
                'no_check_in_found' => AppNotification::noCheckInFound($result['child_name'])->send(),
                default => AppNotification::error('Check-out failed.')->send(),
            };

            return;
        }

        AppNotification::checkedOut($result['child_name'])->send();
        $this->determineAttendanceStatus($attendanceService, $gatepass);
    }

    public function clearGatepass(): void
    {
        $this->gatepassId = null;
        $this->attendanceStatus = null;
        $this->form->fill(['code' => '']);
    }

    private function determineAttendanceStatus(AttendanceServiceInterface $attendanceService, Gatepass $gatepass): void
    {
        $activityId = $gatepass->activity_id;
        $childId = $gatepass->child_id;

        if ($attendanceService->isAlreadyCheckedOut($activityId, $childId)) {
            $this->attendanceStatus = 'checked_out';
        } elseif ($attendanceService->isCheckedIn($activityId, $childId)) {
            $this->attendanceStatus = 'checked_in';
        } else {
            $this->attendanceStatus = 'not_checked_in';
        }
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::QrCode;
    }
}
