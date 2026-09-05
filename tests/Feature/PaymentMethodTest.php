<?php

use App\Models\User;
use App\Services\FirebaseOtpService;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('patient can list their seeded Libyan payment methods', function () {
    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->getJson('/api/payment-methods');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'type',
                    'number',
                    'expiry',
                    'is_default',
                ],
            ],
        ]);

    expect(count($response->json('data')))->toBe(3);
});

test('first payment method created for a new user becomes default automatically', function () {
    $newUser = User::create([
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => bcrypt('password'),
    ]);

    // Mock Firebase OTP verification.
    $mock = Mockery::mock(FirebaseOtpService::class);
    $mock->shouldReceive('verifyTokenForUser')->once()->andReturn('+218900000099');
    app()->instance(FirebaseOtpService::class, $mock);

    $response = $this->actingAs($newUser, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Moamalat',
            'number' => '092-1112233',
            'is_default' => false, // even if passed false, first should be true
            'firebase_token' => 'mock-firebase-token',
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'type' => 'Moamalat',
                'number' => '092-1112233',
                'is_default' => true,
            ],
        ]);
});

test('adding a new default payment method demotes the previous default', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $oldDefault = $monder->paymentMethods()->where('is_default', true)->first();
    expect($oldDefault)->not->toBeNull();

    // Mock Firebase OTP verification.
    $mock = Mockery::mock(FirebaseOtpService::class);
    $mock->shouldReceive('verifyTokenForUser')->once()->andReturn('+218900000099');
    app()->instance(FirebaseOtpService::class, $mock);

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Tadawul',
            'number' => '091-7776655',
            'is_default' => true,
            'firebase_token' => 'mock-firebase-token',
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'type' => 'Tadawul',
                'is_default' => true,
            ],
        ]);

    expect($oldDefault->fresh()->is_default)->toBeFalse();
});

test('patient can set an existing payment method as default', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $nonDefault = $monder->paymentMethods()->where('is_default', false)->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson("/api/payment-methods/{$nonDefault->id}/default");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $nonDefault->id,
                'is_default' => true,
            ],
        ]);

    $defaultsCount = $monder->paymentMethods()->where('is_default', true)->count();
    expect($defaultsCount)->toBe(1);
});

test('deleting default payment method promotes the next method to default', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $defaultMethod = $monder->paymentMethods()->where('is_default', true)->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->deleteJson("/api/payment-methods/{$defaultMethod->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Payment method deleted successfully']);

    $this->assertDatabaseMissing('payment_methods', ['id' => $defaultMethod->id]);

    $newDefault = $monder->paymentMethods()->where('is_default', true)->first();
    expect($newDefault)->not->toBeNull();
});

test('patient cannot modify or delete another patient payment method', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $otherUser = User::create([
        'name' => 'Other Patient',
        'email' => 'other_pm@example.com',
        'password' => bcrypt('password'),
    ]);

    $otherPm = $otherUser->paymentMethods()->create([
        'type' => 'Sadad',
        'number' => '091-0000000',
        'is_default' => true,
    ]);

    $setDefaultResponse = $this->actingAs($monder, 'sanctum')
        ->postJson("/api/payment-methods/{$otherPm->id}/default");
    $setDefaultResponse->assertStatus(404);

    $deleteResponse = $this->actingAs($monder, 'sanctum')
        ->deleteJson("/api/payment-methods/{$otherPm->id}");
    $deleteResponse->assertStatus(404);
});

test('payment method creation fails with invalid gateway type', function () {
    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'PayPal', // not in Libyan gateway allowed list
            'number' => '12345',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});
