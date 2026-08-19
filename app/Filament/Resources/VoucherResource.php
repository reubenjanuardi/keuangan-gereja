<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\ChartOfAccount;
use App\Models\Voucher;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';

    protected static ?string $recordTitleAttribute = 'no_bukti';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('no_bukti')
                ->label('No Bukti')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->rule('regex:/^[^\s,]+$/')
                ->validationMessages([
                    'regex' => 'Format Nomor Bukti harus kontinu (menyambung) dan tidak boleh mengandung spasi atau tanda koma (,).',
                    'unique' => 'Nomor Bukti sudah digunakan.',
                ])
                ->dehydrateStateUsing(fn (?string $state) => $state ? str_replace([',', ' '], ['', ''], $state) : $state)
                ->disabled(fn(string $operation): bool => $operation === 'edit')
                ->dehydrated(fn(string $operation): bool => $operation === 'create'),

            DatePicker::make('tanggal')
                ->required()
                ->default(fn(): string => now()->toDateString()),

            TextInput::make('pihak_terkait')
                ->label('Pihak Terkait')
                ->required()
                ->maxLength(255),

            Select::make('jenis_voucher')
                ->label('Jenis Voucher')
                ->required()
                ->options([
                    'BKM' => 'Bukti Kas Masuk (BKM)',
                    'BKK' => 'Bukti Kas Keluar (BKK)',
                    'BBM' => 'Bukti Bank Masuk (BBM)',
                    'BBK' => 'Bukti Bank Keluar (BBK)',
                ])
                ->live()
                ->afterStateUpdated(function (?string $state, $set, $get, string $operation): void {
                    if ($operation !== 'create' || ! $state) {
                        return;
                    }

                    $currentNo = (string) ($get('no_bukti') ?? '');
                    $prefixes = ['BKM', 'BKK', 'BBM', 'BBK'];
                    $matched = false;

                    foreach ($prefixes as $prefix) {
                        if (str_starts_with($currentNo, $prefix)) {
                            $suffix = substr($currentNo, strlen($prefix));
                            $set('no_bukti', $state . $suffix);
                            $matched = true;
                            break;
                        }
                    }

                    if (! $matched) {
                        $set('no_bukti', $state . $currentNo);
                    }
                }),

            // ─── Mata Anggaran ────────────────────────────────────────────────────
            // Satu voucher = satu mata anggaran. Dipilih di level header.
            // Nilai ini akan dipropagasi ke semua baris transactions saat disimpan.
            Select::make('kode_akun')
                ->label('Mata Anggaran (Kode Akun)')
                ->required()
                ->searchable()
                ->preload()
                ->optionsLimit(500)
                ->options(fn (): array => static::getCoaTreeOptions())
                ->disableOptionWhen(function (?string $value): bool {
                    if (! $value) return false;
                    static $nonPostable = null;
                    if ($nonPostable === null) {
                        $nonPostable = ChartOfAccount::where('is_postable', false)->pluck('kode_akun')->flip()->toArray();
                    }
                    return isset($nonPostable[$value]);
                })
                ->helperText('Semua baris item dalam voucher ini akan dicatat pada mata anggaran yang sama.'),

            // ─── Detail Item ──────────────────────────────────────────────────────
            Repeater::make('transactions')
                ->label('Detail Item Transaksi')
                ->relationship()
                ->live()
                ->minItems(1)
                ->addActionLabel('Tambah Item')
                ->afterStateHydrated(function ($state, $set): void {
                    $set('total_nominal', static::calculateTotalNominal($state ?? []));
                })
                ->afterStateUpdated(function ($state, $set): void {
                    $set('total_nominal', static::calculateTotalNominal($state ?? []));
                })
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Repeater $component): array {
                    // Access the Livewire component's public property for kode_akun.
                    // The Livewire state always reflects the current form header value.
                    $livewire = $component->getLivewire();
                    $data['kode_akun'] = data_get($livewire, 'data.kode_akun') ?? '';
                    return $data;
                })
                ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Repeater $component): array {
                    $livewire = $component->getLivewire();
                    $kodeAkun = data_get($livewire, 'data.kode_akun');
                    if ($kodeAkun) {
                        $data['kode_akun'] = $kodeAkun;
                    }
                    return $data;
                })
                ->schema([
                    TextInput::make('uraian')
                        ->label('Keterangan / Uraian')
                        ->required()
                        ->maxLength(1000)
                        ->columnSpan(2),

                    TextInput::make('nominal')
                        ->required()
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix('Rp')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($get, $set): void {
                            $transactions = $get('../../') ?? [];
                            $set('../../total_nominal', static::calculateTotalNominal($transactions));
                        }),
                ])
                ->columns(3),

            TextInput::make('total_nominal')
                ->label('Total Nominal')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->readOnly()
                ->default(0)
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('no_bukti')
                    ->label('No Bukti')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('pihak_terkait')
                    ->label('Pihak Terkait')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis_voucher')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'BKM' => 'BKM (Kas Masuk)',
                        'BKK' => 'BKK (Kas Keluar)',
                        'BBM' => 'BBM (Bank Masuk)',
                        'BBK' => 'BBK (Bank Keluar)',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Masuk', 'BKM', 'BBM' => 'success',
                        'Keluar', 'BKK', 'BBK' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total_nominal')
                    ->label('Total Nominal')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(fn($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('cetak_pdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn (Voucher $record): string => route('vouchers.pdf', $record))
                        ->openUrlInNewTab(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->authorizeIndividualRecords('delete')
                        ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each->delete()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function calculateTotalNominal(array $transactions): float
    {
        return collect($transactions)->sum(function (mixed $transaction): float {
            if (! is_array($transaction)) {
                return 0;
            }

            return (float) ($transaction['nominal'] ?? 0);
        });
    }

    public static function formatCoaLabel(ChartOfAccount $coa): string
    {
        $depth = substr_count($coa->kode_akun, '.');
        $indent = str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $depth);
        $icon = ! $coa->is_postable ? '📂' : '↳';

        return "{$indent}{$icon} {$coa->kode_akun} - {$coa->nama_akun}";
    }

    public static function getCoaTreeOptions(): array
    {
        return ChartOfAccount::orderBy('kode_akun')
            ->get()
            ->mapWithKeys(function (ChartOfAccount $coa) {
                return [$coa->kode_akun => static::formatCoaLabel($coa)];
            })
            ->toArray();
    }
}
