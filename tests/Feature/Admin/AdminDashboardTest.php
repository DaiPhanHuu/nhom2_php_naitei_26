<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('admin can access dedicated admin dashboard page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertStatus(200)
        ->assertSee('Dashboard')
        ->assertSee('Tổng doanh thu')
        ->assertSee('Đơn đặt chỗ mới nhất');
});

test('regular users cannot access admin dashboard page', function () {
    $user = User::factory()->create(['role' => 'user']);
    $user->assignRole('user');

    $response = $this->actingAs($user)->get('/admin');

    $response->assertStatus(403);
});
