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
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'diagnostic_test_id' => $this->diagnostic_test_id,
            'lab_name' => $this->lab_name,
            'test_date' => $this->test_date?->format('Y-m-d'),
            'report_date' => $this->report_date?->format('Y-m-d'),
            'pdf_url' => $this->pdf_url,
            'diagnostic_test' => new DiagnosticTestResource($this->whenLoaded('diagnosticTest')),
            'biomarkers' => BiomarkerResource::collection($this->whenLoaded('biomarkers')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
