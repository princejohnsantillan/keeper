<x-mail::message>
Hi {{ $userName }},

**{{ $inviterName }}** has invited you to join **{{ $organizationName }}** as a **{{ $role }}**.

As a {{ $role }}, you will have access to the Keeper platform to manage activities, gatepasses, and more.

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

This invitation will expire on {{ $expiresAt->setTimezone(config('app.display_timezone'))->format('l, F j, Y \a\t g:i A') }}.

If you have any questions, please contact {{ $inviterName }} directly.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
