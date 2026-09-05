<?php

beforeEach(function () {
    $this->artisan('db:seed');
});

test('public catalog lists all seeded diagnostic tests with full resource fields', function () {
    $response = $this->getJson('/api/tests');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'subtitle',
                    'description',
                    'category',
                    'price',
                    'reports_in_hours',
                    'sample_type',
                    'fasting_required',
                    'is_package',
                ],
            ],
        ]);

    expect(count($response->json('data')))->toBeGreaterThanOrEqual(5);
});

test('can filter diagnostic tests by category', function () {
    $categories = ['Heart', 'Blood', 'Thyroid', 'Energy', 'General'];

    foreach ($categories as $category) {
        $response = $this->getJson("/api/tests?category={$category}");
        $response->assertStatus(200);

        $data = $response->json('data');
        expect(count($data))->toBeGreaterThanOrEqual(1);
        foreach ($data as $item) {
            expect($item['category'])->toBe($category);
        }
    }
});

test('can filter diagnostic tests by package status', function () {
    $packageResponse = $this->getJson('/api/tests?is_package=true');
    $packageResponse->assertStatus(200);
    foreach ($packageResponse->json('data') as $item) {
        expect($item['is_package'])->toBeTrue();
    }

    $singleTestResponse = $this->getJson('/api/tests?is_package=false');
    $singleTestResponse->assertStatus(200);
    foreach ($singleTestResponse->json('data') as $item) {
        expect($item['is_package'])->toBeFalse();
    }
});

test('can search diagnostic tests by name, subtitle, or description keyword', function () {
    $nameSearch = $this->getJson('/api/tests?search=Executive');
    $nameSearch->assertStatus(200);
    expect(count($nameSearch->json('data')))->toBe(1);
    expect($nameSearch->json('data.0.name'))->toContain('Full Body Executive Checkup');

    $subtitleSearch = $this->getJson('/api/tests?search=Hemoglobin');
    $subtitleSearch->assertStatus(200);
    expect(count($subtitleSearch->json('data')))->toBeGreaterThanOrEqual(1);

    $descSearch = $this->getJson('/api/tests?search=cardiovascular');
    $descSearch->assertStatus(200);
    expect(count($descSearch->json('data')))->toBeGreaterThanOrEqual(1);

    $emptySearch = $this->getJson('/api/tests?search=nonexistentterm12345');
    $emptySearch->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

test('public catalog lists all partner labs with comprehensive data', function () {
    $response = $this->getJson('/api/labs');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'rating',
                    'reviews_count',
                    'address',
                    'phone',
                    'hours',
                    'has_home_collection',
                ],
            ],
        ]);

    expect(count($response->json('data')))->toBe(2);
});
