<?php

use App\Models\Booking;
use App\Models\DiagnosticTest;
use App\Models\PartnerLab;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('patient can create a booking with home collection', function () {
    $user = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::where('id', 't9')->first();
    $lab = PartnerLab::where('id', 'l1')->first();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/bookings', [
            'diagnostic_test_id' => $test->id,
            'partner_lab_id' => $lab->id,
            'is_home_collection' => true,
            'date' => '2026-08-25',
            'time_slot' => '09:00 AM',
            'patient_name' => 'Monder Patient',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'diagnostic_test_id',
                'partner_lab_id',
                'is_home_collection',
                'date',
                'time_slot',
                'patient_name',
                'total_amount',
                'status',
                'diagnostic_test',
                'partner_lab',
            ],
        ])
        ->assertJson([
            'data' => [
                'user_id' => $user->id,
                'diagnostic_test_id' => 't9',
                'partner_lab_id' => 'l1',
                'is_home_collection' => true,
                'patient_name' => 'Monder Patient',
                'total_amount' => 299.00,
                'status' => 'pending',
            ],
        ]);

    $bookingId = $response->json('data.id');
    expect($bookingId)->toStartWith('NX-');

    $this->assertDatabaseHas('bookings', [
        'id' => $bookingId,
        'user_id' => $user->id,
        'status' => 'pending',
    ]);
});

test('patient can list their own bookings ordered latest first', function () {
    $user = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    $b1 = Booking::create([
        'id' => 'NX-11111',
        'user_id' => $user->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-10',
        'time_slot' => '08:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => $test->price,
        'status' => 'completed',
    ]);
    $b1->forceFill(['created_at' => now()->subDay()])->save();

    $b2 = Booking::create([
        'id' => 'NX-22222',
        'user_id' => $user->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => true,
        'date' => '2026-08-20',
        'time_slot' => '10:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => $test->price,
        'status' => 'pending',
    ]);
    $b2->forceFill(['created_at' => now()])->save();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/bookings');

    $response->assertStatus(200);
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(2);
    expect($data[0]['id'])->toBe('NX-22222');
});

test('patient can cancel their own booking', function () {
    $user = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    $booking = Booking::create([
        'id' => 'NX-33333',
        'user_id' => $user->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-22',
        'time_slot' => '11:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => $test->price,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/bookings/{$booking->id}/cancel");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => 'NX-33333',
                'status' => 'cancelled',
            ],
        ]);

    $this->assertDatabaseHas('bookings', [
        'id' => 'NX-33333',
        'status' => 'cancelled',
    ]);
});

test('patient cannot cancel another patient booking', function () {
    $otherUser = User::create([
        'name' => 'Other Patient',
        'email' => 'other@example.com',
        'password' => bcrypt('password'),
    ]);

    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    $otherBooking = Booking::create([
        'id' => 'NX-44444',
        'user_id' => $otherUser->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-22',
        'time_slot' => '11:00 AM',
        'patient_name' => 'Other Patient',
        'total_amount' => $test->price,
        'status' => 'pending',
    ]);

    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson("/api/bookings/{$otherBooking->id}/cancel");

    $response->assertStatus(404);
});

test('booking store fails with validation errors when parameters are invalid', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/bookings', [
            'diagnostic_test_id' => 'invalid_test_id',
            'partner_lab_id' => 'invalid_lab_id',
            'date' => 'not-a-date',
            'patient_name' => '',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['diagnostic_test_id', 'partner_lab_id', 'date', 'time_slot', 'patient_name']);
});

test('unauthenticated user cannot access bookings', function () {
    $this->getJson('/api/bookings')->assertStatus(401);
    $this->postJson('/api/bookings', [])->assertStatus(401);
    $this->postJson('/api/bookings/NX-12345/cancel')->assertStatus(401);
});
