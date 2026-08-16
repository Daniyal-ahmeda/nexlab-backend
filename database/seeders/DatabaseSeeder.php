<?php

namespace Database\Seeders;

use App\Models\DiagnosticTest;
use App\Models\PartnerLab;
use App\Models\PaymentMethod;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@nexlab.ly'],
            [
                'name' => 'NexLab System Administrator',
                'password' => Hash::make('password'),
                'age' => 35,
                'gender' => 'Male',
                'blood_group' => 'O+',
                'is_admin' => true,
            ]
        );

        // 2. Seed Standard Patient User
        $user = User::updateOrCreate(
            ['email' => 'monder@example.com'],
            [
                'name' => 'Monder',
                'password' => Hash::make('password'),
                'age' => 34,
                'gender' => 'Male',
                'blood_group' => 'O+',
                'is_admin' => false,
            ]
        );

        // 3. Seed Diagnostic Tests
        $tests = [
            [
                'id' => 't9',
                'name' => 'Full Body Executive Checkup',
                'subtitle' => '85+ Biomarkers · Complete Blood & Urine Analysis',
                'description' => 'Comprehensive health checkup covering full blood counts, liver, kidney, lipid profiles, and urinary screening.',
                'category' => 'General',
                'price' => 299.00,
                'reports_in_hours' => 24,
                'sample_type' => 'Blood & Urine',
                'fasting_required' => true,
                'is_package' => true,
            ],
            [
                'id' => 't1',
                'name' => 'CBC Complete Blood Count',
                'subtitle' => 'Hemoglobin, RBC, WBC, Platelet Indices',
                'description' => 'Evaluates overall health and detects a wide range of disorders including anemia and infection.',
                'category' => 'Blood',
                'price' => 45.00,
                'reports_in_hours' => 12,
                'sample_type' => 'Blood',
                'fasting_required' => false,
                'is_package' => false,
            ],
            [
                'id' => 't2',
                'name' => 'Lipid Profile Panel',
                'subtitle' => 'Cholesterol, Triglycerides, HDL, LDL',
                'description' => 'Measures circulating blood lipids to assess cardiovascular risk.',
                'category' => 'Heart',
                'price' => 85.00,
                'reports_in_hours' => 12,
                'sample_type' => 'Blood',
                'fasting_required' => true,
                'is_package' => false,
            ],
            [
                'id' => 't3',
                'name' => 'Thyroid Panel T3/T4/TSH',
                'subtitle' => 'Complete Thyroid Function Screen',
                'description' => 'Evaluates how well your thyroid gland is functioning.',
                'category' => 'Thyroid',
                'price' => 110.00,
                'reports_in_hours' => 24,
                'sample_type' => 'Blood',
                'fasting_required' => false,
                'is_package' => false,
            ],
            [
                'id' => 't4',
                'name' => 'Vitamin D & B12 Panel',
                'subtitle' => 'Essential Vitamin & Energy Assessment',
                'description' => 'Screens for key micronutrient deficiencies affecting energy levels and bone density.',
                'category' => 'Energy',
                'price' => 140.00,
                'reports_in_hours' => 24,
                'sample_type' => 'Blood',
                'fasting_required' => false,
                'is_package' => true,
            ],
        ];

        foreach ($tests as $testData) {
            DiagnosticTest::updateOrCreate(['id' => $testData['id']], $testData);
        }

        // 4. Seed Partner Labs
        $labs = [
            [
                'id' => 'l1',
                'name' => 'Tripoli Central Diagnostic Lab',
                'rating' => 4.9,
                'reviews_count' => 312,
                'address' => 'Omar Al-Mukhtar Street, Tripoli',
                'phone' => '+218 21 123 4567',
                'hours' => '07:00 AM - 09:00 PM',
                'has_home_collection' => true,
            ],
            [
                'id' => 'l2',
                'name' => 'Al-Afia Medical Center',
                'rating' => 4.8,
                'reviews_count' => 184,
                'address' => 'Gurji Road, Tripoli',
                'phone' => '+218 21 987 6543',
                'hours' => '08:00 AM - 08:00 PM',
                'has_home_collection' => true,
            ],
        ];

        foreach ($labs as $labData) {
            PartnerLab::updateOrCreate(['id' => $labData['id']], $labData);
        }

        // 5. Seed Libyan Payment Methods
        $paymentMethods = [
            [
                'type' => 'Edfaaly',
                'number' => '091-2345678',
                'expiry' => null,
                'is_default' => true,
            ],
            [
                'type' => 'Mobi Cash',
                'number' => '092-8765432',
                'expiry' => null,
                'is_default' => false,
            ],
            [
                'type' => 'Sadad',
                'number' => '091-5551234',
                'expiry' => null,
                'is_default' => false,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['user_id' => $user->id, 'type' => $method['type']],
                $method
            );
        }

        // 6. Seed Sample Medical Test Result & Biomarkers for Patient Monder
        $testResult = TestResult::updateOrCreate(
            ['id' => 'res_1'],
            [
                'user_id' => $user->id,
                'diagnostic_test_id' => 't1',
                'lab_name' => 'Tripoli Central Diagnostic Lab',
                'test_date' => '2026-08-01',
                'report_date' => '2026-08-02',
                'pdf_url' => asset('storage/test_results/sample_report.pdf'),
            ]
        );

        $biomarkers = [
            [
                'name' => 'Hemoglobin',
                'value' => '14.2',
                'unit' => 'g/dL',
                'reference_range' => '13.5 - 17.5',
                'status' => 'Normal',
            ],
            [
                'name' => 'Red Blood Cells (RBC)',
                'value' => '4.8',
                'unit' => 'm/uL',
                'reference_range' => '4.3 - 5.9',
                'status' => 'Normal',
            ],
            [
                'name' => 'White Blood Cells (WBC)',
                'value' => '11.5',
                'unit' => 'x10^3/uL',
                'reference_range' => '4.5 - 11.0',
                'status' => 'High',
            ],
            [
                'name' => 'Platelet Count',
                'value' => '250',
                'unit' => 'x10^3/uL',
                'reference_range' => '150 - 450',
                'status' => 'Normal',
            ],
        ];

        foreach ($biomarkers as $bm) {
            $testResult->biomarkers()->updateOrCreate(['name' => $bm['name']], $bm);
        }
    }
}
