<?php

namespace App\Filament\Settings\Pages;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class PengaturanGereja extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.pengaturan-gereja';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-library';

    protected static UnitEnum|string|null $navigationGroup = 'Konfigurasi Portal';

    protected static ?string $navigationLabel = 'Profil & Identitas Gereja';

    protected static ?string $title = 'Pengaturan Profil & Identitas Gereja';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('portal.config.manage') ?? false;
    }

    // Livewire public state — bound to form fields via statePath('')
    public ?string $church_name     = '';
    public ?string $church_address1 = '';
    public ?string $church_address2 = '';
    public $church_logo             = null;

    public function mount(): void
    {
        $this->church_name     = AppSetting::get('church_name',     'GPIB Jemaat Hosiana');
        $this->church_address1 = AppSetting::get('church_address1', 'Jl. Rajawali Selatan V No. 7');
        $this->church_address2 = AppSetting::get('church_address2', 'Jakarta Pusat 10772');
        $this->church_logo     = AppSetting::get('church_logo',     '');

        $this->form->fill([
            'church_name'     => $this->church_name,
            'church_address1' => $this->church_address1,
            'church_address2' => $this->church_address2,
            'church_logo'     => $this->church_logo ?: null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identitas & Profil Lembaga')
                    ->description('Data ini digunakan untuk identitas sistem, header cetakan Bukti Voucher (PDF), dan logo pada Navbar.')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        FileUpload::make('church_logo')
                            ->label('Logo Gereja')
                            ->image()
                            ->disk('public')
                            ->directory('church')
                            ->visibility('public')
                            ->imagePreviewHeight('120')
                            ->maxSize(2048)
                            ->helperText('Unggah logo gereja (format PNG, JPG, JPEG, SVG, WebP, maks 2MB). Jika logo diunggah, logo akan muncul pada Navbar menggantikan teks "Keuangan Gereja". Jika belum ada logo, teks default akan digunakan.'),

                        TextInput::make('church_name')
                            ->label('Nama Gereja / Lembaga')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('Contoh: GPIB Jemaat Hosiana'),

                        TextInput::make('church_address1')
                            ->label('Alamat Baris 1')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('Contoh: Jl. Rajawali Selatan V No. 7'),

                        TextInput::make('church_address2')
                            ->label('Alamat Baris 2 (Kota & Kode Pos)')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('Contoh: Jakarta Pusat 10772'),
                    ]),
            ])
            ->statePath('');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $logo = $data['church_logo'] ?? '';
        if (is_array($logo)) {
            $logo = array_values($logo)[0] ?? '';
        }

        $oldSettings = [
            'church_name'     => AppSetting::get('church_name'),
            'church_address1' => AppSetting::get('church_address1'),
            'church_address2' => AppSetting::get('church_address2'),
            'church_logo'     => AppSetting::get('church_logo'),
        ];

        $newSettings = [
            'church_name'     => $data['church_name'] ?? '',
            'church_address1' => $data['church_address1'] ?? '',
            'church_address2' => $data['church_address2'] ?? '',
            'church_logo'     => (string) $logo,
        ];

        AppSetting::set('church_name',     $newSettings['church_name']);
        AppSetting::set('church_address1', $newSettings['church_address1']);
        AppSetting::set('church_address2', $newSettings['church_address2']);
        AppSetting::set('church_logo',     $newSettings['church_logo']);

        ActivityLog::log(
            description: 'Memperbarui Konfigurasi Profil & Identitas Gereja',
            logName: 'portal_settings',
            properties: [
                'old' => $oldSettings,
                'attributes' => $newSettings,
            ]
        );

        Notification::make()
            ->title('Pengaturan profil gereja berhasil disimpan.')
            ->success()
            ->send();
    }
}
