<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Producer, User};
use App\Traits\ResponseTrait;
use App\Http\Resources\{ProducerResources};
use Illuminate\Http\JsonResponse;


use Illuminate\Http\Request;


class ProducerController extends Controller
{
    use ResponseTrait;
    public function index(Request $request)
    {
        try {
            $producers = Producer::latest()->paginate(10)->through(fn ($producer) => new ProducerResources($producer));

            return $this->successResponse('Producers retrieved successfully', $producers);
        } catch (\Throwable $th) {
            return $this->errorResponse('Producers not found');
        }
    }

    public function show(string $id)
    {
        try {
            $producer = User::findOrFail($id);

            if(!$producer){
                return $this->errorResponse('User is not a Producer');
            } else{
                $producer = Producer::where('user_id', $id)->first();

            }

            //update Producer view count
            $producer->increment('profile_views');

            return $this->successResponse('Producer retrieved successfully', [
                'data' => new ProducerResources($producer)
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse('User not found');
        }
    }

    public function trendingProducers(): JsonResponse
    {
        try {
            $producers = Producer::with('user')->orderBy('profile_views', 'desc')->limit(10)->get();

            return $this->successResponse('Trending producers retrieved successfully', [
                'data' => $producers
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Trending producers not found');
        }
    }
}
