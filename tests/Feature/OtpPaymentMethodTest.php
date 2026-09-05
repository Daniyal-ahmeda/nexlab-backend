<?php

use App\Models\User;
use App\Services\FirebaseOtpService;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->artisan('db:seed');
});

/**
 * Helper: mock FirebaseOtpService::verifyTokenForUser() for payment method tests.
 *
 * @param  bool  $shouldThrow  When true, simulates an invalid/expired token.
 */
function mockFirebaseOtpForPayment(bool $shouldThrow = false): void
{
    $mock = Mockery::mock(FirebaseOtpService::class);

    if ($shouldThrow) {
        $mock->shouldReceive('verifyTokenForUser')
            ->once()
            ->andThrow(ValidationException::withMessages([
                'firebase_token' => ['The phone verification token is invalid or has expired.'],
            ]));
    } else {
        $mock->shouldReceive('verifyTokenForUser')
            ->once()
            ->andReturn('+218912345678');
    }

    app()->instance(FirebaseOtpService::class, $mock);
}

test('patient cannot add a payment method without a firebase_token field', function () {
    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Moamalat',
            'number' => '092-9998877',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['firebase_token']);
});

test('patient cannot add a payment method with an invalid firebase token', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    mockFirebaseOtpForPayment(shouldThrow: true);

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Moamalat',
            'number' => '092-9998877',
            'firebase_token' => 'expired-or-invalid-token',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['firebase_token']);
});

test('patient can add a payment method with a valid firebase token', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    mockFirebaseOtpForPayment();

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Tadawul',
            'number' => '091-7776655',
            'firebase_token' => 'valid-firebase-id-token',
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'type' => 'Tadawul',
                'number' => '091-7776655',
            ],
        ]);

    $this->assertDatabaseHas('payment_methods', [
        'user_id' => $monder->id,
        'type' => 'Tadawul',
        'number' => '091-7776655',
    ]);
});

test('firebase_token is not stored in the payment_methods table', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    mockFirebaseOtpForPayment();

    $this->actingAs($monder, 'sanctum')
        ->postJson('/api/payment-methods', [
            'type' => 'Sahel',
            'number' => '092-1112222',
            'firebase_token' => 'valid-firebase-id-token',
        ])->assertStatus(201);

    $this->assertDatabaseMissing('payment_methods', [
        'firebase_token' => 'valid-firebase-id-token',
    ]);
});

test('unauthenticated patient cannot add a payment method', function () {
    $response = $this->postJson('/api/payment-methods', [
        'type' => 'Sadad',
        'number' => '091-0001111',
        'firebase_token' => 'some-token',
    ]);

    $response->assertStatus(401);
});
