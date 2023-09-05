<?php

namespace App\Http\Controllers\api;

use App\Models\{Subscription, User};
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResources;
use App\Traits\ResponseTrait;
use App\Http\Requests\SubscriptionRequest;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    //
    use ResponseTrait;

    public function store(SubscriptionRequest $request): JsonResponse
    {
        $subscription = Subscription::create([
            'plan' => $request->plan,
            'price' => $request->price,
            'upload_limit' => $request->upload_limit,
        ]);

        return $this->successResponse('Subscription created successfully', new SubscriptionResources($subscription), 201);
    }

    public function index(): JsonResponse
    {
        $subscriptions = Subscription::latest()->paginate(10)->through(fn ($subscription) => new SubscriptionResources($subscription));

        return $this->successResponse('Subscriptions retrieved successfully', $subscriptions);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $subscription = Subscription::findOrFail($id);

            return $this->successResponse('Subscription retrieved successfully', new SubscriptionResources($subscription));
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 404);
        }
    }

    public function update(SubscriptionRequest $request,string $id): JsonResponse
    {

        try {
            $subscription = Subscription::findOrFail($id);

            $subscription->update([
                'plan' => $request->plan,
                'price' => $request->price,
                'upload_limit' => $request->upload_limit,
            ]);

            return $this->successResponse('Subscription updated successfully', new SubscriptionResources($subscription));
        } catch (\Throwable $th) {
            return $this->errorResponse('Subscription not found', 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->delete();

        return $this->successResponse('Subscription deleted successfully');
    }
}
