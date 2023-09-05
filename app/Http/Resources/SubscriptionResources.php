<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *  @mixin \App\Models\Subscription
 */


class SubscriptionResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attributes' => [
                'plan' => $this->plan,
                'price' => $this->price,
                'no_of_subscribers' => $this->users->count(),
                'upload_limit' => $this->upload_limit,
            ],

            'subscribers' => $this->users,
        ];
    }
}
