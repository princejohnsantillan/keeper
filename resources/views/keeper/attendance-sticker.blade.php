<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sticker - {{ $childKnownAs }} {{ $childLastName }}</title>
    <style>
        @media print {


            .no-print {
                display: none !important;
            }

            .sticker {
                position: absolute !important;
                top: 2mm !important;
                left: 2mm !important;
                right: 2mm !important;
                margin: 0 auto !important;
                height: auto !important;
                padding: 2mm 4mm !important;
                width: 90mm !important;
                text-align: center !important;
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

        .sticker {
            width: 100mm;
            height: auto;
            background: white;
            border: 2px dashed #d1d5db;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2mm 5mm;
            text-align: center;
            gap: 1mm;
        }

        .sticker-row {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #111827;
            line-height: 1.25;
            text-align: center;
        }

        .child-name {
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.4mm;
            line-height: 1;
            min-width: 0;
        }

        .child-known-as {
            text-transform: uppercase;
            letter-spacing: 0.04em;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .child-last-name {
            flex-shrink: 0;
            font-weight: 400;
            line-height: 1;
            white-space: nowrap;
        }

        .child-checkin-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0.4px solid #111827;
            color: #ffffff;
            background-color: #111827;
            font-weight: 100;
            font-size: 18px;
            line-height: 1;
            padding: 0.2mm 0.8mm;
        }

        .child-tags,
        .child-note {
            font-size: 15px;
            font-weight: 500;
            color: #374151;
        }

        .child-tags {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1mm;
        }

        .tag-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 500;
            line-height: 1.2;
            letter-spacing: 0.05em;
            color: #111827;
            background: #ffffff;
            border-radius: 0;
            border: 0.5px solid #111827;
            padding: 0.8mm 1.6mm;
        }

        .child-note {
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
    <div class="no-print preview-label">Sticker Preview</div>

    <div class="sticker">
        <div class="sticker-row child-name">
            <span class="child-checkin-code" >{{ $checkinCode }}</span>
            <span class="child-known-as">{{ $childKnownAs }}</span>
            <span class="child-last-name">{{ $childLastName }}</span>

        </div>
        @if (count($childTags) > 0)
            <div class="sticker-row child-tags">
                @foreach ($childTags as $childTag)
                    <span class="tag-badge">{{ $childTag }}</span>
                @endforeach
            </div>
        @endif
        @if ($guardianNote !== '')
            <div class="sticker-row child-note">{{ $guardianNote }}</div>
        @endif
    </div>

    <div class="actions no-print">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            Print Sticker
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.close()">
            Close
        </button>
    </div>
</body>
</html>
