<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerLabResource;
use App\Models\PartnerLab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLabController extends Controller
{
    /**
     * Create a new partner lab.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'unique:partner_labs,id'],
            'name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'reviews_count' => ['required', 'integer', 'min:0'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'hours' => ['required', 'string', 'max:255'],
            'has_home_collection' => ['boolean'],
        ]);

        $lab = PartnerLab::create($validated);

        return (new PartnerLabResource($lab))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update an existing partner lab.
     */
    public function update(Request $request, string $id): PartnerLabResource
    {
        $lab = PartnerLab::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'rating' => ['sometimes', 'required', 'numeric', 'min:0', 'max:5'],
            'reviews_count' => ['sometimes', 'required', 'integer', 'min:0'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:255'],
            'hours' => ['sometimes', 'required', 'string', 'max:255'],
            'has_home_collection' => ['boolean'],
        ]);

        $lab->update($validated);

        return new PartnerLabResource($lab->fresh());
    }

    /**
     * Delete a partner lab.
     */
    public function destroy(string $id): JsonResponse
    {
        $lab = PartnerLab::findOrFail($id);
        $lab->delete();

        return response()->json([
            'message' => 'Partner lab deleted successfully',
        ]);
    }
}
