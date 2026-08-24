<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use LogsActivity;

    protected $primaryKey = 'kode_akun';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'budget' => 'float',
        'is_postable' => 'boolean',
    ];

    public function getActivityLogName(): string
    {
        return 'keuangan';
    }

    public function getActivityLogTitle(): string
    {
        return "{$this->kode_akun} - {$this->nama_akun}";
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_code', 'kode_akun');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_code', 'kode_akun')
            ->with('children')
            ->orderBy('kode_akun');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'kode_akun', 'kode_akun');
    }

    public function getIsGroupAttribute(): bool
    {
        return !$this->is_postable || $this->children->isNotEmpty();
    }

    /**
     * Get direct transaction total balance for this single account.
     */
    public function getDirectBalanceAttribute(): float
    {
        $transactions = $this->transactions()->with('voucher')->get();
        $total = 0.0;

        foreach ($transactions as $tx) {
            $jenis = $tx->voucher->jenis_voucher ?? 'Masuk';
            $nominal = (float) $tx->nominal;

            if ($this->kategori === 'Pengeluaran') {
                $total += ($jenis === 'Keluar') ? $nominal : -$nominal;
            } else {
                $total += ($jenis === 'Masuk') ? $nominal : -$nominal;
            }
        }

        return $total;
    }

    /**
     * Calculate recursive balance for this account and all its children.
     */
    public function getTotalBalanceAttribute(): float
    {
        $balance = $this->direct_balance;

        foreach ($this->children as $child) {
            $balance += $child->total_balance;
        }

        return $balance;
    }
}
