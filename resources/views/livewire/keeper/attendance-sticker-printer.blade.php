<div class="sticker-page" style="{{ $this->stickerVariableStyle }}">
    @php
        $tagCount = count($childTags);
        $hasGuardianNote = $guardianNote !== '';
    @endphp

    <style>
        .sticker-page {
            --print-width: 90mm;
            --print-height: 30mm;
            --print-margin-top: 1mm;
            --print-margin-right: 1mm;
            --print-margin-bottom: 1mm;
            --print-margin-left: 1mm;
        }

        @media print {
            @page {
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            body {
                min-height: 0 !important;
                display: block !important;
            }

            .sticker-preview {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                padding:
                    var(--print-margin-top)
                    var(--print-margin-right)
                    var(--print-margin-bottom)
                    var(--print-margin-left) !important;
                background: transparent !important;
            }

            .sticker {
                position: static !important;
                width: var(--print-width) !important;
                height: var(--print-height) !important;
                box-shadow: none !important;
            }
        }

        .sticker-page * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .print-controls {
            width: min(100%, 56rem);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
        }

        .controls-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .print-control {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .print-control label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .print-control input,
        .print-control select {
            width: 100%;
            padding: 0.55rem 0.7rem;
            border: 1px solid #9ca3af;
            border-radius: 0.5rem;
            font: inherit;
            color: #111827;
            background: #ffffff;
        }

        .print-control input:focus,
        .print-control select:focus {
            outline: 2px solid #4f46e5;
            outline-offset: 1px;
            border-color: #4f46e5;
        }

        .print-control-button {
            justify-content: flex-end;
        }

        .print-control-button .btn {
            width: 100%;
        }

        .print-status {
            font-size: 0.875rem;
            color: #374151;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(17, 24, 39, 0.45);
            z-index: 50;
        }

        .modal-card {
            width: min(100%, 28rem);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.1rem;
            border-radius: 0.9rem;
            background: #ffffff;
            box-shadow: 0 20px 40px rgba(17, 24, 39, 0.18);
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .modal-help,
        .input-error {
            font-size: 0.8125rem;
            color: #6b7280;
        }

        .input-error {
            color: #b91c1c;
        }

        .sticker-preview {
            padding:
                var(--print-margin-top)
                var(--print-margin-right)
                var(--print-margin-bottom)
                var(--print-margin-left);
            background: transparent;
        }

        .sticker {
            width: var(--print-width);
            height: var(--print-height);
            background: #ffffff;
            overflow: hidden;
            display: grid;
            grid-template-columns: 11mm minmax(0, 1fr);
            gap: 1mm;
            padding: 1mm;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.12);
        }

        .sticker-right {
            min-width: 0;
            display: grid;
            grid-template-rows: 1fr;
            gap: 1mm;
        }

        .sticker-right.has-tags {
            grid-template-rows: 2fr 1fr;
        }

        .print-black-fill {
            background: #000000 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            forced-color-adjust: none;
        }

        .child-checkin-code-rail {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            border: 0.45px solid #111827;
            border-radius: 2mm;
            overflow: hidden;
            padding: 1mm 0;
        }

        .top-row {
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 65fr) minmax(0, 35fr);
            gap: 1mm;
            min-width: 0;
        }

        .top-row.top-row-full {
            grid-template-columns: minmax(0, 1fr);
        }

        .panel {
            min-width: 0;
            height: 100%;
            border: 0.45px solid #111827;
            border-radius: 2mm;
            background: #ffffff;
            overflow: hidden;
        }

        .name-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5mm 2.4mm;
            text-align: center;
        }

        .child-full-name {
            display: -webkit-box;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            overflow: hidden;
            color: #111827;
            font-size: 4.7mm;
            font-weight: 700;
            line-height: 0.9;
            white-space: normal;
            word-break: break-word;
            text-wrap: balance;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .notes-panel {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 0.45mm;
            padding: 1.4mm 1.8mm;
            text-align: left;
            color: #111827;
        }

        .notes-label {
            font-size: 2.2mm;
            font-weight: 700;
            line-height: 1;
        }

        .notes-content {
            max-width: 100%;
            overflow: hidden;
            font-size: 3.1mm;
            line-height: 1.1;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .child-checkin-code {
            color: #ffffff;
            font-weight: 700;
            font-size: 5.6mm;
            line-height: 1;
            letter-spacing: 0.4mm;
            transform: rotate(-90deg);
            transform-origin: center;
            white-space: nowrap;
        }

        .tags-row {
            min-width: 0;
            display: grid;
            grid-template-columns: repeat(var(--tag-count), minmax(0, 1fr));
            gap: 1mm;
        }

        .tag-badge {
            min-width: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8mm;
            padding: 0 1.2mm;
            font-size: 3.1mm;
            font-weight: 700;
            line-height: 1.05;
            letter-spacing: 0.03em;
            text-align: center;
            text-transform: uppercase;
            color: #111827;
            background: #ffffff;
            overflow: hidden;
            border-radius: 2mm;
            border: 0.45px solid #111827;
        }

        .tag-icon {
            width: 3.2mm;
            height: 3.2mm;
            flex: 0 0 auto;
        }

        .tag-label {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .actions {
            margin-top: 1.5rem;
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .preview-label {
            margin-bottom: 0.75rem;
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>

    <div class="print-controls no-print">
        <div class="controls-row">
            <div class="print-control">
                <label for="margin-top">Margin Top (mm)</label>
                <input id="margin-top" type="number" min="0" step="0.1" wire:model.live="marginTop">
                @error('marginTop')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="print-control">
                <label for="margin-right">Margin Right (mm)</label>
                <input id="margin-right" type="number" min="0" step="0.1" wire:model.live="marginRight">
                @error('marginRight')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="print-control">
                <label for="margin-bottom">Margin Bottom (mm)</label>
                <input id="margin-bottom" type="number" min="0" step="0.1" wire:model.live="marginBottom">
                @error('marginBottom')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="print-control">
                <label for="margin-left">Margin Left (mm)</label>
                <input id="margin-left" type="number" min="0" step="0.1" wire:model.live="marginLeft">
                @error('marginLeft')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="controls-row">
            <div class="print-control">
                <label for="print-height">Height (mm)</label>
                <input id="print-height" type="number" min="1" step="0.1" wire:model.live="printHeight">
                @error('printHeight')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="print-control">
                <label for="print-width">Width (mm)</label>
                <input id="print-width" type="number" min="1" step="0.1" wire:model.live="printWidth">
                @error('printWidth')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="print-control print-control-button">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-secondary" wire:click="openLoadSettingsModal">
                    Load Settings
                </button>
            </div>
            <div class="print-control print-control-button">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary" wire:click="openSaveSettingsModal">
                    Save Settings
                </button>
            </div>
        </div>

        @if ($statusMessage !== null)
            <div class="print-status">{{ $statusMessage }}</div>
        @endif
    </div>

    @if ($showLoadSettingsModal)
        <div class="modal-backdrop no-print">
            <div class="modal-card">
                <div class="modal-title">Load Print Settings</div>
                <div class="print-control">
                    <label for="load-print-setting">Saved Settings</label>
                    <select id="load-print-setting" wire:model="loadPrintSettingId">
                        <option value="">Select a saved setting...</option>
                        @foreach ($savedPrintSettings as $savedPrintSetting)
                            <option value="{{ $savedPrintSetting['id'] }}">{{ $savedPrintSetting['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" wire:click="closeLoadSettingsModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="loadPrintSetting">Load</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showSaveSettingsModal)
        <div class="modal-backdrop no-print">
            <form wire:submit="savePrintSetting" class="modal-card">
                <div class="modal-title">Save Print Settings</div>
                <div class="print-control">
                    <label for="save-print-setting-name">Setting Name</label>
                    <input
                        id="save-print-setting-name"
                        type="text"
                        wire:model.live="saveSettingName"
                        list="saved-print-setting-names"
                        placeholder="Brother 90x30"
                    >
                    <datalist id="saved-print-setting-names">
                        @foreach ($savedPrintSettings as $savedPrintSetting)
                            <option value="{{ $savedPrintSetting['name'] }}"></option>
                        @endforeach
                    </datalist>
                    <span class="modal-help">Using an existing name will overwrite that saved setting.</span>
                    @error('saveSettingName')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="print-control">
                    <label for="save-existing-setting">Existing Settings</label>
                    <select id="save-existing-setting" wire:model.live="saveExistingSettingId">
                        <option value="">Type a new name or pick one to overwrite...</option>
                        @foreach ($savedPrintSettings as $savedPrintSetting)
                            <option value="{{ $savedPrintSetting['id'] }}">{{ $savedPrintSetting['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" wire:click="closeSaveSettingsModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    @endif

    <div class="no-print preview-label">Sticker Preview</div>

    <div class="sticker-preview">
        <div class="sticker">
            <div class="child-checkin-code-rail print-black-fill">
                <span class="child-checkin-code">{{ $checkinCode }}</span>
            </div>
            <div @class(['sticker-right', 'has-tags' => $tagCount > 0])>
                <div @class(['top-row', 'top-row-full' => ! $hasGuardianNote])>
                    <div class="panel name-panel">
                        <span class="child-full-name">{{ trim($childKnownAs.' '.$childLastName) }}</span>
                    </div>
                    @if ($hasGuardianNote)
                        <div class="panel notes-panel">
                            <span class="notes-label">Notes:</span>
                            <span class="notes-content">{{ $guardianNote }}</span>
                        </div>
                    @endif
                </div>
                @if ($tagCount > 0)
                    <div class="tags-row" style="--tag-count: {{ $tagCount }};">
                        @foreach ($childTags as $childTag)
                            <span class="tag-badge">
                                <x-filament::icon icon="heroicon-s-tag" class="tag-icon" />
                                <span class="tag-label">{{ $childTag }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="actions no-print">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            Print Sticker
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.close()">
            Close
        </button>
    </div>
</div>
