<?php

use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('patient can create a family member', function () {
    $user = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/family-members', [
            'name' => 'Ali',
            'relationship' => 'Child',
            'age' => 7,
            'gender' => 'Male',
            'blood_group' => 'O+',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'name',
                'relationship',
                'age',
                'gender',
                'blood_group',
            ],
        ])
        ->assertJson([
            'data' => [
                'name' => 'Ali',
                'relationship' => 'Child',
                'age' => 7,
                'gender' => 'Male',
                'blood_group' => 'O+',
            ],
        ]);

    $this->assertDatabaseHas('family_members', [
        'user_id' => $user->id,
        'name' => 'Ali',
        'relationship' => 'Child',
    ]);
});

test('patient can list their family members only', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $otherUser = User::create([
        'name' => 'Other User',
        'email' => 'other_user@example.com',
        'password' => bcrypt('password'),
    ]);

    $monder->familyMembers()->create([
        'name' => 'Nour',
        'relationship' => 'Spouse',
        'age' => 32,
        'gender' => 'Female',
        'blood_group' => 'A+',
    ]);

    $otherUser->familyMembers()->create([
        'name' => 'Other Spouse',
        'relationship' => 'Spouse',
        'age' => 29,
        'gender' => 'Female',
        'blood_group' => 'B+',
    ]);

    $response = $this->actingAs($monder, 'sanctum')
        ->getJson('/api/family-members');

    $response->assertStatus(200);
    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['name'])->toBe('Nour');
});

test('patient can delete their own family member', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $member = $monder->familyMembers()->create([
        'name' => 'Tariq',
        'relationship' => 'Sibling',
        'age' => 25,
        'gender' => 'Male',
        'blood_group' => 'AB+',
    ]);

    $response = $this->actingAs($monder, 'sanctum')
        ->deleteJson("/api/family-members/{$member->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Family member deleted successfully']);

    $this->assertDatabaseMissing('family_members', ['id' => $member->id]);
});

test('patient cannot delete another patient family member', function () {
    $monder = User::where('email', 'monder@example.com')->first();
    $otherUser = User::create([
        'name' => 'Other User 2',
        'email' => 'other2@example.com',
        'password' => bcrypt('password'),
    ]);

    $otherMember = $otherUser->familyMembers()->create([
        'name' => 'Secret Child',
        'relationship' => 'Child',
        'age' => 4,
        'gender' => 'Female',
        'blood_group' => 'O-',
    ]);

    $response = $this->actingAs($monder, 'sanctum')
        ->deleteJson("/api/family-members/{$otherMember->id}");

    $response->assertStatus(404);
    $this->assertDatabaseHas('family_members', ['id' => $otherMember->id]);
});

test('family member validation fails with invalid attributes', function () {
    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/family-members', [
            'name' => '',
            'relationship' => 'Friend',
            'age' => 200,
            'gender' => 'Unknown',
            'blood_group' => 'XYZ',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'relationship', 'age', 'gender', 'blood_group']);
});

test('unauthenticated access to family members returns 401', function () {
    $this->getJson('/api/family-members')->assertStatus(401);
    $this->postJson('/api/family-members', [])->assertStatus(401);
    $this->deleteJson('/api/family-members/1')->assertStatus(401);
});
