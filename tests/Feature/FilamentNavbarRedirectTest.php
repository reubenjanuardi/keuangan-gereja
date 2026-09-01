<?php

use App\Models\User;
use Database\Seeders\ChartOfAccountSeeder;
use Database\Seeders\RbacSeeder;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);
    $this->seed(RbacSeeder::class);
});

test('keuangan panel has home url configured to /dashboard', function () {
    $panel = Filament::getPanel('keuangan');
    expect($panel->getHomeUrl())->toBe('/dashboard');
});

test('keuangan panel page renders logo linking to /dashboard', function () {
    $admin = User::where('email', 'admin@gpibhosiana.org')->first();

    $response = $this->actingAs($admin)->get('/keuangan');

    $response->assertStatus(200);
    $response->assertSee('/dashboard');
});

