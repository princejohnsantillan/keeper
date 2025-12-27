<?php

use App\Subdomain;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    if(Subdomain::defined())
    {
        return redirect('/admin');
    }

    return redirect('/dashboard');

    //    return view('welcome');
});
