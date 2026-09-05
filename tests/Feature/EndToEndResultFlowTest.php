<?php

use App\Models\User;
use App\Services\FcmService;

beforeEach(function () {
    $this->artisan('db:seed');
    // Silence real FCM HTTP calls globally; individual tests override with mock() for strict assertions
    $this->spy(FcmService::class);
});

test('end-to-end: admin submits a test result for patient, then patient retrieves it', function () {
    // 1. Admin login & token generation
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();

    $newResultId = 'res_777_'.rand(100, 999);

    // 2. Admin submits a test result with PDF & Biomarkers for patient (user_id: 1)
    $submitResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/results', [
            'id' => $newResultId,
            'user_id' => $patient->id,
            'diagnostic_test_id' => 't2',
            'lab_name' => 'Al-Afia Medical Center',
            'test_date' => '2026-08-06',
            'report_date' => '2026-08-06',
            'pdf_url' => 'http://nexlab-backend.test/storage/test_results/lipid_panel_777.pdf',
            'biomarkers' => [
                [
                    'name' => 'Total Cholesterol',
                    'value' => '210',
                    'unit' => 'mg/dL',
                    'reference_range' => '< 200',
                    'status' => 'High',
                ],
                [
                    'name' => 'HDL Cholesterol',
                    'value' => '55',
                    'unit' => 'mg/dL',
                    'reference_range' => '> 40',
                    'status' => 'Normal',
                ],
                [
                    'name' => 'Triglycerides',
                    'value' => '145',
                    'unit' => 'mg/dL',
                    'reference_range' => '< 150',
                    'status' => 'Normal',
                ],
            ],
        ]);

    $submitResponse->assertStatus(201);

    // 3. Patient logs in and fetches their medical results
    $patientResultsResponse = $this->actingAs($patient, 'sanctum')
        ->getJson('/api/results');

    $patientResultsResponse->assertStatus(200);

    // 4. Verify that the newly submitted result exists in patient's results list
    $resultsList = $patientResultsResponse->json('data');
    $matchingResult = collect($resultsList)->firstWhere('id', $newResultId);

    expect($matchingResult)->not->toBeNull();
    expect($matchingResult['lab_name'])->toBe('Al-Afia Medical Center');
    expect($matchingResult['pdf_url'])->toBe('http://nexlab-backend.test/storage/test_results/lipid_panel_777.pdf');
    expect(count($matchingResult['biomarkers']))->toBe(3);
});

test('publishing a result triggers an FCM push notification to the patient', function () {
    $fcm = $this->mock(FcmService::class);
    $fcm->shouldReceive('sendToUser')
        ->once()
        ->withArgs(function ($user, $title, $body, $data) {
            return str_contains($title, 'Results Are Ready')
                && isset($data['result_id'])
                && $data['type'] === 'new_result';
        });

    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/results', [
            'id' => 'res_fcm_test_'.rand(100, 999),
            'user_id' => $patient->id,
            'diagnostic_test_id' => 't1',
            'lab_name' => 'Test Lab',
            'test_date' => '2026-08-23',
            'report_date' => '2026-08-23',
            'biomarkers' => [
                [
                    'name' => 'Hemoglobin',
                    'value' => '14.5',
                    'unit' => 'g/dL',
                    'reference_range' => '13.5-17.5',
                    'status' => 'Normal',
                ],
            ],
        ])
        ->assertStatus(201);
});
