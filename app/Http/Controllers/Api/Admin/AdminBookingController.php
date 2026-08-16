<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Admin Portal
 */
class AdminBookingController extends Controller
{
    /**
     * Display a listing of all user bookings.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Booking::with(['user', 'diagnosticTest', 'partnerLab']);

        if ($request->has('status') && $request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return BookingResource::collection($query->latest()->get());
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, string $id): BookingResource
    {
        $request->validate([
            'status' => ['required', 'in:pending,completed,cancelled'],
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => $request->input('status'),
        ]);

        return new BookingResource($booking->load(['user', 'diagnosticTest', 'partnerLab']));
    }
}
