<?php

namespace App\Filament\Settings\Resources\UserResource\Pages;

use App\Filament\Settings\Resources\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengguna'),
        ];
    }
}
