<x-mail::message>
Hello,

Keeper is sharing a newly available activity: **{{ $activityTitle }}**.

@if($organizationName || $activityStartsAt || $activityLocation)
<x-mail::table>
| | |
|:--|:--|
@if($organizationName)
| **Organizer** | {{ $organizationName }} |
@endif
@if($activityStartsAt)
| **When** | {{ $activityStartsAt->setTimezone(config('app.display_timezone'))->format('l, F j, Y \a\t g:i A') }} |
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
