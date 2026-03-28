<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Gatepass;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class GatepassPublicController extends Controller
{
    public function show(Request $request, Gatepass $gatepass): View
    {
        abort_unless($request->hasValidSignature(), 403);

        $gatepass->loadMissing(['activity', 'child', 'guardian']);

        return view('gatepass.show', [
            'gatepass' => $gatepass,
            'qrImageUrl' => $gatepass->getSignedQrImageUrl(),
        ]);
    }

    public function qrImage(Request $request, Gatepass $gatepass): Response
    {
        abort_unless($request->hasValidSignature(), 403);

        /** @var HtmlString|string $qrCode */
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($gatepass->code);

        $png = $qrCode instanceof HtmlString ? $qrCode->toHtml() : $qrCode;

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
