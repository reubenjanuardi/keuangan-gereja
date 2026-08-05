<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasUuids; // Mengaktifkan UUID otomatis

    protected $guarded = [];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'no_bukti', 'no_bukti');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'kode_akun', 'kode_akun');
    }
}
