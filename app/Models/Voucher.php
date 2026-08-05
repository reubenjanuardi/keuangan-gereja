<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $primaryKey = 'no_bukti';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'no_bukti', 'no_bukti');
    }

    /**
     * The primary chart of account (mata anggaran) for this voucher.
     * All transactions in this voucher share this same kode_akun.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'kode_akun', 'kode_akun');
    }
}
