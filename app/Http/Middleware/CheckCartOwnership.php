<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CheckCartOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // //Check if the user is a guest or a producer
        // if (auth()->guest() || auth()->user()->isProducer()){
        //    return response()->json([
        //     'message' => 'You do not have access to the cart',
        // ], 201);
        // }
        // return $next($request);
    }
}
