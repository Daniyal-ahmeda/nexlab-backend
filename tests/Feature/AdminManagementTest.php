<?php

use App\Models\Booking;
use App\Models\DiagnosticTest;
use App\Models\PartnerLab;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->artisan('db:seed');
});

test('admin dashboard returns complete statistical overview and recent bookings', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    Booking::create([
        'id' => 'NX-STAT-1',
        'user_id' => $patient->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-20',
        'time_slot' => '10:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => 100.00,
        'status' => 'completed',
    ]);

    Booking::create([
        'id' => 'NX-STAT-2',
        'user_id' => $patient->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-21',
        'time_slot' => '11:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => 50.00,
        'status' => 'pending',
    ]);

    Booking::create([
        'id' => 'NX-STAT-3',
        'user_id' => $patient->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-22',
        'time_slot' => '12:00 PM',
        'patient_name' => 'Monder',
        'total_amount' => 75.00,
        'status' => 'cancelled',
    ]);

    $response = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/admin/dashboard/stats');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'stats' => [
                'total_revenue_lyd',
                'total_bookings',
                'pending_bookings',
                'completed_bookings',
                'cancelled_bookings',
                'total_patients',
                'total_tests',
                'total_labs',
            ],
            'recent_bookings',
        ]);

    $stats = $response->json('stats');
    // Total revenue excludes cancelled (100 + 50 = 150)
    expect($stats['total_revenue_lyd'])->toEqual(150);
    expect($stats['total_bookings'])->toBeGreaterThanOrEqual(3);
    expect($stats['pending_bookings'])->toBeGreaterThanOrEqual(1);
    expect($stats['completed_bookings'])->toBeGreaterThanOrEqual(1);
    expect($stats['cancelled_bookings'])->toBeGreaterThanOrEqual(1);
    expect($stats['total_patients'])->toBeGreaterThanOrEqual(1);
    expect($stats['total_tests'])->toBeGreaterThanOrEqual(5);
    expect($stats['total_labs'])->toBeGreaterThanOrEqual(2);
});

test('admin can list all bookings with status filter', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    Booking::create([
        'id' => 'NX-FILTER-1',
        'user_id' => $patient->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-20',
        'time_slot' => '10:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => 100.00,
        'status' => 'completed',
    ]);

    $allResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/bookings');
    $allResponse->assertStatus(200);

    $completedResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/bookings?status=completed');
    $completedResponse->assertStatus(200);
    foreach ($completedResponse->json('data') as $booking) {
        expect($booking['status'])->toBe('completed');
    }
});

test('admin can update booking status or receive validation error', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();
    $lab = PartnerLab::first();

    $booking = Booking::create([
        'id' => 'NX-UPDATE-1',
        'user_id' => $patient->id,
        'diagnostic_test_id' => $test->id,
        'partner_lab_id' => $lab->id,
        'is_home_collection' => false,
        'date' => '2026-08-20',
        'time_slot' => '10:00 AM',
        'patient_name' => 'Monder',
        'total_amount' => 100.00,
        'status' => 'pending',
    ]);

    $updateResponse = $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/admin/bookings/{$booking->id}/status", [
            'status' => 'completed',
        ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['data' => ['id' => $booking->id, 'status' => 'completed']]);

    // Validation error on invalid status
    $invalidResponse = $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/admin/bookings/{$booking->id}/status", [
            'status' => 'unsupported_status',
        ]);
    $invalidResponse->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

test('admin can perform full CRUD lifecycle on diagnostic tests', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    // 1. Create Test
    $createResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/tests', [
            'id' => 't_test_crud',
            'name' => 'Liver Function Panel',
            'subtitle' => 'ALT, AST, ALP, Bilirubin & Albumin',
            'description' => 'Evaluates hepatic cellular integrity and secretory function.',
            'category' => 'General',
            'price' => 120.00,
            'reports_in_hours' => 12,
            'sample_type' => 'Blood',
            'fasting_required' => true,
            'is_package' => false,
        ]);

    $createResponse->assertStatus(201)
        ->assertJson(['data' => ['id' => 't_test_crud', 'name' => 'Liver Function Panel']]);

    // 2. Validation error on duplicate ID
    $dupResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/tests', [
            'id' => 't_test_crud',
            'name' => 'Duplicate',
            'subtitle' => 'Sub',
            'description' => 'Desc',
            'category' => 'General',
            'price' => 100,
            'reports_in_hours' => 12,
            'sample_type' => 'Blood',
        ]);
    $dupResponse->assertStatus(422)->assertJsonValidationErrors(['id']);

    // 3. Update Test
    $updateResponse = $this->actingAs($admin, 'sanctum')
        ->putJson('/api/admin/tests/t_test_crud', [
            'name' => 'Advanced Liver Function Panel',
            'price' => 135.00,
        ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['data' => ['name' => 'Advanced Liver Function Panel', 'price' => 135.00]]);

    // 4. Delete Test
    $deleteResponse = $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/admin/tests/t_test_crud');

    $deleteResponse->assertStatus(200)
        ->assertJson(['message' => 'Diagnostic test deleted successfully']);

    $this->assertDatabaseMissing('diagnostic_tests', ['id' => 't_test_crud']);
});

