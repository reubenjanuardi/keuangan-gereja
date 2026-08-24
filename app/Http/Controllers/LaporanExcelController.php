<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Services\ExcelReportService;
use App\Services\LaporanRealisasiService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanExcelController extends Controller
{
    public function __construct(
        protected ExcelReportService $excelReportService
    ) {}

    /**
     * Download Excel for Laporan Buku Besar
     */
    public function bukuBesar(Request $request): BinaryFileResponse
    {
        abort_unless(auth()->user()?->can('keuangan.laporan.export'), 403, 'Anda tidak memiliki izin untuk mengunduh laporan keuangan.');

        ActivityLog::log(
            description: 'Mengunduh Laporan Buku Besar Excel',
            logName: 'keuangan',
            properties: ['params' => $request->query()]
        );

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

        $filePath = $this->excelReportService->generateBukuBesarXlsx(
            $reportData,
            $startDate,
            $endDate,
            $kodeAkun,
            $jenisVoucher
        );

        $fileName = 'Laporan-Buku-Besar-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download Excel for Laporan Jurnal Transaksi
     */
    public function jurnal(Request $request): BinaryFileResponse
    {
        abort_unless(auth()->user()?->can('keuangan.laporan.export'), 403, 'Anda tidak memiliki izin untuk mengunduh laporan keuangan.');

        ActivityLog::log(
            description: 'Mengunduh Laporan Jurnal Transaksi Excel',
            logName: 'keuangan',
            properties: ['params' => $request->query()]
        );

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
            Voucher::select('tanggal')
                ->whereColumn('vouchers.no_bukti', 'transactions.no_bukti')
        )->get();

        $filePath = $this->excelReportService->generateJurnalXlsx(
            $reportData,
            $startDate,
            $endDate,
            $kategori
        );

        $fileName = 'Laporan-Jurnal-Transaksi-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download Excel for Laporan Realisasi Mingguan
     */
    public function realisasiMingguan(Request $request): BinaryFileResponse
    {
        abort_unless(auth()->user()?->can('keuangan.laporan.export'), 403, 'Anda tidak memiliki izin untuk mengunduh laporan keuangan.');

        ActivityLog::log(
            description: 'Mengunduh Laporan Realisasi Mingguan Excel',
            logName: 'keuangan',
            properties: ['params' => $request->query()]
        );

        $startDate = $request->query('startDate') ?: now()->startOfWeek()->toDateString();
        $endDate = $request->query('endDate') ?: now()->endOfWeek()->toDateString();
        $mingguKe = $request->query('mingguKe') ?: '';

        $reportData = app(LaporanRealisasiService::class)->getWeeklyReport($startDate, $endDate);

        $filePath = $this->excelReportService->generateRealisasiMingguanXlsx(
            $reportData,
            $startDate,
            $endDate,
            $mingguKe
        );

        $fileName = 'Laporan-Realisasi-Mingguan-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
