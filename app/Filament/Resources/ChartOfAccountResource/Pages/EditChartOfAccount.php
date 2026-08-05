<?php

namespace App\Filament\Resources\ChartOfAccountResource\Pages;

use App\Filament\Resources\ChartOfAccountResource;
use App\Models\ChartOfAccount;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditChartOfAccount extends EditRecord
{
    protected static string $resource = ChartOfAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (ChartOfAccount $record, Actions\DeleteAction $action): void {
                    // Cek apakah akun masih punya anak (child accounts)
                    if (ChartOfAccount::where('parent_code', $record->kode_akun)->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Akun Tidak Dapat Dihapus')
                            ->body('Akun "' . $record->nama_akun . '" masih memiliki akun anak. Hapus atau pindahkan akun anak terlebih dahulu.')
                            ->send();
                        $action->cancel();
                        return;
                    }

                    // Cek apakah akun sudah punya riwayat transaksi
                    if ($record->transactions()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Akun Tidak Dapat Dihapus')
                            ->body('Akun "' . $record->nama_akun . '" sudah memiliki riwayat transaksi dan tidak dapat dihapus.')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
