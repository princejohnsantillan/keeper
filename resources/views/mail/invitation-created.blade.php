<x-mail::message>
Hi {{ $inviteeName }},

You've been invited to **{{ $activityTitle }}** by **{{ $organizationName }}**.

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

Your invitation code is:

<x-mail::panel>
<div style="text-align: center;">
<div style="font-size: 28px; font-weight: bold; letter-spacing: 6px;">
{{ $invitationCode }}
</div>
</div>
</x-mail::panel>

Please keep this code safe. You will need to enter it when registering for this event.

@if($customMessage)
<div style="background-color: #f8f9fa; border-left: 4px solid #6c757d; padding: 16px 20px; margin: 24px 0; border-radius: 4px;">
<div style="font-weight: 600; margin-bottom: 12px; color: #495057;">A message from {{ $organizationName }}</div>
<div style="color: #212529; line-height: 1.6;">
{!! $customMessage !!}
</div>
<div style="font-size: 11px; color: #868e96; margin-top: 16px; font-style: italic;">
    This message is from the event organizer ({{ $organizationName }}), not Keeper.
</div>
</div>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
