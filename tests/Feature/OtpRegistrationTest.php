<?php

use App\Models\User;
use App\Services\FirebaseOtpService;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->artisan('db:seed');
});

/**
 * Helper: mock FirebaseOtpService so tests never hit the real Firebase API.
 *
 * @param  string  $phone  Phone number the mock should return as confirmed.
 */
function mockFirebaseOtp(string $phone = '+218912345678'): void
{
    $mock = Mockery::mock(FirebaseOtpService::class);
    $mock->shouldReceive('verifyToken')
        ->once()
        ->andReturn($phone);

    app()->instance(FirebaseOtpService::class, $mock);
}

test('patient cannot register without a firebase_token field', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Amira Test',
        'email' => 'amira@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['firebase_token']);
});

test('patient cannot register with an invalid firebase token', function () {
    // The real service would throw, so we test without mocking here
    // by binding a service that simulates a Firebase rejection.
    $mock = Mockery::mock(FirebaseOtpService::class);
    $mock->shouldReceive('verifyToken')
        ->once()
        ->andThrow(ValidationException::withMessages([
            'firebase_token' => ['The phone verification token is invalid or has expired.'],
        ]));

    app()->instance(FirebaseOtpService::class, $mock);

    $response = $this->postJson('/api/register', [
        'name' => 'Amira Test',
        'email' => 'amira@example.com',
        'password' => 'password123',
        'firebase_token' => 'invalid-token-abc',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['firebase_token']);
});

test('patient can register successfully with a valid firebase token', function () {
    mockFirebaseOtp('+218912345678');

    $response = $this->postJson('/api/register', [
        'name' => 'Amira Tripoli',
        'email' => 'amira@example.com',
        'password' => 'securepass123',
        'age' => 25,
        'gender' => 'Female',
        'blood_group' => 'A+',
        'firebase_token' => 'valid-firebase-id-token',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'age', 'gender', 'blood_group', 'is_admin'],
        ])
        ->assertJson([
            'user' => [
                'name' => 'Amira Tripoli',
                'email' => 'amira@example.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'amira@example.com',
        'phone' => '+218912345678',
    ]);
});

test('patient cannot register with duplicate email even with valid firebase token', function () {
    // Laravel validates unique:users,email BEFORE the controller runs, so verifyToken()
    // is never called. We still bind the mock to keep the container satisfied.
    $mock = Mockery::mock(FirebaseOtpService::class);
    $mock->shouldReceive('verifyToken')->zeroOrMoreTimes();
    app()->instance(FirebaseOtpService::class, $mock);

    // monder@example.com already exists in the seeder
    $response = $this->postJson('/api/register', [
        'name' => 'Duplicate',
        'email' => 'monder@example.com',
        'password' => 'password123',
        'firebase_token' => 'valid-token',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('phone number from firebase token is stored on the user after registration', function () {
    mockFirebaseOtp('+218945000111');

    $this->postJson('/api/register', [
        'name' => 'Phone User',
        'email' => 'phoneuser@example.com',
        'password' => 'password123',
        'firebase_token' => 'valid-firebase-id-token',
    ])->assertStatus(201);

    $user = User::where('email', 'phoneuser@example.com')->first();
    expect($user->phone)->toBe('+218945000111');
});
