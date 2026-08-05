<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use App\Models\Voucher;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateVoucher extends CreateRecord
{
    protected static string $resource = VoucherResource::class;

    /**
     * Inject kode_akun from the voucher header into every transaction row
     * before Filament's Repeater processes the relationship.
     * This enforces the business rule: one voucher = one mata anggaran.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $kodeAkun = $data['kode_akun'] ?? null;

        // Propagate header kode_akun into each transaction row
        if ($kodeAkun && isset($data['transactions'])) {
            $data['transactions'] = array_map(function (array $tx) use ($kodeAkun): array {
                $tx['kode_akun'] = $kodeAkun;
                return $tx;
            }, $data['transactions']);
        }

        $data['total_nominal'] = VoucherResource::calculateTotalNominal($data['transactions'] ?? []);

        return $data;
    }

    /**
     * Wrap entire Voucher + Transactions creation in a DB transaction.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            return parent::handleRecordCreation($data);
        });
    }

    /**
     * After Filament creates the parent record AND saves relationship items to DB,
     * calculate total_nominal from DB and ensure all rows have correct kode_akun.
     */
    protected function afterCreate(): void
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
