<?php

use App\Facades\Subdomain;
use App\Http\Controllers\Keeper\PrintAttendanceStickerController;
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

// Print sticker route (uses ULID attendance ID for security)
Route::get('/admin/attendance/{attendance}/print', PrintAttendanceStickerController::class)
    ->name('filament.keeper.attendance.print');
