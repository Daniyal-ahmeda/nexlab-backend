<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Resources\FamilyMemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Patient Family Members
 */
class FamilyMemberController extends Controller
{
    /**
     * Display listing of authenticated patient's family members.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return FamilyMemberResource::collection($request->user()->familyMembers);
    }

    /**
     * Store a new family member for authenticated patient.
     */
    public function store(StoreFamilyMemberRequest $request): JsonResponse
    {
        $member = $request->user()->familyMembers()->create($request->validated());

        return (new FamilyMemberResource($member))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Remove specified family member.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $member = $request->user()->familyMembers()->findOrFail($id);
        $member->delete();

        return response()->json([
            'message' => 'Family member deleted successfully',
        ]);
    }
}
