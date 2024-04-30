<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\Clients;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::all();

        return response()->json([
            'results' => $promos
        ], 200);
    }

    public function show($id)
    {
        $promos = Promo::where('name', $id)->get();
        
        if ($promos->isEmpty()) {
            return response()->json([
                'message' => 'Промокод не найден'
            ], 404);
        }

        return response()->json([
            'results' => $promos
        ], 200);
    }

    public function create(Request $request)
    {
        $Promo = Promo::create([
            'name' => $request -> name,
            'sale' => $request->sale,
        ]);

        return response()->json([
            'message' => "Прмоокод добавлен"
        ], 201);
    }
}