<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

class RedirectToAuthLogin extends FilamentAuthenticate
{
    protected function redirectTo($request): ?string
    {
        return '/auth/login';
    }
}
