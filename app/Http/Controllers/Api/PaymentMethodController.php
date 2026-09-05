<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Services\FirebaseOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Patient Payment Gateways
 */
class PaymentMethodController extends Controller
{
    public function __construct(private readonly FirebaseOtpService $firebaseOtp) {}

    /**
     * Display listing of patient's payment methods.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return PaymentMethodResource::collection($request->user()->paymentMethods);
    }

    /**
     * Store a new payment method for patient.
     *
     * Requires a valid Firebase Phone Auth ID token (`firebase_token`) to confirm
     * the patient's phone identity before registering a new payment method.
     */
    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Verify the Firebase token belongs to this authenticated user.
        $this->firebaseOtp->verifyTokenForUser($validated['firebase_token'], $user);

        $isFirst = $user->paymentMethods()->count() === 0;
        $shouldBeDefault = $isFirst || ($validated['is_default'] ?? false);

        if ($shouldBeDefault) {
            $user->paymentMethods()->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        // Do not persist the token itself.
        unset($validated['firebase_token']);

        $paymentMethod = $user->paymentMethods()->create($validated);

        return (new PaymentMethodResource($paymentMethod))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Set specified payment method as default.
     */
    public function setDefault(Request $request, string $id): PaymentMethodResource
    {
        $user = $request->user();
        $targetMethod = $user->paymentMethods()->findOrFail($id);

        $user->paymentMethods()->where('id', '!=', $targetMethod->id)->update(['is_default' => false]);
        $targetMethod->update(['is_default' => true]);

        return new PaymentMethodResource($targetMethod->fresh());
    }

    /**
     * Remove specified payment method.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $paymentMethod = $request->user()->paymentMethods()->findOrFail($id);
        $wasDefault = $paymentMethod->is_default;
        $paymentMethod->delete();

        if ($wasDefault) {
            $nextMethod = $request->user()->paymentMethods()->first();
            if ($nextMethod) {
                $nextMethod->update(['is_default' => true]);
            }
        }

        return response()->json([
            'message' => 'Payment method deleted successfully',
        ]);
    }
}
