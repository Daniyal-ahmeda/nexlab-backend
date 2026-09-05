<?php

use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('patient can retrieve their test results with nested biomarkers and test details', function () {
    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->getJson('/api/results');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'diagnostic_test_id',
                    'lab_name',
                    'test_date',
                    'report_date',
                    'pdf_url',
                    'diagnostic_test',
                    'biomarkers' => [
                        '*' => [
                            'id',
                            'test_result_id',
                            'name',
                            'value',
                            'unit',
                            'reference_range',
                            'status',
                        ],
                    ],
                ],
            ],
        ]);

    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect($data[0]['id'])->toBe('res_1');
    expect(count($data[0]['biomarkers']))->toBe(4);
});

test('patient only sees their own test results', function () {
    $otherUser = User::create([
        'name' => 'Other Patient',
        'email' => 'other_results@example.com',
        'password' => bcrypt('password'),
    ]);

    TestResult::create([
        'id' => 'res_other_99',
        'user_id' => $otherUser->id,
        'diagnostic_test_id' => 't1',
        'lab_name' => 'Tripoli Lab',
        'test_date' => '2026-08-01',
        'report_date' => '2026-08-02',
    ]);

    $monder = User::where('email', 'monder@example.com')->first();

    $response = $this->actingAs($monder, 'sanctum')
        ->getJson('/api/results');

    $response->assertStatus(200);
    $resultIds = collect($response->json('data'))->pluck('id')->all();
    expect($resultIds)->not->toContain('res_other_99');
});

test('patient can upload prescription file in PDF, JPG, or PNG format', function () {
    Storage::fake('public');
    $monder = User::where('email', 'monder@example.com')->first();

    // PDF upload
    $pdf = UploadedFile::fake()->create('doctor_prescription.pdf', 500, 'application/pdf');
    $responsePdf = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/prescriptions/upload', [
            'prescription' => $pdf,
        ]);

    $responsePdf->assertStatus(200)
        ->assertJsonStructure(['message', 'path']);

    Storage::disk('public')->assertExists('prescriptions/'.$pdf->hashName());

    // Image upload
    $image = UploadedFile::fake()->image('prescription_photo.jpg');
    $responseImg = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/prescriptions/upload', [
            'prescription' => $image,
        ]);

    $responseImg->assertStatus(200)
        ->assertJsonStructure(['message', 'path']);

    Storage::disk('public')->assertExists('prescriptions/'.$image->hashName());
});

test('prescription upload fails when file is missing or invalid type or too large', function () {
    Storage::fake('public');
    $monder = User::where('email', 'monder@example.com')->first();

    // Missing file
    $missingResponse = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/prescriptions/upload', []);
    $missingResponse->assertStatus(422)
        ->assertJsonValidationErrors(['prescription']);

    // Invalid file type (.txt)
    $txtFile = UploadedFile::fake()->create('note.txt', 10, 'text/plain');
    $invalidTypeResponse = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/prescriptions/upload', [
            'prescription' => $txtFile,
        ]);
    $invalidTypeResponse->assertStatus(422)
        ->assertJsonValidationErrors(['prescription']);

    // Exceeds max 10MB
    $hugeFile = UploadedFile::fake()->create('huge.pdf', 11000, 'application/pdf');
    $tooLargeResponse = $this->actingAs($monder, 'sanctum')
        ->postJson('/api/prescriptions/upload', [
            'prescription' => $hugeFile,
        ]);
    $tooLargeResponse->assertStatus(422)
        ->assertJsonValidationErrors(['prescription']);
});

test('unauthenticated request to results or prescription upload returns 401', function () {
    $this->getJson('/api/results')->assertStatus(401);
    $this->postJson('/api/prescriptions/upload', [])->assertStatus(401);
});
