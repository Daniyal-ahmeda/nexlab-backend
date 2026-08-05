<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestResultResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResultController extends Controller
{
    /**
     * Display authenticated user's test results with biomarkers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $results = $request->user()
            ->testResults()
            ->with(['biomarkers', 'diagnosticTest'])
            ->latest()
            ->get();

        return TestResultResource::collection($results);
    }

    /**
     * Handle prescription file upload.
     */
    public function uploadPrescription(Request $request): JsonResponse
    {
        $request->validate([
            'prescription' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $request->file('prescription')->store('prescriptions', 'public');

        return response()->json([
            'message' => 'Prescription uploaded successfully',
            'path' => asset('storage/'.$path),
        ]);
    }
}
