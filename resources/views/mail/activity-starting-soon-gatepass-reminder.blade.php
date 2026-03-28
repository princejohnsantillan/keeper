<x-mail::message>
Hi {{ $guardianName }},

**{{ $activityTitle }}** will start in about 15 minutes. We are excited to see you and **{{ $childName }}**.

Please have your gatepass ready when you arrive. The Keeper can scan the QR code or use the gatepass code manually.

@if($activityStartsAt || $activityLocation)
<x-mail::table>
| | |
|:--|:--|
@if($activityStartsAt)
| **Starts at** | {{ $activityStartsAt->setTimezone(config('app.display_timezone'))->format('l, F j, Y \a\t g:i A') }} |
@endif
@if($activityLocation)
| **Location** | {{ $activityLocation }} |
@endif
</x-mail::table>
@endif

For your convenience, here is a copy of your gatepass.

<x-mail::panel>
<div style="text-align: center;">
<a href="{{ $gatepassUrl }}" target="_blank"><img src="{{ $qrImageUrl }}" alt="Gate Pass QR Code" style="width: 200px; height: 200px; margin: 0 auto 16px;"></a>
<div style="font-size: 24px; font-weight: bold; letter-spacing: 4px; font-family: monospace;">
<a href="{{ $gatepassUrl }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $code }}</a>
</div>
</div>
</x-mail::panel>

If the QR code does not appear above, you can view your Gate Pass here:
<{{ $gatepassUrl }}>

Please do not forget to present your gatepass when arriving with **{{ $childName }}**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
