<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaporanPdfController extends Controller
{
    /**
     * Stream PDF for Laporan Buku Besar
     */
    public function bukuBesar(Request $request): Response
    {
        $startDate = $request->query('startDate') ?: now()->startOfMonth()->toDateString();
        $endDate = $request->query('endDate') ?: now()->endOfMonth()->toDateString();
        $kodeAkun = $request->query('kodeAkun');
        $jenisVoucher = $request->query('jenisVoucher');

        $reportData = Transaction::query()
            ->with(['chartOfAccount', 'voucher'])
            ->whereHas('voucher', function ($q) use ($startDate, $endDate, $jenisVoucher) {
                if ($startDate) {
                    $q->where('tanggal', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('tanggal', '<=', $endDate);
                }
                if ($jenisVoucher) {
                    $q->where('jenis_voucher', $jenisVoucher);
                }
            })
            ->when($kodeAkun, fn ($q) => $q->where('kode_akun', $kodeAkun))
            ->get()
            ->groupBy('kode_akun');

        $pdf = Pdf::loadView('pdf.laporan-buku-besar', compact('reportData', 'startDate', 'endDate', 'kodeAkun', 'jenisVoucher'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Buku-Besar.pdf');
    }

    /**
     * Stream PDF for Laporan Jurnal
     */
    public function jurnal(Request $request): Response
    {
        $startDate = $request->query('startDate') ?: now()->startOfMonth()->toDateString();
        $endDate = $request->query('endDate') ?: now()->endOfMonth()->toDateString();
        $kategori = $request->query('kategori');

        $query = Transaction::query()
            ->with(['chartOfAccount.parent', 'voucher'])
            ->whereHas('voucher', function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where('tanggal', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('tanggal', '<=', $endDate);
                }
            });

        if ($kategori) {
            $query->whereHas('chartOfAccount', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        }

        $reportData = $query->orderBy(
            \App\Models\Voucher::select('tanggal')
                ->whereColumn('vouchers.no_bukti', 'transactions.no_bukti')
        )->get();

        $pdf = Pdf::loadView('pdf.laporan-jurnal', compact('reportData', 'startDate', 'endDate', 'kategori'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Jurnal.pdf');
    }
}
