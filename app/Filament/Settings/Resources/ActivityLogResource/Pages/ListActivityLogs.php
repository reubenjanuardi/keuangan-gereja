<?php

namespace App\Filament\Settings\Resources\ActivityLogResource\Pages;

use App\Filament\Settings\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
