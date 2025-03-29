<?php

use App\Http\Controllers\Auth\RegisteredUserController;

Route::post('/auth/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');