<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerLabResource;
use App\Models\PartnerLab;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Public Catalog
 */
class LabController extends Controller
{
    /**
     * Display a listing of partner labs.
     */
    public function index(): AnonymousResourceCollection
    {
        return PartnerLabResource::collection(PartnerLab::all());
    }
}
