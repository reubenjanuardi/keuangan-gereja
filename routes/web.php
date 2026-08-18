<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherPdfController;
use App\Http\Controllers\LaporanPdfController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Redirect /dashboard ke Filament admin panel.
// Route ini dipertahankan agar link lama tidak broken.
Route::get('/dashboard', function () {
    return redirect('/admin');
})->middleware(['auth', 'verified'])->name('dashboard');

// Redirect /admin/login ke Breeze login page (/login) agar hanya ada satu pintu login.
Route::get('/admin/login', function () {
    return redirect()->route('login');
})->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/vouchers/{voucher}/pdf', [VoucherPdfController::class, 'stream'])->name('vouchers.pdf');
    Route::get('/laporan/buku-besar/pdf', [LaporanPdfController::class, 'bukuBesar'])->name('laporan.buku-besar.pdf');
    Route::get('/laporan/jurnal/pdf', [LaporanPdfController::class, 'jurnal'])->name('laporan.jurnal.pdf');
    Route::get('/laporan/realisasi-mingguan/pdf', [LaporanPdfController::class, 'realisasiMingguan'])->name('laporan.realisasi-mingguan.pdf');
});


require __DIR__ . '/auth.php';
