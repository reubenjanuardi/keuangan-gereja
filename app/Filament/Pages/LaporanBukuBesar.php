<?php

namespace App\Filament\Pages;

use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Collection;

class LaporanBukuBesar extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-buku-besar';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?string $title = 'Buku Besar';

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $kodeAkun = null;
    public ?string $jenisVoucher = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();

        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'kodeAkun' => $this->kodeAkun,
            'jenisVoucher' => $this->jenisVoucher,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('startDate')
                    ->label('Dari Tanggal')
                    ->required()
                    ->live(),

                DatePicker::make('endDate')
                    ->label('Sampai Tanggal')
                    ->required()
                    ->live(),

                Select::make('kodeAkun')
                    ->label('Kode Akun')
                    ->placeholder('-- Semua Akun --')
                    ->searchable()
                    ->options(
                        fn (): array => ChartOfAccount::where('is_postable', true)
                            ->orderBy('kode_akun')
                            ->get()
                            ->mapWithKeys(fn (ChartOfAccount $coa) => [$coa->kode_akun => "{$coa->kode_akun} - {$coa->nama_akun}"])
                            ->toArray()
                    )
                    ->live(),

                Select::make('jenisVoucher')
                    ->label('Jenis Voucher')
                    ->placeholder('-- Semua --')
                    ->options([
                        'BKM' => 'Bukti Kas Masuk (BKM)',
                        'BKK' => 'Bukti Kas Keluar (BKK)',
                        'BBM' => 'Bukti Bank Masuk (BBM)',
                        'BBK' => 'Bukti Bank Keluar (BBK)',
                        'Masuk' => 'Masuk',
                        'Keluar' => 'Keluar',
                    ])
                    ->live(),
            ])
            ->columns(4);
    }

    public function getReportDataProperty(): Collection
    {
        return Transaction::query()
            ->with(['chartOfAccount', 'voucher'])
            ->whereHas('voucher', function ($q) {
                if ($this->startDate) {
                    $q->where('tanggal', '>=', $this->startDate);
                }
                if ($this->endDate) {
                    $q->where('tanggal', '<=', $this->endDate);
                }
                if ($this->jenisVoucher) {
                    $q->where('jenis_voucher', $this->jenisVoucher);
                }
            })
            ->when($this->kodeAkun, fn ($q) => $q->where('kode_akun', $this->kodeAkun))
            ->get()
            ->groupBy('kode_akun');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (): string => route('laporan.buku-besar.pdf', [
                    'startDate' => $this->startDate ?? '',
                    'endDate' => $this->endDate ?? '',
                    'kodeAkun' => $this->kodeAkun ?? '',
                    'jenisVoucher' => $this->jenisVoucher ?? '',
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
