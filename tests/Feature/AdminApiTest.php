<?php

use App\Models\Booking;
use App\Models\DiagnosticTest;
use App\Models\PartnerLab;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('admin can manage partner labs', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $createResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/labs', [
            'id' => 'lab99',
            'name' => 'Al-Najah Clinical Labs',
            'rating' => 4.9,
            'reviews_count' => 42,
            'address' => 'Gargaresh, Tripoli',
            'phone' => '+218 21 777 8888',
            'hours' => '8:00 AM - 10:00 PM',
            'has_home_collection' => true,
        ]);

    $createResponse->assertStatus(201)
        ->assertJson(['data' => ['id' => 'lab99', 'name' => 'Al-Najah Clinical Labs']]);

    $updateResponse = $this->actingAs($admin, 'sanctum')
        ->putJson('/api/admin/labs/lab99', [
            'name' => 'Al-Najah Advanced Labs',
            'rating' => 5.0,
        ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['data' => ['id' => 'lab99', 'name' => 'Al-Najah Advanced Labs']]);

    $deleteResponse = $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/admin/labs/lab99');

    $deleteResponse->assertStatus(200);
});

test('admin can update booking status', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    $booking = Booking::create([
        'id' => 'NX-11223',
        'user_id' => $patient->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => true,
        'date' => '2026-08-20',
        'time_slot' => '10:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => 150.00,
        'status' => 'pending',
    ]);

    $updateResponse = $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/admin/bookings/{$booking->id}/status", [
            'status' => 'completed',
        ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['data' => ['id' => $booking->id, 'status' => 'completed']]);
});

test('admin can upload lab report pdf', function () {
    Storage::fake('public');
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $file = UploadedFile::fake()->create('report.pdf', 200, 'application/pdf');

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/results/upload-pdf', [
            'pdf_file' => $file,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'pdf_url']);
});

test('admin can log out', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Admin logged out successfully']);
});
