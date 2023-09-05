<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['plan', 'price', 'upload_limit'];


    /**
     * Get the users associated with the subcription.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\User>
     */

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'subscription_plan_id');
    }
}
