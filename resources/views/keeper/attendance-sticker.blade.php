<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sticker - {{ $childKnownAs }} {{ $childLastName }}</title>
    <style>
        :root {
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
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
            width: min(100%, 34rem);
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
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

        .print-control input {
            width: 100%;
            padding: 0.55rem 0.7rem;
            border: 1px solid #9ca3af;
            border-radius: 0.5rem;
            font: inherit;
            color: #111827;
            background: #ffffff;
        }

        .print-control input:focus {
            outline: 2px solid #4f46e5;
            outline-offset: 1px;
            border-color: #4f46e5;
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
            background: white;
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
            padding: 0 1.2mm;
            font-size: 3.1mm;
            font-weight: 700;
            line-height: 1.05;
            letter-spacing: 0.03em;
            text-align: center;
            text-transform: uppercase;
            color: #ffffff;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border-radius: 2mm;
            border: 0.45px solid #111827;
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
            color: white;
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
</head>
<body>
    @php
        $tagCount = count($childTags);
        $hasGuardianNote = $guardianNote !== '';
    @endphp

    <div class="print-controls no-print">
        <div class="print-control">
            <label for="print-width">Width (mm)</label>
            <input id="print-width" type="number" min="1" step="0.1" value="90" data-css-var="--print-width">
        </div>
        <div class="print-control">
            <label for="print-height">Height (mm)</label>
            <input id="print-height" type="number" min="1" step="0.1" value="30" data-css-var="--print-height">
        </div>
        <div class="print-control">
            <label for="print-margin-top">Top Margin (mm)</label>
            <input id="print-margin-top" type="number" min="0" step="0.1" value="1" data-css-var="--print-margin-top">
        </div>
        <div class="print-control">
            <label for="print-margin-right">Right Margin (mm)</label>
            <input id="print-margin-right" type="number" min="0" step="0.1" value="1" data-css-var="--print-margin-right">
        </div>
        <div class="print-control">
            <label for="print-margin-bottom">Bottom Margin (mm)</label>
            <input id="print-margin-bottom" type="number" min="0" step="0.1" value="1" data-css-var="--print-margin-bottom">
        </div>
        <div class="print-control">
            <label for="print-margin-left">Left Margin (mm)</label>
            <input id="print-margin-left" type="number" min="0" step="0.1" value="1" data-css-var="--print-margin-left">
        </div>
    </div>

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
                            <span class="tag-badge print-black-fill">{{ $childTag }}</span>
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
    <script>
        const rootStyle = document.documentElement.style;
        const printInputs = document.querySelectorAll('[data-css-var]');

        for (const printInput of printInputs) {
            const syncCssVariable = () => {
                const numericValue = Number.parseFloat(printInput.value);

                if (Number.isNaN(numericValue)) {
                    return;
                }

                rootStyle.setProperty(printInput.dataset.cssVar, `${numericValue}mm`);
            };

            syncCssVariable();
            printInput.addEventListener('input', syncCssVariable);
        }
    </script>
</body>
</html>
