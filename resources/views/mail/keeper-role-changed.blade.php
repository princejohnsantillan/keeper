<x-mail::message>
Hi {{ $userName }},

Your role in **{{ $organizationName }}** has been updated by **{{ $changedByName }}**.

<x-mail::table>
| | |
|:--|:--|
| **Previous role** | {{ $previousRole }} |
| **New role** | {{ $newRole }} |
</x-mail::table>

If you have any questions, please contact your organization administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
