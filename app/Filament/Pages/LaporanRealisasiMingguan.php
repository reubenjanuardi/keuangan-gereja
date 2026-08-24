<?php

namespace App\Filament\Pages;

use App\Services\LaporanRealisasiService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;
use Carbon\Carbon;

class LaporanRealisasiMingguan extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-realisasi-mingguan';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?string $title = 'Laporan Realisasi Mingguan';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('keuangan.laporan.view') ?? false;
    }

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $mingguKe = '';
    public string $activeTab = 'all';

    public function mount(): void
    {
        // Default to current week (e.g. Monday to Sunday)
        $now = now();
        $this->startDate = $now->copy()->startOfWeek()->toDateString();
        $this->endDate = $now->copy()->endOfWeek()->toDateString();
        $this->mingguKe = (string) $now->weekOfMonth;

        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'mingguKe' => $this->mingguKe,
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

                TextInput::make('mingguKe')
                    ->label('Minggu Ke-')
                    ->placeholder('Contoh: 1 / 2 / 3')
                    ->live(),
            ])
            ->columns(3);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getReportDataProperty(): array
    {
        if (! $this->startDate || ! $this->endDate) {
            return [
                'penerimaan' => [],
                'totalPenerimaan' => 0,
                'pengeluaran' => [],
                'totalPengeluaran' => 0,
                'kasBank' => [],
                'totalSaldoAwal' => 0,
                'totalSaldoAkhir' => 0,
                'surplusDefisit' => 0,
            ];
        }

        return app(LaporanRealisasiService::class)->getWeeklyReport($this->startDate, $this->endDate);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (): string => route('laporan.realisasi-mingguan.pdf', [
                    'startDate' => $this->startDate ?? '',
                    'endDate' => $this->endDate ?? '',
                    'mingguKe' => $this->mingguKe ?? '',
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
