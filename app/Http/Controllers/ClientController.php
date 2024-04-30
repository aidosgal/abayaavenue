<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clients;
use App\Models\Clients_History;
use App\Models\Products_Files;
use App\Models\Product_Size;
use App\Models\Products;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Auth;

class ClientController extends Controller
{
    public function index()
    {
	   $client = Clients::all();
       return response()->json([
        'results' => $client
       ],200);
	}

    public function show($id)
	{
	    $client = Clients::find($id);
        if(!$client){
            return response()->json([
                'message'=>'Клиент не найден'
            ],200);
        }
        return response()->json([
           'results' => $client
        ],200);
    }
    
    public function history($id)
    {
        // Get all history records for the given client
        $history = Clients_History::where("client_id", $id)->get();
        
        $responseData = [];

        foreach ($history as $item) {
            $product_size = Product_Size::find($item->product_history_id);

            if ($product_size) {
                $product = Products::find($product_size->product_id);

                // Fetch all files associated with this product size
                $product_files = Products_Files::where("product_id", $product_size->id)->get();

                // Build a structure for product_files including file_path
                $file_paths = $product_files->map(function($file) {
                    return $file->file_path;
                });

                $responseData[] = [
                    'product_size' => $product_size->size,  // The size of the product
                    'product_name' => $product ? $product->name : null,
                    'file_paths' => $file_paths,  // All file paths for this product
                ];
            }
        }

        return response()->json([
            'results' => $responseData
        ], 200);
    }


    public function register(Request $request)
    {
        $client = Clients::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'id' => $client->id
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return response()->json([
                'message' => 'Клиент не найден'
            ], 404);
        }

        $client->name = $request->name;
        $client->email = $request->email;

        return response()->json([
            'message' => 'Данные клиента обновлены'
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $client = Auth::user();

            return response()->json([
                'id' => $client->id,
            ], 200);
        }

        throw ValidationException::withMessages([
            'message' => ['Неверные учетные данные']
        ]);
    }


    public function change_password(Request $request, $id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return response()->json([
                'message' => 'Клиент не найден'
            ], 404);
        }

        if (!Hash::check($request->old_password, $client->password)) {
            return response()->json([
                'message' => 'Неверный старый пароль'
            ], 400);
        }

        $client->password = Hash::make($request->new_password);
        $client->save();

        return response()->json([
            'message' => 'Пароль успешно изменен'
        ], 200);
    }
}