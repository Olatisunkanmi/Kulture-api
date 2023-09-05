<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SubscriptionFactory extends Factory
{
    
     protected $model = Subscription::class;

    public function definition()
    {
        return [
            'plan' => $this->faker->unique()->randomElement(['Referral Plan', 'Free Plan']),
            'price' => $this->faker->unique()->randomNumber(),
            'upload_limit' => $this->faker->unique()->randomDigitNotZero(),
        ];
    }
}
