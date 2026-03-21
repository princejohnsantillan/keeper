<?php

declare(strict_types=1);

namespace App\Livewire\Keeper;

use App\Models\AttendanceStickerPrintSetting;
use App\Models\User;
use App\Services\Contracts\AttendanceStickerPrintSettingServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

final class AttendanceStickerPrinter extends Component
{
    private const SESSION_KEY = 'attendance_sticker_print_settings';

    /** @var list<string> */
    public array $childTags = [];

    /** @var array<int, array{id: string, name: string}> */
    public array $savedPrintSettings = [];

    public string $childKnownAs = '';

    public string $childLastName = '';

    public string $checkinCode = '';

    public string $guardianNote = '';

    public string $selectedPrintSettingId = '';

    public string $loadPrintSettingId = '';

    public string $saveSettingName = '';

    public string $saveExistingSettingId = '';

    public string $printWidth = '90';

    public string $printHeight = '30';

    public string $marginTop = '1';

    public string $marginRight = '1';

    public string $marginBottom = '1';

    public string $marginLeft = '1';

    public bool $showLoadSettingsModal = false;

    public bool $showSaveSettingsModal = false;

    public ?string $statusMessage = null;

    /**
     * @param  list<string>  $childTags
     */
    public function mount(
        string $childKnownAs,
        string $childLastName,
        string $checkinCode,
        array $childTags,
        string $guardianNote,
    ): void {
        $this->childKnownAs = $childKnownAs;
        $this->childLastName = $childLastName;
        $this->checkinCode = $checkinCode;
        $this->childTags = $childTags;
        $this->guardianNote = $guardianNote;

        $this->loadSavedPrintSettings();
        $this->restoreSessionState();
    }

