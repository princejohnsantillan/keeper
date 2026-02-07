<?php

use App\Facades\Subdomain;
use App\Http\Controllers\Keeper\AcceptInvitationController;
use App\Http\Controllers\Keeper\PrintAttendanceStickerController;
use App\Http\Controllers\Keeper\WalkInRegistrationController;
use App\Http\Middleware\EnsureKeeperAuthenticated;
use App\Http\Middleware\RequireOrganizationSubdomain;
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

Route::middleware([RequireOrganizationSubdomain::class, EnsureKeeperAuthenticated::class])->group(function () {
    Route::get('/walk-ins/activities/{activity}', [WalkInRegistrationController::class, 'create'])
        ->name('keeper.walk-ins.create');
    Route::post('/walk-ins/activities/{activity}', [WalkInRegistrationController::class, 'store'])
        ->name('keeper.walk-ins.store');
});

// Print sticker route (uses ULID attendance ID for security)
Route::get('/admin/attendance/{attendance}/print', PrintAttendanceStickerController::class)
    ->name('filament.keeper.attendance.print');
