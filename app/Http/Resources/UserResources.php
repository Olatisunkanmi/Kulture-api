<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User */

class UserResources extends JsonResource
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
            'type' => 'users',
            'attributes' => [
                'username' => $this->username,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'profile_picture' => $this->profile_picture,
                'upload_limit' => $this->upload_limit,
                'referral_code' => $this->referral_code,
                'referred_by' => $this->referred_by,
                'no_of_referrals' => $this->no_of_referrals,
                'subscription_plan' => $this->subscription_plan,
                'user_type' => $this->user_type,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'referred_by' => $this->referred_by,

            'referrals' => $this->referrals,

            'subscription' => $this->subscription,
        ];
    }
}
