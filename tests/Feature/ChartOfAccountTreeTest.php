<?php

use App\Filament\Resources\ChartOfAccountResource\Pages\ListChartOfAccounts;
use App\Models\ChartOfAccount;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('chart of accounts tree page can be rendered', function () {
    $this->actingAs($this->user)
        ->get('/admin/chart-of-accounts')
        ->assertStatus(200);
});

test('chart of accounts livewire component renders tree nodes and state', function () {
    $parent = ChartOfAccount::create([
        'kode_akun' => '1000',
        'nama_akun' => 'Aktiva',
        'kategori' => 'Kas & Bank',
        'parent_code' => null,
        'is_postable' => false,
    ]);

    $child = ChartOfAccount::create([
        'kode_akun' => '1001',
        'nama_akun' => 'Kas Operasional',
        'kategori' => 'Kas & Bank',
        'parent_code' => '1000',
        'is_postable' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(ListChartOfAccounts::class)
        ->assertSee('Aktiva')
        ->assertSee('Kas Operasional');
});

test('can create child account via addChildAccount action', function () {
    $parent = ChartOfAccount::create([
        'kode_akun' => '2000',
        'nama_akun' => 'Kewajiban',
        'kategori' => 'Hutang / Piutang',
        'parent_code' => null,
        'is_postable' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(ListChartOfAccounts::class)
        ->callAction('addChildAccount', [
            'kode_akun' => '2001',
            'nama_akun' => 'Hutang Pembangunan',
            'kategori' => 'Hutang / Piutang',
            'parent_code' => '2000',
            'is_postable' => true,
        ]);

    $this->assertDatabaseHas('chart_of_accounts', [
        'kode_akun' => '2001',
        'nama_akun' => 'Hutang Pembangunan',
        'parent_code' => '2000',
    ]);
});

test('cannot delete account with existing child accounts', function () {
    $parent = ChartOfAccount::create([
        'kode_akun' => '3000',
        'nama_akun' => 'Penerimaan Gereja',
        'kategori' => 'Penerimaan',
        'parent_code' => null,
        'is_postable' => false,
    ]);

    $child = ChartOfAccount::create([
        'kode_akun' => '3001',
        'nama_akun' => 'Persembahan Mingguan',
        'kategori' => 'Penerimaan',
        'parent_code' => '3000',
        'is_postable' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(ListChartOfAccounts::class)
        ->callAction('deleteAccount', [], ['kode_akun' => '3000']);

    $this->assertDatabaseHas('chart_of_accounts', [
        'kode_akun' => '3000',
    ]);
});
