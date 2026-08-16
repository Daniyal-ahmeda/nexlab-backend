<?php

use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed');
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

    $submitResponse->assertStatus(201)
        ->assertJson([
            'data' => [
                'id' => $newResultId,
                'lab_name' => 'Al-Afia Medical Center',
                'pdf_url' => 'http://nexlab-backend.test/storage/test_results/lipid_panel_777.pdf',
            ],
        ]);

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
