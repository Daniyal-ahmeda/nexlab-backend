<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiagnosticTestResource;
use App\Models\DiagnosticTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Admin Portal
 */
class AdminTestController extends Controller
{
    /**
     * Create a new diagnostic test or package.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'unique:diagnostic_tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'in:Heart,Blood,Thyroid,Energy,General'],
            'price' => ['required', 'numeric', 'min:0'],
            'reports_in_hours' => ['required', 'integer', 'min:1'],
            'sample_type' => ['required', 'string', 'max:255'],
            'fasting_required' => ['boolean'],
            'is_package' => ['boolean'],
        ]);

        $test = DiagnosticTest::create($validated);

        return (new DiagnosticTestResource($test))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update an existing diagnostic test.
     */
    public function update(Request $request, string $id): DiagnosticTestResource
    {
        $test = DiagnosticTest::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'subtitle' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'category' => ['sometimes', 'required', 'in:Heart,Blood,Thyroid,Energy,General'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'reports_in_hours' => ['sometimes', 'required', 'integer', 'min:1'],
            'sample_type' => ['sometimes', 'required', 'string', 'max:255'],
            'fasting_required' => ['boolean'],
            'is_package' => ['boolean'],
        ]);

        $test->update($validated);

        return new DiagnosticTestResource($test->fresh());
    }

    /**
     * Delete a diagnostic test.
     */
    public function destroy(string $id): JsonResponse
    {
        $test = DiagnosticTest::findOrFail($id);
        $test->delete();

        return response()->json([
            'message' => 'Diagnostic test deleted successfully',
        ]);
    }
}
