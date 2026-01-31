<?php

use App\Http\Controllers\Keeper\AcceptInvitationController;
use App\Http\Middleware\RequireOrganizationSubdomain;
use App\Subdomain;
use Illuminate\Support\Facades\Route;

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
