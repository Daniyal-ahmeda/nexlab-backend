<?php

use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('admin can log in successfully', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@nexlab.ly',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'is_admin']]);

    expect($response->json('user.is_admin'))->toBeTrue();
});

test('non-admin user is forbidden from admin login', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'monder@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(403)
        ->assertJson(['message' => 'Access denied. You do not have administrator privileges.']);
});

test('admin can view dashboard stats and all bookings', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $statsResponse = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/admin/dashboard/stats');

    $statsResponse->assertStatus(200)
        ->assertJsonStructure(['stats' => ['total_revenue_lyd', 'total_bookings', 'total_patients', 'total_labs', 'total_tests']]);

    $bookingsResponse = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/admin/bookings');

    $bookingsResponse->assertStatus(200);
});

test('admin can manage diagnostic tests', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $createResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/tests', [
            'id' => 't99',
            'name' => 'Cardiac Enzyme Panel',
            'subtitle' => 'Troponin & CK-MB Screen',
            'description' => 'Evaluates heart tissue damage.',
            'category' => 'Heart',
            'price' => 175.00,
            'reports_in_hours' => 6,
            'sample_type' => 'Blood',
            'fasting_required' => false,
            'is_package' => false,
        ]);

    $createResponse->assertStatus(201)
        ->assertJson(['data' => ['id' => 't99', 'name' => 'Cardiac Enzyme Panel']]);

    $deleteResponse = $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/admin/tests/t99');

    $deleteResponse->assertStatus(200);
});
