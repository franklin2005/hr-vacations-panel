<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        if ($user->role === 'employee' && $user->employee_id) {
            return redirect()->intended('/employee');
        }

        Auth::logout();
    }

    return redirect()->away(Filament::getPanel('auth')?->getLoginUrl() ?? '/auth/login');
});

Route::get('/login', fn () => redirect()->to('/auth/login'));
