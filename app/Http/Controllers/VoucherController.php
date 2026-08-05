<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VoucherController extends Controller
{
    /**
     * Menampilkan halaman form input Vue
     */
    public function create()
    {
        // Hanya menarik CoA yang is_postable = true (bukan induk) untuk dipilih di dropdown
        $coaList = ChartOfAccount::where('is_postable', true)
            ->orderBy('kode_akun')
            ->get();

        return Inertia::render('Vouchers/Create', [
            'coaList' => $coaList
        ]);
    }

    /**
     * Memproses penyimpanan data dari form Vue ke Database
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Ketat
        $validated = $request->validate([
            // Memastikan no_bukti unik dan tidak mengandung koma
            'no_bukti' => ['required', 'string', 'unique:vouchers,no_bukti', 'regex:/^[^,]+$/'],
            'tanggal' => 'required|date',
            'pihak_terkait' => 'required|string|max:255',
            'jenis_voucher' => 'required|in:Masuk,Keluar',

            // Validasi Array untuk detail rincian transaksi
            'rincian' => 'required|array|min:1',
            'rincian.*.kode_akun' => 'required|exists:chart_of_accounts,kode_akun',
            'rincian.*.uraian' => 'required|string|max:1000',
            'rincian.*.nominal' => 'required|numeric|min:1',
        ], [
            'no_bukti.regex' => 'Format Nomor Bukti harus kontinu dan tidak boleh menggunakan tanda koma.',
        ]);

        // 2. Kalkulasi Total Nominal otomatis di sisi server
        $totalNominal = collect($validated['rincian'])->sum('nominal');

        try {
            // 3. Eksekusi Database Transaction
            DB::transaction(function () use ($validated, $totalNominal) {

                // Simpan Header ke tabel Vouchers
                $voucher = Voucher::create([
                    'no_bukti' => $validated['no_bukti'],
                    'tanggal' => $validated['tanggal'],
                    'pihak_terkait' => $validated['pihak_terkait'],
                    'jenis_voucher' => $validated['jenis_voucher'],
                    'total_nominal' => $totalNominal,
                ]);

                // Simpan Detail ke tabel Transactions menggunakan perulangan
                foreach ($validated['rincian'] as $item) {
                    Transaction::create([
                        'no_bukti' => $voucher->no_bukti,
                        'kode_akun' => $item['kode_akun'],
                        'uraian' => $item['uraian'],
                        'nominal' => $item['nominal'],
                    ]);
                }
            });

            // 4. Redirect kembali dengan Flash Message sukses
            return redirect()->back()->with('success', 'Voucher berhasil direkam ke dalam sistem!');
        } catch (\Exception $e) {
            // Rollback otomatis terjadi jika ada error
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        // Mengambil data voucher beserta relasi transaksinya, diurutkan dari yang terbaru
        $vouchers = Voucher::with('transactions')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Dibatasi 10 baris per halaman agar ringan

        return Inertia::render('Vouchers/Index', [
            'vouchers' => $vouchers
        ]);
    }
}
