<x-mail::message>
# You have successfully registered!

Hi {{ $guardianName }},

You have successfully registered **{{ $childName }}** for **{{ $activityTitle }}**.

Your Gate Pass code is:

<x-mail::panel>
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 4px;">
{{ $code }}
</div>
</x-mail::panel>

Please keep this code safe. You will need it for check-in and check-out.


@if($organizerMessage)
<div style="background-color: #f8f9fa; border-left: 4px solid #6c757d; padding: 16px 20px; margin: 24px 0; border-radius: 4px;">
<div style="font-weight: 600; margin-bottom: 12px; color: #495057;">A message from {{ $organizerName }}</div>
<div style="color: #212529; line-height: 1.6;">
{!! $organizerMessage !!}
</div>
<div style="font-size: 11px; color: #868e96; margin-top: 16px; font-style: italic;">
The message in this block is from the organizer ({{ $organizerName }}), not from Keeper.
</div>
</div>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
