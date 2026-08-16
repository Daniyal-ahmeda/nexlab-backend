<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\DiagnosticTest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Patient Bookings
 */
class BookingController extends Controller
{
    /**
     * Display authenticated patient's bookings.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['diagnosticTest', 'partnerLab'])
            ->latest()
            ->get();

        return BookingResource::collection($bookings);
    }

    /**
     * Create a new lab appointment booking.
     */
    public function store(StoreBookingRequest $request): BookingResource
    {
        $validated = $request->validated();
        $test = DiagnosticTest::findOrFail($validated['diagnostic_test_id']);

        do {
            $bookingId = 'NX-'.rand(10000, 99999);
        } while (Booking::where('id', $bookingId)->exists());

        $booking = $request->user()->bookings()->create([
            'id' => $bookingId,
            'diagnostic_test_id' => $test->id,
            'partner_lab_id' => $validated['partner_lab_id'],
            'is_home_collection' => $validated['is_home_collection'] ?? false,
            'date' => $validated['date'],
            'time_slot' => $validated['time_slot'],
            'patient_name' => $validated['patient_name'],
            'total_amount' => $test->price,
            'status' => 'pending',
        ]);

        return new BookingResource($booking->load(['diagnosticTest', 'partnerLab']));
    }

    /**
     * Cancel an existing booking.
     */
    public function cancel(Request $request, string $id): BookingResource
    {
        $booking = $request->user()->bookings()->findOrFail($id);

        $booking->update([
            'status' => 'cancelled',
        ]);

        return new BookingResource($booking->load(['diagnosticTest', 'partnerLab']));
    }
}
