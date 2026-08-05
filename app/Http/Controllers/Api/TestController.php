<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiagnosticTestResource;
use App\Models\DiagnosticTest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestController extends Controller
{
    /**
     * Display a listing of diagnostic tests.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DiagnosticTest::query();

        if ($request->has('category') && $request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->has('is_package') && $request->filled('is_package')) {
            $isPackage = filter_var($request->query('is_package'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_package', $isPackage);
        }

        if ($request->has('search') && $request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return DiagnosticTestResource::collection($query->get());
    }
}
