<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentMethodController extends Controller
{
    /**
     * Display listing of user's payment methods.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return PaymentMethodResource::collection($request->user()->paymentMethods);
    }

    /**
     * Store a new payment method for user.
     */
    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $isFirst = $user->paymentMethods()->count() === 0;
        $shouldBeDefault = $isFirst || ($validated['is_default'] ?? false);

        if ($shouldBeDefault) {
            $user->paymentMethods()->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

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
