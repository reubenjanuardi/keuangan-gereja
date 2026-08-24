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

class LaporanJurnal extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-jurnal';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?string $title = 'Laporan Jurnal';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('keuangan.laporan.view') ?? false;
    }

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $kategori = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();

        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'kategori' => $this->kategori,
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

                Select::make('kategori')
                    ->label('Kategori')
                    ->placeholder('-- Semua Kategori --')
                    ->options([
                        'Penerimaan' => 'Penerimaan',
                        'Pengeluaran' => 'Pengeluaran',
                        'Kas & Bank' => 'Kas & Bank',
                        'Hutang / Piutang' => 'Hutang / Piutang',
                    ])
                    ->live(),
            ])
            ->columns(3);
    }

    public function getReportDataProperty(): Collection
    {
        $query = Transaction::query()
            ->with(['chartOfAccount.parent', 'voucher'])
            ->whereHas('voucher', function ($q) {
                if ($this->startDate) {
                    $q->where('tanggal', '>=', $this->startDate);
                }
                if ($this->endDate) {
                    $q->where('tanggal', '<=', $this->endDate);
                }
            });

        if ($this->kategori) {
            $query->whereHas('chartOfAccount', function ($q) {
                $q->where('kategori', $this->kategori);
            });
        }

        return $query->orderBy(
            \App\Models\Voucher::select('tanggal')
                ->whereColumn('vouchers.no_bukti', 'transactions.no_bukti')
        )->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (): string => route('laporan.jurnal.pdf', [
                    'startDate' => $this->startDate ?? '',
                    'endDate' => $this->endDate ?? '',
                    'kategori' => $this->kategori ?? '',
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
