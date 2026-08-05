<?php

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

// Public Catalog
Route::get('/tests', [TestController::class, 'index']);
Route::get('/labs', [LabController::class, 'index']);

// Authenticated Routes
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
