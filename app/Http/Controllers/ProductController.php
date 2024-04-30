<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Product_Size;
use App\Models\Clients_History;
use App\Models\Products_Files;
use App\Models\Product_Images;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Products::all();
        $productsWithRelations = [];
        foreach ($products as $product) {
            $images = Product_Images::where('product_id', $product->id)->get();

            $sizesWithFiles = Product_Size::where('product_id', $product->id)->get()->map(function ($size) {
                $files = Products_Files::where('product_id', $size->id)->get();
                return [
                    'size' => $size->size,
                    'files' => $files
                ];
            });

            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'recomendation' => $product->recomendation,
                'price' => $product->price,
                'images' => $images,
                'sizes' => $sizesWithFiles
            ];

            $productsWithRelations[] = $productData;
        }

        return response()->json([
            'results' => $productsWithRelations
        ], 200);
    }

    public function size($id){
        $product_size = Product_Size::where('product_id', $id)->get();
        return response()->json([
            'results' => $product_size
        ], 200);
    }

    public function show($id)
	{
	    $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Выкройка не найдена'
            ], 404);
        }
        $images = Product_Images::where('product_id', $product->id)->get();
        $sizesWithFiles = Product_Size::where('product_id', $product->id)->get()->map(function ($size) {
            $files = Products_Files::where('product_id', $size->id)->get();
            return [
                'size' => $size->size,
                'files' => $files
            ];
        });

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'recomendation' => $product->recomendation,
            'price' => $product->price,
            'images' => $images,
            'sizes' => $sizesWithFiles
        ];

        return response()->json([
            'results' => $productData
        ], 200);
    }
    public function addProduct(Request $request)
    {
        $uploadImageFolder = 'product_images';
        $uploadFileFolder = 'product_files';

        $product = Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'recomendation' => $request->recomendation,
            'price' => $request->price,
	    'photo' => 'no',
        ]);
        $count = count($request->images);

        if($request -> hasfile('images')) {
            foreach($request->file('images') as $key=>$file)
            {
                $imagePath = $file->store($uploadImageFolder, 'public');
                Product_Images::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                ]);
            }
        }
        foreach($request -> sizes as $sizeData){
            $size = Product_Size::create([
                'product_id' => $product->id,
                'size' => $sizeData['size'],
            ]);
            foreach ($sizeData['file'] as $file) {
                if (is_file($file)){
                    $filePath = $file->store($uploadFileFolder, 'public');
                    Products_Files::create([
                        'product_id' => $size->id,
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Выкройка успешно добавлена'], 201);
    }
    public function buy($client_id, $product_id){
        Clients_History::create([
            'client_id' => $client_id,
            'product_history_id' => $product_id
        ]);
        return response()->json(['message' => 'Выкройка успешно куплена'], 201);
    }
    public function destroy($id)
{
    // Find the product by ID
    $product = Products::find($id);

    // If the product is not found, return a 404 response
    if (!$product) {
        return response()->json([
            'error' => 'Product not found',
        ], 404);
    }

    // Delete related images
    Product_Images::where('product_id', $id)->delete();

    // Delete related sizes and their files
    $sizes = Product_Size::where('product_id', $id)->get();
    foreach ($sizes as $size) {
        // Delete files for each size
        Products_Files::where('product_id', $size->id)->delete();
    }

    // Delete related sizes after deleting files
    Product_Size::where('product_id', $id)->delete();

    // Delete the product
    $product->delete();

    // Return a success response
    return response()->json([
        'message' => 'Product and its related records deleted successfully',
    ], 200);
}

}
