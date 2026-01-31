<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sticker - {{ $childName }}</title>
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
                text-align: center;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            height: 100%;
            background: white;
            border: 2px dashed #d1d5db;
            border-radius: 4px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 2mm 5mm;
            text-align: left;
        }

        .child-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .gatepass-code {
            font-size: 18px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 2px;
            font-family: 'Courier New', Courier, monospace;
            flex-shrink: 0;
            margin-left: 4mm;
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
        <div class="child-name">{{ $childName }}</div>
        <div class="gatepass-code">{{ $gatepassCode }}</div>
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
