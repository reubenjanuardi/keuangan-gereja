<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
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

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil Gereja';

    protected static ?string $title = 'Pengaturan Profil Gereja';

    protected static ?int $navigationSort = 99;

    // Livewire public state — bound to form fields via statePath('')
    public string $church_name     = '';
    public string $church_address1 = '';
    public string $church_address2 = '';

    public function mount(): void
    {
        $this->church_name     = AppSetting::get('church_name',     'GPIB Jemaat Hosiana');
        $this->church_address1 = AppSetting::get('church_address1', 'Jl. Rajawali Selatan V No. 7');
        $this->church_address2 = AppSetting::get('church_address2', 'Jakarta Pusat 10772');

        $this->form->fill([
            'church_name'     => $this->church_name,
            'church_address1' => $this->church_address1,
            'church_address2' => $this->church_address2,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identitas Gereja')
                    ->description('Data ini akan ditampilkan pada header cetakan Bukti Voucher (PDF).')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        TextInput::make('church_name')
                            ->label('Nama Gereja')
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
                ->label('Simpan Pengaturan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AppSetting::set('church_name',     $data['church_name']);
        AppSetting::set('church_address1', $data['church_address1']);
        AppSetting::set('church_address2', $data['church_address2']);

        Notification::make()
            ->title('Pengaturan berhasil disimpan.')
            ->success()
            ->send();
    }
}
