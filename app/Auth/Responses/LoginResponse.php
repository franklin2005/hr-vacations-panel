<?php

namespace App\Auth\Responses;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user?->role === 'admin') {
            return redirect()->intended('/admin');
        }

        if ($user?->role === 'employee' && $user->employee_id) {
            return redirect()->intended('/employee');
        }

        Auth::logout();

        return redirect()->to('/auth/login');
    }
}
