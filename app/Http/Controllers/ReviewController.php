<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Clients;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::all();
        
        $responseData = [];

        foreach ($reviews as $review) {
            $client = Clients::find($review->client_id);
            
            if ($client) {
                $responseData[] = [
                    'review' => $review->review,
                    'client_name' => $client->name
                ];
            } else {
                $responseData[] = [
                    'review' => $review->review,
                    'client_name' => 'Гость'
                ];
            }
        }

        return response()->json([
            'results' => $responseData
        ], 200);
    }

    public function show($id)
    {
        $reviews = Review::where('client_id', $id)->get();
        
        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'Отзывы не найдены'
            ], 404);
        }

        return response()->json([
            'results' => $reviews
        ], 200);
    }

    public function create(Request $request)
    {
        $review = Review::create([
            'client_id' => $request -> client_id,
            'review' => $request->review,
        ]);

        return response()->json([
            'message' => "Спасибо вам за отзыв"
        ], 201);
    }
}