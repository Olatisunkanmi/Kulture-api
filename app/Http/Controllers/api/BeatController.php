<?php

namespace App\Http\Controllers\api;

use App\Models\Beat;
use App\Http\Controllers\Controller;
use App\Models\{Genre, Producer};
use Illuminate\Http\Request;
use App\Http\Resources\BeatResources;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Services\MediaService;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Http\Requests\{UploadBeatRequest};
use GuzzleHttp\Psr7\Stream;

class BeatController extends Controller
{
    //
    use ResponseTrait;
    public function index(): JsonResponse
    {
        // Default status is 'available'
        $status = request()->query('status', 'available');

     
        // Query the "Beat" model to filter results by the "status" value
        $beats = Beat::where('status', $status)->latest()->paginate(10)->through(fn ($beat) => new BeatResources($beat));

        return $this->successResponse('Beats retrieved successfully', $beats);
    }



    public function upload(UploadBeatRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = Auth()->user();

            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $userId = $user->id;

            $producer = Producer::where('user_id', $userId)->first();

            if (!$producer) {
                return $this->errorResponse('User is not a producer');
            }

            $genre = Genre::where('name', $data['genre'])->first();

            if (!$genre) {
                return $this->errorResponse('Genre not found');
            }
            $genreId = $genre->id;

            if ($request->hasFile('image')) {
                $imageUrl = MediaService::uploadImage($request->file('image'), 'beatsImages');
            }

            if ($request->hasFile('audio')) {
                $audioUrl = MediaService::uploadAudio($request->file('audio'), 'beatsAudios');
            }

            $beat = Beat::create(array_merge(
                $request->validated(),
                [
                    'duration' => '00:51',
                    'size' => $data['audio']->getSize(),
                    'type' => $data['audio']->getMimeType(),
                    'imageUrl' => $imageUrl ?? '',
                    'fileUrl' => $audioUrl,
                    'user_id' => $userId,
                    'producer_id' => $producer->id,
                    'genre_id' => $genreId,
                ]
            ));

            //update producer beats count
            $producer->increment('total_beats');

            //update Genere
            $genre->increment('total_uploads');
            $genre->increment('number_of_beats');



            return $this->successResponse('Beat uploaded successfully', new BeatResources($beat), 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $beat = Beat::find($id);

            if (!$beat) {
                return $this->errorResponse('Beat not found');
            }
            $beat->increment('view_count');


            return $this->successResponse('Beat retrieved successfully', new BeatResources($beat));
        } catch (\Throwable $th) {
            return $this->errorResponse('Beat not found');
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $beat = Beat::findOrFail($id);


            $beat->update($request->all());
            return $this->successResponse('Beat updated successfully', new BeatResources($beat));
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $beat = Beat::findOrFail($id);
            $beat->delete();
            return $this->successResponse('Beat deleted successfully', new BeatResources($beat));
        } catch (\Throwable $th) {
            return $this->errorResponse('Beat not deleted');
        }
    }

    public function trending(Request $request): JsonResponse
    {
        try {
            $beats = Beat::orderBy('view_count', 'desc')->paginate(5)->through(fn ($beat) => new BeatResources($beat));
            return $this->successResponse('Trending Beats retrieved successfully', $beats);
        } catch (\Throwable $th) {
            return $this->errorResponse('Trending beats not found');
        }
    }

    public function download(string $id): StreamedResponse
    {
        try {
            $beat = Beat::find($id);


            if (!$beat) {
                return $this->errorResponse('Beat not found');
            }

            $downloadInfo = MediaService::downloadAsset($beat->fileUrl);


            // Prepare the response for download
            $filename = $downloadInfo['filename'];
            $contentType = $downloadInfo['content-type'];
            $fileContent = $downloadInfo['content'];

            $headers = [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            //update beat download count
            $beat->increment('download_count');

            //update producer download count
            $beat->producer->increment('total_downloads');

            //update genre download count
            $beat->genre->increment('total_downloads');

            return response()->stream(function () use ($fileContent) {
                echo $fileContent;
            }, 200, $headers);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    //search 
    // public function searchByTitle(Request $request): JsonResponse
    // {
    //     $keyword = $request->input('keyword');

    //     $beats = Beat::where('name', 'like', '%' . $keyword . '%')->get();

    //     return response()->json(['beats' => $beats], 200);
    // }

    // public function searchByTitle(Request $request) 
    // {
    //     $beat = Beat::query();

    //     if ($request->has('name')) {
    //         $name = $request->input('name');
    //         $beat->where('name', 'like', '%' .$name. '%');
    //     }

    //     if ($request->has('genre_id')) {
    //         $genre = $request->input('genre_id');
    //         $beat->where('genre_id', 'like', '%' .$genre. '%');
    //     }

    //     if ($request->has('type')) {
    //         $type = $request->input('type');
    //         $beat->where('type', 'like', '%' .$type. '%');
    //     }

    //     $filteredBeats = $beat->get();

    //     return response()->json([
    //         'message' => 'Searched beats successfully',
    //         'data' => BeatResources::collection($filteredBeats)
    //     ]);
    // }
    
    public function search($name)
    {
        return Beat::where('name', 'like', '%' .$name. '%')->get();
    }
    //filter by price
    public function filterByPrice(Request $request): JsonResponse
    {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $beats = Beat::whereBetween('price', [$minPrice, $maxPrice])->get();

        return response()->json(['beats' => $beats], 200);
    }

    //filter by genre
    public function filterByGenre(Request $request): JsonResponse
    {
        $genre = $request->input('genre');
        $beats = Beat::where('genre', $genre)->get();

        return response()->json(['beats' => $beats]);
    }
}

