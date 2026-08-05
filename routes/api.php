<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminBookingController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminLabController;
use App\Http\Controllers\Api\Admin\AdminResultController;
use App\Http\Controllers\Api\Admin\AdminTestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FamilyMemberController;
use App\Http\Controllers\Api\LabController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\TestController;
use Illuminate\Support\Facades\Route;

// Public Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Public Catalog
Route::get('/tests', [TestController::class, 'index']);
Route::get('/labs', [LabController::class, 'index']);

// Authenticated Patient Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // Medical Records
    Route::get('/results', [ResultController::class, 'index']);
    Route::post('/prescriptions/upload', [ResultController::class, 'uploadPrescription']);

    // Family Members
    Route::get('/family-members', [FamilyMemberController::class, 'index']);
    Route::post('/family-members', [FamilyMemberController::class, 'store']);
    Route::delete('/family-members/{id}', [FamilyMemberController::class, 'destroy']);

    // Libyan Payment Gateways
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
    Route::post('/payment-methods/{id}/default', [PaymentMethodController::class, 'setDefault']);
    Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'destroy']);
});

// Authenticated Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);

    // Booking Management
    Route::get('/bookings', [AdminBookingController::class, 'index']);
    Route::patch('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus']);

    // Diagnostic Test Management
    Route::post('/tests', [AdminTestController::class, 'store']);
    Route::put('/tests/{id}', [AdminTestController::class, 'update']);
    Route::delete('/tests/{id}', [AdminTestController::class, 'destroy']);

    // Partner Lab Management
    Route::post('/labs', [AdminLabController::class, 'store']);
    Route::put('/labs/{id}', [AdminLabController::class, 'update']);
    Route::delete('/labs/{id}', [AdminLabController::class, 'destroy']);

    // Result Management
    Route::post('/results', [AdminResultController::class, 'store']);
    Route::post('/results/upload-pdf', [AdminResultController::class, 'uploadPdf']);
});
