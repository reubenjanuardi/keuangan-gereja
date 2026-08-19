<?php

namespace App\Filament\Resources\ChartOfAccountResource\Pages;

use App\Filament\Resources\ChartOfAccountResource;
use App\Models\ChartOfAccount;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListChartOfAccounts extends ListRecords
{
    protected static string $resource = ChartOfAccountResource::class;

    protected string $view = 'filament.resources.chart-of-account-resource.pages.list-chart-of-accounts';

    public array $expandedNodes = [];

    public string $search = '';

    public function mount(): void
    {
        parent::mount();

        // Default expand top-level root nodes
        $this->expandedNodes = ChartOfAccount::whereNull('parent_code')
            ->pluck('kode_akun')
            ->toArray();
    }

    public function toggleNode(string $kodeAkun): void
    {
        if (in_array($kodeAkun, $this->expandedNodes)) {
            $this->expandedNodes = array_values(array_diff($this->expandedNodes, [$kodeAkun]));
        } else {
            $this->expandedNodes[] = $kodeAkun;
        }
    }

    public function expandAll(): void
    {
        $this->expandedNodes = ChartOfAccount::pluck('kode_akun')->toArray();
    }

    public function collapseAll(): void
    {
        $this->expandedNodes = [];
    }

    public function getTreeNodesProperty(): Collection
    {
        $query = ChartOfAccount::query()->with(['children' => fn($q) => $q->orderBy('kode_akun'), 'parent']);

        if (!empty($this->search)) {
            $term = strtolower($this->search);
            $matchingCodes = ChartOfAccount::query()
                ->where('kode_akun', 'like', "%{$term}%")
                ->orWhere('nama_akun', 'like', "%{$term}%")
                ->pluck('kode_akun')
                ->toArray();

            $allLineage = [];
            foreach ($matchingCodes as $code) {
                $curr = $code;
                while ($curr) {
                    $allLineage[$curr] = true;
                    $curr = ChartOfAccount::where('kode_akun', $curr)->value('parent_code');
                }
            }

            return $query->whereIn('kode_akun', array_keys($allLineage))
                ->whereNull('parent_code')
                ->orderBy('kode_akun')
                ->get();
        }

        return $query->whereNull('parent_code')
            ->orderBy('kode_akun')
            ->get();
    }

    protected function getActions(): array
    {
        return [
            $this->createAccountAction(),
            $this->addChildAccountAction(),
            $this->editAccountAction(),
            $this->deleteAccountAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->createAccountAction(),
        ];
    }

    public function createAccountAction(): Action
    {
        return Action::make('createAccount')
            ->label('Akun Baru')
            ->icon('heroicon-m-plus')
            ->modalHeading('Tambah Akun Baru')
            ->modalWidth('lg')
            ->form([
                TextInput::make('kode_akun')
                    ->label('Kode Akun')
                    ->required()
                    ->maxLength(255)
                    ->unique('chart_of_accounts', 'kode_akun')
                    ->rule('regex:/^[^,]+$/')
                    ->validationMessages([
                        'regex' => 'Kode Akun tidak boleh mengandung tanda koma.',
                        'unique' => 'Kode Akun sudah digunakan.',
                    ]),

                TextInput::make('nama_akun')
                    ->label('Nama Akun')
                    ->required()
                    ->maxLength(255),

                TextInput::make('budget')
                    ->label('Budget (Anggaran PKA)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Patokan anggaran statis tahunan untuk akun ini.'),

                Select::make('kategori')
                    ->label('Kategori')
                    ->required()
                    ->options([
                        'Penerimaan' => 'Penerimaan',
                        'Pengeluaran' => 'Pengeluaran',
                        'Kas & Bank' => 'Kas & Bank',
                        'Hutang / Piutang' => 'Hutang / Piutang',
                    ]),

                Select::make('parent_code')
                    ->label('Akun Induk (Parent)')
                    ->nullable()
                    ->searchable()
                    ->placeholder('Tidak memiliki induk (Level Atas)')
                    ->options(fn(): array => ChartOfAccountResource::getParentAccountOptions()),

                Toggle::make('is_postable')
                    ->label('Postable (Bisa dipilih untuk transaksi)')
                    ->default(true),
            ])
            ->action(function (array $data): void {
                ChartOfAccount::create($data);

                if (!empty($data['parent_code']) && !in_array($data['parent_code'], $this->expandedNodes)) {
                    $this->expandedNodes[] = $data['parent_code'];
                }

                Notification::make()
                    ->success()
                    ->title('Akun Berhasil Ditambahkan')
                    ->body('Akun ' . $data['nama_akun'] . ' (' . $data['kode_akun'] . ') berhasil dibuat.')
                    ->send();
            });
    }

    public function addChildAccountAction(): Action
    {
        return Action::make('addChildAccount')
            ->label('Add Child')
            ->icon('heroicon-m-plus-circle')
            ->modalHeading('Tambah Akun Anak')
            ->modalWidth('lg')
            ->fillForm(function (array $arguments): array {
                $parentCode = $arguments['parent_code'] ?? null;
                $parent = $parentCode ? ChartOfAccount::find($parentCode) : null;

                return [
                    'parent_code' => $parentCode,
                    'kategori' => $parent?->kategori ?? 'Penerimaan',
                    'budget' => 0,
                    'is_postable' => true,
                ];
            })
            ->form([
                TextInput::make('kode_akun')
                    ->label('Kode Akun')
                    ->required()
                    ->maxLength(255)
                    ->unique('chart_of_accounts', 'kode_akun')
                    ->rule('regex:/^[^,]+$/')
                    ->validationMessages([
                        'regex' => 'Kode Akun tidak boleh mengandung tanda koma.',
                        'unique' => 'Kode Akun sudah digunakan.',
                    ]),

                TextInput::make('nama_akun')
                    ->label('Nama Akun')
                    ->required()
                    ->maxLength(255),

                TextInput::make('budget')
                    ->label('Budget (Anggaran PKA)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Patokan anggaran statis tahunan untuk akun ini.'),

                Select::make('kategori')
                    ->label('Kategori')
                    ->required()
                    ->options([
                        'Penerimaan' => 'Penerimaan',
                        'Pengeluaran' => 'Pengeluaran',
                        'Kas & Bank' => 'Kas & Bank',
                        'Hutang / Piutang' => 'Hutang / Piutang',
                    ]),

                Select::make('parent_code')
                    ->label('Akun Induk (Parent)')
                    ->required()
                    ->searchable()
                    ->options(fn(): array => ChartOfAccountResource::getParentAccountOptions()),

                Toggle::make('is_postable')
                    ->label('Postable (Bisa dipilih untuk transaksi)')
                    ->default(true),
            ])
            ->action(function (array $data): void {
                ChartOfAccount::create($data);

                if (!empty($data['parent_code']) && !in_array($data['parent_code'], $this->expandedNodes)) {
                    $this->expandedNodes[] = $data['parent_code'];
                }

                Notification::make()
                    ->success()
                    ->title('Akun Anak Berhasil Ditambahkan')
                    ->body('Akun ' . $data['nama_akun'] . ' (' . $data['kode_akun'] . ') berhasil dibuat.')
                    ->send();
            });
    }

    public function editAccountAction(): Action
    {
        return Action::make('editAccount')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Edit Akun')
            ->modalWidth('lg')
            ->fillForm(function (array $arguments): array {
                $account = ChartOfAccount::findOrFail($arguments['kode_akun']);
                return [
                    'kode_akun' => $account->kode_akun,
                    'nama_akun' => $account->nama_akun,
                    'budget' => (float) ($account->budget ?? 0),
                    'kategori' => $account->kategori,
                    'parent_code' => $account->parent_code,
                    'is_postable' => (bool) $account->is_postable,
                ];
            })
            ->form([
                TextInput::make('kode_akun')
                    ->label('Kode Akun')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('nama_akun')
                    ->label('Nama Akun')
                    ->required()
                    ->maxLength(255),

                TextInput::make('budget')
                    ->label('Budget (Anggaran PKA)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Patokan anggaran statis tahunan untuk akun ini.'),

                Select::make('kategori')
                    ->label('Kategori')
                    ->required()
                    ->options([
                        'Penerimaan' => 'Penerimaan',
                        'Pengeluaran' => 'Pengeluaran',
                        'Kas & Bank' => 'Kas & Bank',
                        'Hutang / Piutang' => 'Hutang / Piutang',
                    ]),

                Select::make('parent_code')
                    ->label('Akun Induk (Parent)')
                    ->nullable()
                    ->searchable()
                    ->placeholder('Tidak memiliki induk (Level Atas)')
                    ->options(fn($get): array => ChartOfAccountResource::getParentAccountOptions($get('kode_akun'))),

                Toggle::make('is_postable')
                    ->label('Postable (Bisa dipilih untuk transaksi)'),
            ])
            ->action(function (array $data, array $arguments): void {
                $account = ChartOfAccount::findOrFail($arguments['kode_akun']);
                $account->update([
                    'nama_akun' => $data['nama_akun'],
                    'budget' => $data['budget'] ?? 0,
                    'kategori' => $data['kategori'],
                    'parent_code' => $data['parent_code'] ?? null,
                    'is_postable' => $data['is_postable'],
                ]);

                Notification::make()
                    ->success()
                    ->title('Akun Berhasil Diperbarui')
                    ->body('Akun ' . $account->nama_akun . ' telah diperbarui.')
                    ->send();
            });
    }

    public function deleteAccountAction(): Action
    {
        return Action::make('deleteAccount')
            ->label('Delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Hapus Akun')
            ->modalDescription('Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.')
            ->action(function (array $arguments): void {
                $kodeAkun = $arguments['kode_akun'] ?? null;
                $account = ChartOfAccount::find($kodeAkun);

                if (!$account) return;

                if (ChartOfAccount::where('parent_code', $account->kode_akun)->exists()) {
                    Notification::make()
                        ->danger()
                        ->title('Akun Tidak Dapat Dihapus')
                        ->body('Akun "' . $account->nama_akun . '" masih memiliki akun anak. Hapus atau pindahkan akun anak terlebih dahulu.')
                        ->send();
                    return;
                }

                if ($account->transactions()->exists()) {
                    Notification::make()
                        ->danger()
                        ->title('Akun Tidak Dapat Dihapus')
                        ->body('Akun "' . $account->nama_akun . '" sudah memiliki riwayat transaksi dan tidak dapat dihapus.')
                        ->send();
                    return;
                }

                $name = $account->nama_akun;
                $account->delete();

                Notification::make()
                    ->success()
                    ->title('Akun Berhasil Dihapus')
                    ->body('Akun ' . $name . ' telah dihapus.')
                    ->send();
            });
    }
}