test('admin can perform full CRUD lifecycle on partner labs', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    // 1. Create Lab
    $createResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/labs', [
            'id' => 'lab_crud_1',
            'name' => 'Ibn Sina Modern Labs',
            'rating' => 4.8,
            'reviews_count' => 95,
            'address' => 'Zawiyat Al-Dahmani, Tripoli',
            'phone' => '+218 21 444 5566',
            'hours' => '08:00 AM - 10:00 PM',
            'has_home_collection' => true,
        ]);

    $createResponse->assertStatus(201)
        ->assertJson(['data' => ['id' => 'lab_crud_1', 'name' => 'Ibn Sina Modern Labs']]);

    // 2. Validation error on rating > 5
    $invalidResponse = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/labs', [
            'id' => 'lab_invalid',
            'name' => 'Invalid Lab',
            'rating' => 6.5,
            'reviews_count' => 10,
            'address' => 'Tripoli',
            'phone' => '12345',
            'hours' => '24/7',
        ]);
    $invalidResponse->assertStatus(422)->assertJsonValidationErrors(['rating']);

    // 3. Update Lab
    $updateResponse = $this->actingAs($admin, 'sanctum')
        ->putJson('/api/admin/labs/lab_crud_1', [
            'name' => 'Ibn Sina Diagnostic Center',
            'rating' => 4.9,
        ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['data' => ['name' => 'Ibn Sina Diagnostic Center']]);

    // 4. Delete Lab
    $deleteResponse = $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/admin/labs/lab_crud_1');

    $deleteResponse->assertStatus(200)
        ->assertJson(['message' => 'Partner lab deleted successfully']);

    $this->assertDatabaseMissing('partner_labs', ['id' => 'lab_crud_1']);
});

test('admin can store test result with uploaded PDF file and biomarker array', function () {
    Storage::fake('public');
    $admin = User::where('email', 'admin@nexlab.ly')->first();
    $patient = User::where('email', 'monder@example.com')->first();
    $test = DiagnosticTest::first();

    $pdf = UploadedFile::fake()->create('lab_output.pdf', 300, 'application/pdf');

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/results', [
            'id' => 'res_uploaded_99',
            'user_id' => $patient->id,
            'diagnostic_test_id' => $test->id,
            'lab_name' => 'Tripoli Central Lab',
            'test_date' => '2026-08-15',
            'report_date' => '2026-08-16',
            'pdf_file' => $pdf,
            'biomarkers' => [
                [
                    'name' => 'Blood Glucose Fasting',
                    'value' => '95',
                    'unit' => 'mg/dL',
                    'reference_range' => '70 - 99',
                    'status' => 'Normal',
                ],
                [
                    'name' => 'HbA1c',
                    'value' => '5.4',
                    'unit' => '%',
                    'reference_range' => '< 5.7',
                    'status' => 'Normal',
                ],
            ],
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'diagnostic_test_id',
                'lab_name',
                'test_date',
                'report_date',
                'pdf_url',
                'biomarkers' => [
                    '*' => ['id', 'name', 'value', 'unit', 'reference_range', 'status'],
                ],
            ],
        ]);

    Storage::disk('public')->assertExists('test_results/'.$pdf->hashName());
    $this->assertDatabaseHas('test_results', ['id' => 'res_uploaded_99']);
    $this->assertDatabaseHas('biomarkers', ['name' => 'Blood Glucose Fasting']);
});

test('admin can upload lab report PDF independently', function () {
    Storage::fake('public');
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $pdf = UploadedFile::fake()->create('report_standalone.pdf', 250, 'application/pdf');

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/results/upload-pdf', [
            'pdf_file' => $pdf,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'pdf_url']);

    Storage::disk('public')->assertExists('test_results/'.$pdf->hashName());
});

test('admin results store validation fails on invalid biomarkers status or missing user', function () {
    $admin = User::where('email', 'admin@nexlab.ly')->first();

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/admin/results', [
            'id' => 'res_err',
            'user_id' => 999999, // non-existent
            'diagnostic_test_id' => 'invalid_test',
            'lab_name' => 'Test Lab',
            'test_date' => '2026-08-01',
            'report_date' => '2026-08-02',
            'biomarkers' => [
                [
                    'name' => 'Marker 1',
                    'value' => '10',
                    'unit' => 'mg',
                    'reference_range' => '0-10',
                    'status' => 'InvalidStatusValue',
                ],
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user_id', 'diagnostic_test_id', 'biomarkers.0.status']);
});
