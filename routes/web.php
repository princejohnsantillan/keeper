<?php

use App\Http\Controllers\Keeper\AcceptInvitationController;
use App\Http\Controllers\Keeper\PrintAttendanceStickerController;
use App\Http\Middleware\RequireOrganizationSubdomain;
use App\Subdomain;
use Illuminate\Support\Facades\Route;

Route::get('/print', function () {
    echo 'This is my name';
});

Route::get('/', function () {

    if (Subdomain::defined()) {
        return redirect('/admin');
    }

    return redirect('/dashboard');

    //    return view('welcome');
});

// Keeper invitation acceptance routes
Route::middleware([RequireOrganizationSubdomain::class])->group(function () {
    Route::get('/admin/invitation/accept', [AcceptInvitationController::class, 'show'])
        ->name('filament.keeper.invitation.accept');
    Route::post('/admin/invitation/accept', [AcceptInvitationController::class, 'accept']);
});

// Print sticker route (uses ULID attendance ID for security)
Route::get('/admin/attendance/{attendance}/print', PrintAttendanceStickerController::class)
    ->name('filament.keeper.attendance.print');
