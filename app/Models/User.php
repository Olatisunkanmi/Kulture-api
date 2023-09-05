<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Artiste;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{HasOne, BelongsTo, HasMany};
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'confirm_password',
        'user_type',
        'profile_picture',
        'upload_limit',
        'referral_code',
        'referred_by',
        'subscription_plan',
        'subscription_plan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'confirm_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'confirm_password' => 'hashed'
    ];

    /**
     * Generate token based on user Type
     */

    public function generateToken(): string
    {
        if (Str::contains($this->email, 'zojatech.com')) {

            $token = $this->createToken($this->email, ['admin'])->accessToken;
        } else {
            $token = $this->createToken($this->email, [$this->user_type])->accessToken;
        }

        return $token;
    }

    /**
     * Get the producer associated with the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Producer>
     */
    public function producers(): HasOne
    {
        return $this->hasOne(Producer::class);
    }

    /**
     * Get the producer associated with the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Artiste>
     */

    public function artistes(): HasOne
    {
        return $this->hasOne(Artiste::class);
    }

    /**
     * Get the subscription associated with this user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Subscription, \App\Models\User>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_plan_id');
    }

    /**
     * Get the referrer associated with this user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Referral>
     */

    public function referred_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referrals associated with this user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Referral>
     */

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referred_by');
    }

    public function guardName(): mixed
    {
        return 'api';
    }

    // public function favourites(): BelongsToMany
    // {
    //     return $this->belongsToMany(Beat::class, 'favourites', 'user_id', 'beat_id')
    //     ->select('fileUrl', 'imageUrl', 'genre', 'name', 'price', 'id')
    //     ->withTimestamps();
    // }
}
