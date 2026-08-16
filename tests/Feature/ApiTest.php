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

test('can register a new user', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Ahmed Tripoli',
        'email' => 'ahmed@example.com',
        'password' => 'password123',
        'age' => 29,
        'gender' => 'Male',
        'blood_group' => 'A+',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'age', 'gender', 'blood_group']]);
});

test('can login seeded user and fetch profile', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'monder@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token', 'user']);

    $token = $response->json('token');

    $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/user');

    $meResponse->assertStatus(200)
        ->assertJson(['data' => ['email' => 'monder@example.com', 'name' => 'Monder']]);
});

test('can list tests with category and search filter', function () {
    $response = $this->getJson('/api/tests?category=Heart');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');

    $searchResponse = $this->getJson('/api/tests?search=Blood');

    $searchResponse->assertStatus(200);
    expect(count($searchResponse->json('data')))->toBeGreaterThanOrEqual(1);
});

test('can list partner labs', function () {
    $response = $this->getJson('/api/labs');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('authenticated user can create and cancel booking', function () {
    $user = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    $bookingResponse = $this->actingAs($user, 'sanctum')
        ->postJson('/api/bookings', [
            'diagnostic_test_id' => $test->id,
            'partner_lab_id' => $lab->id,
            'is_home_collection' => true,
            'date' => '2026-08-10',
            'time_slot' => '10:00 AM',
            'patient_name' => 'Monder',
        ]);

    $bookingResponse->assertStatus(201)
        ->assertJson(['data' => ['status' => 'pending', 'total_amount' => (float) $test->price]]);

    $bookingId = $bookingResponse->json('data.id');

    $cancelResponse = $this->actingAs($user, 'sanctum')
        ->postJson("/api/bookings/{$bookingId}/cancel");

    $cancelResponse->assertStatus(200)
        ->assertJson(['data' => ['status' => 'cancelled']]);
});

test('authenticated user can manage family members', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $createResponse = $this->actingAs($user, 'sanctum')
        ->postJson('/api/family-members', [
            'name' => 'Sara',
            'relationship' => 'Spouse',
            'age' => 30,
            'gender' => 'Female',
            'blood_group' => 'B+',
        ]);

    $createResponse->assertStatus(201)
        ->assertJson(['data' => ['name' => 'Sara', 'relationship' => 'Spouse']]);

    $memberId = $createResponse->json('data.id');

    $listResponse = $this->actingAs($user, 'sanctum')
        ->getJson('/api/family-members');

    $listResponse->assertStatus(200)
        ->assertJsonCount(1, 'data');

    $deleteResponse = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/family-members/{$memberId}");

    $deleteResponse->assertStatus(200);
});

test('authenticated user can manage payment methods', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $listResponse = $this->actingAs($user, 'sanctum')
        ->getJson('/api/payment-methods');

    $listResponse->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $firstMethodId = $listResponse->json('data.1.id');

    $defaultResponse = $this->actingAs($user, 'sanctum')
        ->postJson("/api/payment-methods/{$firstMethodId}/default");

    $defaultResponse->assertStatus(200)
        ->assertJson(['data' => ['is_default' => true]]);
});

test('can upload prescription', function () {
    Storage::fake('public');

    $user = User::where('email', 'monder@example.com')->first();
    $file = UploadedFile::fake()->create('prescription.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions/upload', [
            'prescription' => $file,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'path']);
});

test('authenticated user can view their bookings', function () {
    $user = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    Booking::create([
        'id' => 'NX-55443',
        'user_id' => $user->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => true,
        'date' => '2026-08-20',
        'time_slot' => '10:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => 150.00,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/bookings');

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => [['id', 'status', 'patient_name', 'diagnostic_test', 'partner_lab']]]);
});

test('authenticated user can add and delete payment method', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $createResponse = $this->actingAs($user, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Sadad',
            'number' => '091-9999999',
            'is_default' => false,
        ]);

    $createResponse->assertStatus(201)
        ->assertJson(['data' => ['type' => 'Sadad', 'number' => '091-9999999']]);

    $pmId = $createResponse->json('data.id');

    $deleteResponse = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/payment-methods/{$pmId}");

    $deleteResponse->assertStatus(200);
});

test('authenticated user can log out', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Successfully logged out']);
});
