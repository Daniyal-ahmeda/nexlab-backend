<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\DiagnosticTest;
use App\Models\PartnerLab;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * @tags Admin Portal
 */
class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard summary statistics.
     */
    public function stats(): JsonResponse
    {
        $totalRevenue = Booking::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $totalPatients = User::where('is_admin', false)->count();
        $totalTests = DiagnosticTest::count();
        $totalLabs = PartnerLab::count();

        $recentBookings = Booking::with(['user', 'diagnosticTest', 'partnerLab'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_revenue_lyd' => (float) $totalRevenue,
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings,
                'total_patients' => $totalPatients,
                'total_tests' => $totalTests,
                'total_labs' => $totalLabs,
            ],
            'recent_bookings' => BookingResource::collection($recentBookings),
        ]);
    }
}
