<x-mail::message>
Hi {{ $guardianName }},

You have successfully registered **{{ $childName }}** for **{{ $activityTitle }}**.

@php
    $displayTimezone = config('app.display_timezone');
    $displayStartsAt = $activityStartsAt?->setTimezone($displayTimezone);
    $displayEndsAt = $activityEndsAt?->setTimezone($displayTimezone);
    $showActivityEndsAt = $displayStartsAt !== null
        && $displayEndsAt !== null
        && ! $displayStartsAt->isSameDay($displayEndsAt);
@endphp

@if($activityStartsAt || $activityLocation)
<x-mail::table>
| | |
|:--|:--|
@if($activityStartsAt)
| **When** | {{ $displayStartsAt?->format('l, F j, Y \a\t g:i A') }} |
@endif
@if($showActivityEndsAt)
| **Ends at** | {{ $displayEndsAt?->format('l, F j, Y \a\t g:i A') }} |
@endif
@if($activityLocation)
| **Where** | {{ $activityLocation }} |
@endif
</x-mail::table>
@endif

Your Gate Pass code is:

<x-mail::panel>
<div style="text-align: center;">
<a href="{{ $gatepassUrl }}" target="_blank"><img src="{{ $qrImageUrl }}" alt="Gate Pass QR Code" style="width: 200px; height: 200px; margin: 0 auto 16px;"></a>
<div style="font-size: 24px; font-weight: bold; letter-spacing: 4px;">
<a href="{{ $gatepassUrl }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $code }}</a>
</div>
</div>
</x-mail::panel>

Please keep this code safe. You can present the QR code or enter the code manually for check-in and check-out.

If the QR code does not appear above, you can view your Gate Pass here:
<{{ $gatepassUrl }}>


@if($organizerMessage)
<div style="background-color: #f8f9fa; border-left: 4px solid #6c757d; padding: 16px 20px; margin: 24px 0; border-radius: 4px;">
<div style="font-weight: 600; margin-bottom: 12px; color: #495057;">A message from {{ $organizerName }}</div>
<div style="color: #212529; line-height: 1.6;">
{!! $organizerMessage !!}
</div>
<div style="font-size: 11px; color: #868e96; margin-top: 16px; font-style: italic;">
    This message is from the event organizer ({{ $organizerName }}), not Keeper.
</div>
</div>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
