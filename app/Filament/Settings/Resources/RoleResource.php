<?php

namespace App\Filament\Settings\Resources;

use App\Filament\Settings\Resources\RoleResource\Pages;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Akses';

    protected static ?string $navigationLabel = 'Peran & Hak Akses';

    protected static ?string $modelLabel = 'Peran';

    protected static ?string $pluralModelLabel = 'Daftar Peran & Hak Akses';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Peran (Role)')
                ->description('Definisikan identitas peran dalam sistem.')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Kode Peran')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->disabled(fn (?Role $record) => $record?->name === 'Super Admin'),

                    TextInput::make('display_name')
                        ->label('Nama Tampilan Peran')
                        ->placeholder('Contoh: Bendahara Jemaat, Operator Kasir')
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Deskripsi Tugas & Kewenangan')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Matriks Distribusi Hak Akses (Permissions)')
                ->description('Pilih hak akses yang diberikan untuk peran ini. Tersedia fitur pencarian dan tombol pilih semua.')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Daftar Hak Akses')
                        ->relationship('permissions', 'id')
                        ->options(function () {
                            return Permission::orderBy('group_name')->orderBy('name')->get()->mapWithKeys(function (Permission $p) {
                                $group = $p->group_name ? "【{$p->group_name}】 " : '';
                                return [$p->id => $group . ($p->display_name ?? $p->name) . ($p->description ? ' — ' . $p->description : '')];
                            })->toArray();
                        })
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable()
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kode Peran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('display_name')
                    ->label('Nama Tampilan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),

                TextColumn::make('permissions_count')
                    ->label('Jumlah Izin')
                    ->counts('permissions')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('users_count')
                    ->label('Jumlah Pengguna')
                    ->counts('users')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Role $record) => $record->name === 'Super Admin'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
