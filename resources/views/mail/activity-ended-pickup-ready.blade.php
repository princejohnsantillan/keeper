<x-mail::message>
Hi {{ $guardianName }},

**{{ $activityTitle }}** will end in about 15 minutes. Please prepare to pick up **{{ $childName }}**.

Please have this gatepass ready when you arrive. The keeper can scan the QR code or use the gatepass code manually.

@if($activityEndsAt || $activityLocation)
<x-mail::table>
| | |
|:--|:--|
@if($activityEndsAt)
| **Ends at** | {{ $activityEndsAt->format('l, F j, Y \a\t g:i A') }} |
@endif
@if($activityLocation)
| **Pickup location** | {{ $activityLocation }} |
@endif
</x-mail::table>
@endif

Your Gate Pass:

<x-mail::panel>
<div style="text-align: center;">
<img src="{{ $qrCode }}" alt="Gate Pass QR Code" style="width: 200px; height: 200px; margin: 0 auto 16px;">
<div style="font-size: 24px; font-weight: bold; letter-spacing: 4px; font-family: monospace;">
{{ $code }}
</div>
</div>
</x-mail::panel>

Please do not forget to present this gatepass when picking up {{ $childName }}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
