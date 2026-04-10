<x-mail::message>
Hello,

Keeper is sharing a newly available activity: **{{ $activityTitle }}**.

@php
    $displayTimezone = config('app.display_timezone');
    $displayStartsAt = $activityStartsAt?->setTimezone($displayTimezone);
    $displayEndsAt = $activityEndsAt?->setTimezone($displayTimezone);
    $showActivityEndsAt = $displayStartsAt !== null
        && $displayEndsAt !== null
        && ! $displayStartsAt->isSameDay($displayEndsAt);
@endphp

@if($organizationName || $activityStartsAt || $activityLocation)
<x-mail::table>
| | |
|:--|:--|
@if($organizationName)
| **Organizer** | {{ $organizationName }} |
@endif
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

@if($activityDescription)
{{ $activityDescription }}
@endif

<x-mail::button :url="$registerUrl">
View Activity
</x-mail::button>

If you already have a guardian account, sign in and continue from the activity page.

If you do not have a guardian account yet, create one first:

<x-mail::button :url="$signupUrl" color="success">
Create Guardian Account
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
