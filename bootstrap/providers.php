<?php

use App\Providers\Filament\AdminPanelProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    App\Providers\Filament\SettingsPanelProvider::class,
];
