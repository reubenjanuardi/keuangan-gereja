<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use App\Models\Voucher;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVoucher extends EditRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (): string => route('vouchers.pdf', $this->record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    /**
     * Ensure total_nominal is accurately computed before record save.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_nominal'] = VoucherResource::calculateTotalNominal($data['transactions'] ?? []);

        return $data;
    }

    /**
     * After Filament updates the parent record AND syncs relationship items to DB,
     * calculate total_nominal from DB and ensure all rows have correct kode_akun.
     */
    protected function afterSave(): void
    {
        /** @var Voucher $record */
        $record = $this->getRecord();

        // Ensure all transaction rows carry the header's kode_akun
        $record->transactions()->update(['kode_akun' => $record->kode_akun]);

        // Recalculate total_nominal from actual saved DB transactions
        $total = (float) $record->transactions()->sum('nominal');
        $record->updateQuietly(['total_nominal' => $total]);
    }
}