    public function updatedSaveExistingSettingId(string $settingId): void
    {
        if ($settingId === '') {
            return;
        }

        $printSetting = $this->printSettingService()->findForUser(
            $this->authenticatedUser(),
            $settingId,
        );

        if (! $printSetting instanceof AttendanceStickerPrintSetting) {
            return;
        }

        $this->saveSettingName = $printSetting->name;
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'printWidth',
            'printHeight',
            'marginTop',
            'marginRight',
            'marginBottom',
            'marginLeft',
        ], true)) {
            $this->persistSessionState();
        }
    }

    public function openLoadSettingsModal(): void
    {
        $this->loadPrintSettingId = $this->selectedPrintSettingId;
        $this->showLoadSettingsModal = true;
    }

    public function closeLoadSettingsModal(): void
    {
        $this->showLoadSettingsModal = false;
    }

    public function loadPrintSetting(): void
    {
        if ($this->loadPrintSettingId === '') {
            $this->statusMessage = 'Select a saved setting to load.';

            return;
        }

        $printSetting = $this->printSettingService()->findForUser(
            $this->authenticatedUser(),
            $this->loadPrintSettingId,
        );

        if (! $printSetting instanceof AttendanceStickerPrintSetting) {
            $this->statusMessage = 'Saved print setting not found.';

            return;
        }

        $this->applyPrintSetting($printSetting);
        $this->selectedPrintSettingId = $printSetting->id;
        $this->saveSettingName = $printSetting->name;
        $this->showLoadSettingsModal = false;
        $this->persistSessionState();
        $this->statusMessage = sprintf('Loaded "%s".', $printSetting->name);
    }

    public function openSaveSettingsModal(): void
    {
        $this->saveExistingSettingId = '';

        if ($this->selectedPrintSettingId !== '') {
            $selectedPrintSetting = $this->printSettingService()->findForUser(
                $this->authenticatedUser(),
                $this->selectedPrintSettingId,
            );

            if ($selectedPrintSetting instanceof AttendanceStickerPrintSetting) {
                $this->saveSettingName = $selectedPrintSetting->name;
                $this->saveExistingSettingId = $selectedPrintSetting->id;
            }
        }

        $this->showSaveSettingsModal = true;
    }

    public function closeSaveSettingsModal(): void
    {
        $this->showSaveSettingsModal = false;
    }

    public function savePrintSetting(): void
    {
        $validated = $this->validate();

        $printSetting = $this->printSettingService()->saveForUser(
            $this->authenticatedUser(),
            $validated['saveSettingName'],
            [
                'width_mm' => (float) $validated['printWidth'],
                'height_mm' => (float) $validated['printHeight'],
                'margin_top_mm' => (float) $validated['marginTop'],
                'margin_right_mm' => (float) $validated['marginRight'],
                'margin_bottom_mm' => (float) $validated['marginBottom'],
                'margin_left_mm' => (float) $validated['marginLeft'],
            ],
        );

        $this->selectedPrintSettingId = $printSetting->id;
        $this->loadPrintSettingId = $printSetting->id;
        $this->saveExistingSettingId = $printSetting->id;
        $this->saveSettingName = $printSetting->name;
        $this->loadSavedPrintSettings();
        $this->showSaveSettingsModal = false;
        $this->persistSessionState();
        $this->statusMessage = sprintf('Saved "%s".', $printSetting->name);
    }

    public function getStickerVariableStyleProperty(): string
    {
        return sprintf(
            '--print-width: %1$smm; --print-height: %2$smm; --print-margin-top: %3$smm; --print-margin-right: %4$smm; --print-margin-bottom: %5$smm; --print-margin-left: %6$smm;',
            $this->normalizeMeasurement($this->printWidth, '90'),
            $this->normalizeMeasurement($this->printHeight, '30'),
            $this->normalizeMeasurement($this->marginTop, '1'),
            $this->normalizeMeasurement($this->marginRight, '1'),
            $this->normalizeMeasurement($this->marginBottom, '1'),
            $this->normalizeMeasurement($this->marginLeft, '1'),
        );
    }

    public function render(): View
    {
        return view('livewire.keeper.attendance-sticker-printer');
    }

    /**
     * @return array<string, list<string>|string>
     */
    protected function rules(): array
    {
        return [
            'printWidth' => ['required', 'numeric', 'gt:0', 'max:999.99'],
            'printHeight' => ['required', 'numeric', 'gt:0', 'max:999.99'],
            'marginTop' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'marginRight' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'marginBottom' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'marginLeft' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'saveSettingName' => ['required', 'string', 'max:120'],
        ];
    }

    private function loadSavedPrintSettings(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            $this->savedPrintSettings = [];

            return;
        }

        $this->savedPrintSettings = $this->printSettingService()
            ->allForUser($user)
            ->map(fn (AttendanceStickerPrintSetting $printSetting): array => [
                'id' => $printSetting->id,
                'name' => $printSetting->name,
            ])
            ->all();
    }

    private function applyPrintSetting(AttendanceStickerPrintSetting $printSetting): void
    {
        $this->loadPrintSettingId = $printSetting->id;
        $this->printWidth = $this->formatMeasurement($printSetting->width_mm);
        $this->printHeight = $this->formatMeasurement($printSetting->height_mm);
        $this->marginTop = $this->formatMeasurement($printSetting->margin_top_mm);
        $this->marginRight = $this->formatMeasurement($printSetting->margin_right_mm);
        $this->marginBottom = $this->formatMeasurement($printSetting->margin_bottom_mm);
        $this->marginLeft = $this->formatMeasurement($printSetting->margin_left_mm);
    }

    private function persistSessionState(): void
    {
        session()->put(self::SESSION_KEY, [
            'selected_print_setting_id' => $this->selectedPrintSettingId,
            'print_width' => $this->printWidth,
            'print_height' => $this->printHeight,
            'margin_top' => $this->marginTop,
            'margin_right' => $this->marginRight,
            'margin_bottom' => $this->marginBottom,
            'margin_left' => $this->marginLeft,
        ]);
    }

    private function restoreSessionState(): void
    {
        /** @var array{
         *     selected_print_setting_id?: string,
         *     print_width?: string,
         *     print_height?: string,
         *     margin_top?: string,
         *     margin_right?: string,
         *     margin_bottom?: string,
         *     margin_left?: string
         * } $sessionState
         */
        $sessionState = session()->get(self::SESSION_KEY, []);

        $selectedPrintSettingId = $sessionState['selected_print_setting_id'] ?? '';

        if ($selectedPrintSettingId !== '') {
            $printSetting = $this->printSettingService()->findForUser(
                $this->authenticatedUser(),
                $selectedPrintSettingId,
            );

            if ($printSetting instanceof AttendanceStickerPrintSetting) {
                $this->selectedPrintSettingId = $printSetting->id;
                $this->saveExistingSettingId = $printSetting->id;
                $this->saveSettingName = $printSetting->name;
                $this->applyPrintSetting($printSetting);

                return;
            }
        }

        $this->printWidth = $sessionState['print_width'] ?? $this->printWidth;
        $this->printHeight = $sessionState['print_height'] ?? $this->printHeight;
        $this->marginTop = $sessionState['margin_top'] ?? $this->marginTop;
        $this->marginRight = $sessionState['margin_right'] ?? $this->marginRight;
        $this->marginBottom = $sessionState['margin_bottom'] ?? $this->marginBottom;
        $this->marginLeft = $sessionState['margin_left'] ?? $this->marginLeft;
        $this->loadPrintSettingId = '';
        $this->saveExistingSettingId = '';
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function printSettingService(): AttendanceStickerPrintSettingServiceInterface
    {
        return app(AttendanceStickerPrintSettingServiceInterface::class);
    }

    private function normalizeMeasurement(string $value, string $fallback): string
    {
        if (! is_numeric($value)) {
            return $fallback;
        }

        return $this->formatMeasurement((float) $value);
    }

    private function formatMeasurement(float|int|string $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
