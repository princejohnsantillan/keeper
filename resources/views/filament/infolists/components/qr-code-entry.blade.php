@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    $code = $getState();
    $ulid = $getRecord()->id;
    $qrCodeData = 'data:image/png;base64,' . base64_encode((string) QrCode::format('png')->size(200)->margin(1)->generate($ulid));
@endphp

<div class="flex flex-col items-center gap-2">
    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
        <img src="{{ $qrCodeData }}" alt="QR Code for {{ $code }}" />
    </div>
    <span class="font-mono text-lg font-bold text-gray-900 dark:text-white">{{ $code }}</span>
</div>
