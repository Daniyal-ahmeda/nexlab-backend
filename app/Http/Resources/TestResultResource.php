<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $diagnosticTest = $this->whenLoaded('diagnosticTest');
        $biomarkers = $this->whenLoaded('biomarkers');

        $pdfUrl = $this->pdf_url;
        if ($pdfUrl && str_contains($pdfUrl, '/storage/')) {
            $path = substr($pdfUrl, strpos($pdfUrl, '/storage/'));
            $pdfUrl = $request->schemeAndHttpHost().$path;
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'diagnostic_test_id' => $this->diagnostic_test_id,
            'lab_name' => $this->lab_name,
            'test_date' => $this->test_date?->format('Y-m-d'),
            'report_date' => $this->report_date?->format('Y-m-d'),
            'pdf_url' => $pdfUrl,
            'status' => 'Results Available',
            'test' => new DiagnosticTestResource($diagnosticTest),
            'diagnostic_test' => new DiagnosticTestResource($diagnosticTest),
            'parameters' => BiomarkerResource::collection($biomarkers),
            'biomarkers' => BiomarkerResource::collection($biomarkers),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
