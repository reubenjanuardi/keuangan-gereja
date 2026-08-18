<?php

namespace App\Providers\Filament;

use App\Http\Middleware\FilamentRedirectToLogin;
use App\Models\AppSetting;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            // Nonaktifkan login page Filament bawaan.
            // Autentikasi ditangani oleh Laravel Breeze di route /login.
            // Filament akan meredirect ke /login jika belum terautentikasi.
            ->login(false)
            ->authGuard('web')
            ->brandName('Keuangan Gereja')
            ->brandLogo(function () {
                $logo = AppSetting::get('church_logo');
                if ($logo) {
                    $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
                    if (Storage::disk($disk)->exists($logo)) {
                        return Storage::disk($disk)->url($logo);
                    }
                }
                return null;
            })
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                // Middleware custom: redirect ke /login (Breeze) jika belum terautentikasi
                FilamentRedirectToLogin::class,
            ]);
    }
}
