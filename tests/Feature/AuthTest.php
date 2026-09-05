<?php

use App\Models\User;
use App\Services\FirebaseOtpService;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('user can register with valid required and optional fields', function () {
    // Mock Firebase OTP so no real HTTP call is made.
    $mock = Mockery::mock(FirebaseOtpService::class);
    $mock->shouldReceive('verifyToken')->once()->andReturn('+218911112222');
    app()->instance(FirebaseOtpService::class, $mock);

    $response = $this->postJson('/api/register', [
        'name' => 'Fatima Tripoli',
        'email' => 'fatima@example.com',
        'password' => 'securepass123',
        'age' => 28,
        'gender' => 'Female',
        'blood_group' => 'B+',
        'firebase_token' => 'mock-firebase-token',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'age',
                'gender',
                'blood_group',
                'is_admin',
            ],
        ])
        ->assertJson([
            'user' => [
                'name' => 'Fatima Tripoli',
                'email' => 'fatima@example.com',
                'age' => 28,
                'gender' => 'Female',
                'blood_group' => 'B+',
                'is_admin' => false,
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'fatima@example.com',
        'name' => 'Fatima Tripoli',
    ]);
});

test('registration validation fails with duplicate email', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Duplicate Monder',
        'email' => 'monder@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration validation fails when required fields are missing or invalid', function () {
    $response = $this->postJson('/api/register', [
        'email' => 'not-an-email',
        'password' => '123',
        'age' => 200,
        'gender' => 'InvalidGender',
        'blood_group' => 'XYZ',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'age', 'gender', 'blood_group']);
});

test('patient can login with correct credentials and receive token', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'monder@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token', 'user'])
        ->assertJson([
            'user' => [
                'email' => 'monder@example.com',
                'name' => 'Monder',
            ],
        ]);
});

test('login fails with incorrect password', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'monder@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('login fails with non-existent email', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('authenticated user can fetch their profile via /api/user and /api/me', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $userResponse = $this->actingAs($user, 'sanctum')->getJson('/api/user');
    $userResponse->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'email' => 'monder@example.com',
                'name' => 'Monder',
                'age' => 34,
                'gender' => 'Male',
                'blood_group' => 'O+',
                'is_admin' => false,
            ],
        ]);

    $meResponse = $this->actingAs($user, 'sanctum')->getJson('/api/me');
    $meResponse->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'email' => 'monder@example.com',
                'name' => 'Monder',
            ],
        ]);
});

test('unauthenticated request to /api/user returns 401', function () {
    $response = $this->getJson('/api/user');
    $response->assertStatus(401);
});

test('authenticated patient can logout and invalidate token', function () {
    $user = User::where('email', 'monder@example.com')->first();
    $token = $user->createToken('test_token')->plainTextToken;

    $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/logout');

    $logoutResponse->assertStatus(200)
        ->assertJson(['message' => 'Successfully logged out']);

    $this->assertDatabaseCount('personal_access_tokens', 0);

    $this->app['auth']->forgetGuards();

    $profileResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/user');

    $profileResponse->assertStatus(401);
});
