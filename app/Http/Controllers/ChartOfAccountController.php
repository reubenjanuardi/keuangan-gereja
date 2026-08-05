<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChartOfAccountController extends Controller
{
    // READ: Menampilkan daftar CoA
    public function index()
    {
        $coaList = ChartOfAccount::orderBy('kode_akun', 'asc')->paginate(15);
        return Inertia::render('CoA/Index', ['coaList' => $coaList]);
    }

    // CREATE: Menampilkan form tambah
    public function create()
    {
        // Tarik SEMUA akun (baik induk maupun anak), urutkan berdasarkan kode_akun
        $coaList = ChartOfAccount::orderBy('kode_akun', 'asc')->get();

        return Inertia::render('Vouchers/Create', [
            'coaList' => $coaList
        ]);
    }

    // STORE: Menyimpan data CoA baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_akun' => 'required|string|unique:chart_of_accounts,kode_akun|max:255',
            'nama_akun' => 'required|string|max:255',
            'kategori' => 'required|in:Penerimaan,Pengeluaran,Kas & Bank,Hutang / Piutang',
            'parent_code' => 'nullable|exists:chart_of_accounts,kode_akun',
            'is_postable' => 'boolean',
        ]);

        ChartOfAccount::create($validated);

        return redirect()->route('coa.index')->with('success', 'Mata Anggaran berhasil ditambahkan.');
    }

    // EDIT: Menampilkan form edit
    public function edit(ChartOfAccount $coa)
    {
        $parents = ChartOfAccount::where('is_postable', false)
            ->where('kode_akun', '!=', $coa->kode_akun) // Jangan jadikan diri sendiri sebagai parent
            ->orderBy('kode_akun')
            ->get();

        return Inertia::render('CoA/Edit', [
            'coa' => $coa,
            'parents' => $parents
        ]);
    }

    // UPDATE: Menyimpan perubahan
    public function update(Request $request, ChartOfAccount $coa)
    {
        $validated = $request->validate([
            // kode_akun tidak divalidasi karena tidak boleh diubah (read-only di form)
            'nama_akun' => 'required|string|max:255',
            'kategori' => 'required|in:Penerimaan,Pengeluaran,Kas & Bank,Hutang / Piutang',
            'parent_code' => 'nullable|exists:chart_of_accounts,kode_akun',
            'is_postable' => 'boolean',
        ]);

        $coa->update($validated);

        return redirect()->route('coa.index')->with('success', 'Mata Anggaran berhasil diperbarui.');
    }

    // DESTROY: Menghapus CoA
    public function destroy(ChartOfAccount $coa)
    {
        try {
            $coa->delete();
            return redirect()->route('coa.index')->with('success', 'Mata Anggaran berhasil dihapus.');
        } catch (\Exception $e) {
            // Error biasanya terjadi jika CoA ini sudah dipakai di tabel transactions (Foreign Key Restrict)
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus: Akun ini sedang digunakan dalam transaksi.']);
        }
    }
}
