<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sticker - {{ $childKnownAs }} {{ $childLastName }}</title>
    @livewireStyles
</head>
<body>
    <livewire:keeper.attendance-sticker-printer
        :attendance-id="$attendanceId"
        :child-known-as="$childKnownAs"
        :child-last-name="$childLastName"
        :checkin-code="$checkinCode"
        :child-tags="$childTags"
        :guardian-note="$guardianNote"
    />

    @livewireScripts
</body>
</html>
