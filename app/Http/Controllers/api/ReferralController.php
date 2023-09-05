<?php

namespace App\Http\Controllers\api;

use App\Models\Referral;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    //

    public static function generateReferralCode(): string
    {

        $code =  strtoupper(Str::random(5));
        $referralCode = 'KULTURE-' . $code;

        return $referralCode;
    }
}
