<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass - {{ $gatepass->child->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 400px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }
        .activity-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .child-name {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        .qr-container {
            display: inline-block;
            padding: 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        .qr-container img {
            display: block;
            width: 250px;
            height: 250px;
        }
        .code {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 6px;
            font-family: 'Courier New', monospace;
            margin-bottom: 24px;
        }
        .details {
            text-align: left;
            font-size: 14px;
            color: #4b5563;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .details dt {
            font-weight: 600;
            color: #1f2937;
            margin-top: 12px;
        }
        .details dt:first-child { margin-top: 0; }
        .details dd { margin-top: 2px; }
        .footer {
            margin-top: 24px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="activity-title">{{ $gatepass->activity->title }}</div>
        <div class="child-name">{{ $gatepass->child->full_name }}</div>

        <div class="qr-container">
            <img src="{{ $qrImageUrl }}" alt="Gate Pass QR Code">
        </div>

        <div class="code">{{ $gatepass->code }}</div>

        <dl class="details">
            @if($gatepass->activity->starts_at)
                @php
                    $startsAt = $gatepass->activity->starts_at->setTimezone(config('app.display_timezone'));
                    $endsAt = $gatepass->activity->ends_at?->setTimezone(config('app.display_timezone'));
                @endphp
                <dt>When</dt>
                <dd>{{ $startsAt->format('l, F j, Y \a\t g:i A') }}</dd>
                @if($endsAt && ! $startsAt->isSameDay($endsAt))
                    <dt>Ends</dt>
                    <dd>{{ $endsAt->format('l, F j, Y \a\t g:i A') }}</dd>
                @endif
            @endif
            @if($gatepass->activity->location)
                <dt>Where</dt>
                <dd>{{ $gatepass->activity->location }}</dd>
            @endif
            <dt>Guardian</dt>
            <dd>{{ $gatepass->guardian->full_name }}</dd>
        </dl>

        <div class="footer">
            Present this QR code or enter the code manually for check-in and check-out.
        </div>
    </div>
</body>
</html>
