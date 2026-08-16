<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestResultResource;
use App\Models\TestResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Admin Portal
 */
class AdminResultController extends Controller
{
    /**
     * Store a test result and biomarkers for a patient (with optional PDF file upload).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'unique:test_results,id'],
            'user_id' => ['required', 'exists:users,id'],
            'diagnostic_test_id' => ['required', 'exists:diagnostic_tests,id'],
            'lab_name' => ['required', 'string', 'max:255'],
            'test_date' => ['required', 'date'],
            'report_date' => ['required', 'date'],
            'pdf_url' => ['nullable', 'string'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'biomarkers' => ['required', 'array', 'min:1'],
            'biomarkers.*.name' => ['required', 'string', 'max:255'],
            'biomarkers.*.value' => ['required', 'string', 'max:255'],
            'biomarkers.*.unit' => ['required', 'string', 'max:255'],
            'biomarkers.*.reference_range' => ['required', 'string', 'max:255'],
            'biomarkers.*.status' => ['required', 'in:Normal,Low,High,Borderline'],
        ]);

        $pdfUrl = $validated['pdf_url'] ?? null;

        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('test_results', 'public');
            $pdfUrl = asset('storage/'.$path);
        }

        $result = TestResult::create([
            'id' => $validated['id'],
            'user_id' => $validated['user_id'],
            'diagnostic_test_id' => $validated['diagnostic_test_id'],
            'lab_name' => $validated['lab_name'],
            'test_date' => $validated['test_date'],
            'report_date' => $validated['report_date'],
            'pdf_url' => $pdfUrl,
        ]);

        foreach ($validated['biomarkers'] as $bm) {
            $result->biomarkers()->create($bm);
        }

        return (new TestResultResource($result->load(['biomarkers', 'diagnosticTest'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Upload a lab report PDF independently.
     */
    public function uploadPdf(Request $request): JsonResponse
    {
        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $request->file('pdf_file')->store('test_results', 'public');

        return response()->json([
            'message' => 'Lab report uploaded successfully',
            'pdf_url' => asset('storage/'.$path),
        ]);
    }
}
