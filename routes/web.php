<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/mobile-callback', [App\Http\Controllers\API\AUTH\AuthController::class, 'handleMobileCallback']);
