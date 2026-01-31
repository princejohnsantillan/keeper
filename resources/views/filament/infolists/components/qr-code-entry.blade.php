@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    $code = $getState();
    $qrCodeData = 'data:image/png;base64,' . base64_encode((string) QrCode::format('png')->size(200)->margin(1)->generate($code));
@endphp

<div class="flex flex-col items-center gap-2">
    <img
        src="{{ $qrCodeData }}"
        alt="QR Code for {{ $code }}"
        class="rounded-lg"
    />
</div>
