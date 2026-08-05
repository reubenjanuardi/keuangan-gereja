<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

/**
 * Override Filament Authenticate middleware untuk redirect ke
 * halaman login Breeze (/login) alih-alih halaman login Filament
 * yang sudah dinonaktifkan.
 */
class FilamentRedirectToLogin extends FilamentAuthenticate
{
    protected function redirectTo($request): string
    {
        return route('login');
    }
}
