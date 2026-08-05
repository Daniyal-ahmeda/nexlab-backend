<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'partner_lab_id' => $this->partner_lab_id,
            'is_home_collection' => (bool) $this->is_home_collection,
            'date' => $this->date?->format('Y-m-d'),
            'time_slot' => $this->time_slot,
            'patient_name' => $this->patient_name,
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'diagnostic_test' => new DiagnosticTestResource($this->whenLoaded('diagnosticTest')),
            'partner_lab' => new PartnerLabResource($this->whenLoaded('partnerLab')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
