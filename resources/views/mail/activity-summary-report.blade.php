<x-mail::message>
# Activity Summary Report

Here is the summary report for **{{ $activityTitle }}**.

<x-mail::table>
| | |
|:--|:--|
| **Activity** | {{ $activityTitle }} |
@if($activityLocation)
| **Location** | {{ $activityLocation }} |
@endif
| **Start time** | {{ $activityStartsAt->setTimezone(config('app.display_timezone'))->format('l, F j, Y \a\t g:i A') }} |
| **End time** | {{ $activityEndsAt->setTimezone(config('app.display_timezone'))->format('l, F j, Y \a\t g:i A') }} |
</x-mail::table>

## Attendance Overview

<x-mail::table>
| Metric | Value |
|:--|:--|
| **Total registered** | {{ $totalRegistered }} |
| **Total attended** | <span style="color: #16a34a; font-weight: bold;">{{ $totalAttended }}</span> |
| **Boys** | <span style="color: #7c3aed;">{{ $boysCount }}</span> |
| **Girls** | <span style="color: #e11d48;">{{ $girlsCount }}</span> |
| **No-shows** | @if($noShowsCount > 0)<span style="color: #dc2626; font-weight: bold;">{{ $noShowsCount }}</span>@else{{ $noShowsCount }}@endif |
| **Incomplete checkouts** | @if($incompleteCheckoutsCount > 0)<span style="color: #ea580c; font-weight: bold;">{{ $incompleteCheckoutsCount }}</span>@else{{ $incompleteCheckoutsCount }}@endif |
| **Unique guardians** | {{ $uniqueGuardians }} |
| **Average stay** | @if($averageStayMinutes !== null)<span style="color: #2563eb; font-weight: bold;">{{ intdiv($averageStayMinutes, 60) }}h {{ $averageStayMinutes % 60 }}m</span>@else—@endif |
</x-mail::table>

## Highlights

<x-mail::table>
| | Child | Time |
|:--|:--|:--|
| **First check-in** | {{ $firstCheckin['childName'] ?? '—' }} | @if(isset($firstCheckin['time']))<span style="color: #16a34a;">{{ $firstCheckin['time']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else—@endif |
| **Last check-in** | {{ $lastCheckin['childName'] ?? '—' }} | @if(isset($lastCheckin['time']))<span style="color: #16a34a;">{{ $lastCheckin['time']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else—@endif |
| **First check-out** | {{ $firstCheckout['childName'] ?? '—' }} | @if(isset($firstCheckout['time']))<span style="color: #2563eb;">{{ $firstCheckout['time']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else—@endif |
| **Last check-out** | {{ $lastCheckout['childName'] ?? '—' }} | @if(isset($lastCheckout['time']))<span style="color: #2563eb;">{{ $lastCheckout['time']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else—@endif |
</x-mail::table>

@if(count($tagCounts) > 0)
## Tag Distribution

<x-mail::table>
| Tag | Count |
|:--|:--|
@foreach($tagCounts as $tag => $count)
| {{ ucfirst($tag) }} | <span style="color: #7c3aed; font-weight: bold;">{{ $count }}</span> |
@endforeach
</x-mail::table>
@endif

@if(count($attendedChildren) > 0)
## Children Who Attended

<x-mail::table>
| # | Child | Check-in | Check-out |
|:--|:--|:--|:--|
@foreach($attendedChildren as $index => $child)
| {{ $index + 1 }} | {{ $child['childName'] }} | @if($child['checkedInAt'])<span style="color: #16a34a;">{{ $child['checkedInAt']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else—@endif | @if($child['checkedOutAt'])<span style="color: #2563eb;">{{ $child['checkedOutAt']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else<span style="color: #ea580c;">—</span>@endif |
@endforeach
</x-mail::table>
@endif

@if(count($noShowChildren) > 0)
## No-Shows

<x-mail::table>
| # | Child |
|:--|:--|
@foreach($noShowChildren as $index => $child)
| {{ $index + 1 }} | <span style="color: #dc2626;">{{ $child['childName'] }}</span> |
@endforeach
</x-mail::table>
@endif

@if(count($incompleteCheckoutChildren) > 0)
## Incomplete Checkouts

<x-mail::table>
| # | Child | Check-in |
|:--|:--|:--|
@foreach($incompleteCheckoutChildren as $index => $child)
| {{ $index + 1 }} | <span style="color: #ea580c;">{{ $child['childName'] }}</span> | @if($child['checkedInAt'])<span style="color: #16a34a;">{{ $child['checkedInAt']->setTimezone(config('app.display_timezone'))->format('g:i A') }}</span>@else—@endif |
@endforeach
</x-mail::table>
@endif

A detailed attendance spreadsheet is attached as CSV.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
