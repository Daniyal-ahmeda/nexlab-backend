<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('authenticated user can register an fcm token', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/device-token', [
        'fcm_token' => 'test-fcm-token-abc123',
    ]);

    $response->assertOk()
        ->assertJson(['message' => 'Device token registered successfully']);

    expect($user->fresh()->fcm_token)->toBe('test-fcm-token-abc123');
});

test('device token registration requires a token', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/device-token', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['fcm_token']);
});

test('guest cannot register a device token', function () {
    $this->postJson('/api/device-token', ['fcm_token' => 'abc'])
        ->assertUnauthorized();
});

test('registering a new token overwrites the old one', function () {
    $user = User::factory()->create(['fcm_token' => 'old-token']);
    Sanctum::actingAs($user);

    $this->postJson('/api/device-token', ['fcm_token' => 'new-token'])
        ->assertOk();

    expect($user->fresh()->fcm_token)->toBe('new-token');
});
