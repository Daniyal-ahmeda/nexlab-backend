<?php

use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('admin can log in successfully and receive admin token', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@nexlab.ly',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'is_admin'],
        ]);

    expect($response->json('user.is_admin'))->toBeTrue();
    expect($response->json('user.email'))->toBe('admin@nexlab.ly');
});

test('regular patient cannot log in through admin portal', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'monder@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Access denied. You do not have administrator privileges.',
        ]);
});

test('admin login fails with wrong password', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@nexlab.ly',
        'password' => 'wrongpass',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('admin login fails with non-existent admin email', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'ghostadmin@nexlab.ly',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('admin can log out and revoke admin token', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $token = $admin->createToken('admin_test_token')->plainTextToken;

    $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/admin/logout');

    $logoutResponse->assertStatus(200)
        ->assertJson(['message' => 'Admin logged out successfully']);

    $this->assertDatabaseCount('personal_access_tokens', 0);

    $this->app['auth']->forgetGuards();

    $statsResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/admin/dashboard/stats');

    $statsResponse->assertStatus(401);
});

test('regular patient is forbidden by middleware from accessing admin endpoints', function () {
    $patient = User::where('email', 'monder@example.com')->first();

    $this->actingAs($patient, 'sanctum')
        ->getJson('/api/admin/dashboard/stats')
        ->assertStatus(403)
        ->assertJson(['message' => 'Unauthorized. Admin access required.']);

    $this->actingAs($patient, 'sanctum')
        ->getJson('/api/admin/bookings')
        ->assertStatus(403);

    $this->actingAs($patient, 'sanctum')
        ->postJson('/api/admin/tests', [])
        ->assertStatus(403);

    $this->actingAs($patient, 'sanctum')
        ->postJson('/api/admin/labs', [])
        ->assertStatus(403);

    $this->actingAs($patient, 'sanctum')
        ->postJson('/api/admin/results', [])
        ->assertStatus(403);
});

test('guest is rejected with 401 when accessing admin endpoints', function () {
    $this->getJson('/api/admin/dashboard/stats')
        ->assertStatus(401);

    $this->getJson('/api/admin/bookings')
        ->assertStatus(401);
});
