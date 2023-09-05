<?php

namespace App\Http\Controllers\Api;

use App\Models\Beat;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\{Producer, User};
use Illuminate\Http\JsonResponse;


use App\Http\Controllers\Controller;
use App\Http\Resources\{ProducerResources};


class ProducerController extends Controller
{
    use ResponseTrait;
    public function index(Request $request): JsonResponse
    {
        try {
            $producers = Producer::latest()->paginate(10)->through(fn ($producer) => new ProducerResources($producer));

            return $this->successResponse('Producers retrieved successfully', $producers);
        } catch (\Throwable $th) {
            return $this->errorResponse('Producers not found');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $producer = User::findOrFail($id);

            if ($producer) {
                $producer = Producer::where('user_id', $id)->first();
                $producer->increment('profile_views');
            } else {
                return $this->errorResponse('User is not a Producer');
            }
            


            return $this->successResponse('Producer retrieved successfully', new ProducerResources($producer));
        } catch (\Throwable $th) {
            return $this->errorResponse('User not found');
        }
    }

    public function trendingProducers(): JsonResponse
    {
        try {


            $producers = Producer::with('user')->orderBy('profile_views', 'desc')->paginate(5)->through(fn ($producer) => new ProducerResources($producer));
            return $this->successResponse('Trending Producers retrieved successfully', $producers);
        } catch (\Throwable $th) {
            return $this->errorResponse('Trending producers not found');
        }
    }

    public function producerDashboard(string $id)
    {
        // $auth = auth()->id();
        $producer = Producer::find($id);
        // echo $producer;
        // echo($producer->total_sales);
        echo($producer->total_beat_sold);
        $uploaded_beats = $producer->beats;
        $liked_beats = Beat::whereHas('favourites');

        return response()->json([
            'data' => $producer,
            'total_sales' => $producer->total_sales,
            'total_beats_sold' => $producer->total_beats_sold,
            'all_beats' => $uploaded_beats,
            'liked_beats' => $liked_beats
        ]);
    }
}
