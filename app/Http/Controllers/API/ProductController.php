<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function all()
    {
        return Product::all();
    }

    public function getProduct($id): JsonResponse
    {
        // Find product
        $product = Product::findOrFail($id);

        // Return OK
        return response()->json($product);
    }

    public function delete($id): JsonResponse
    {
        // Find product
        $product = Product::findOrFail($id);

        // Validate if model instance has been soft deleted
        if ($product->trashed()) {
            return response()->json(null, 404);
        }

        // Delete
        $product->delete();


        // Return OK
        return response()->json(null, 201);

    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'numeric', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['Drink', 'Dessert', 'Cold dish', 'Hot dish'])],
            'description' => ['required', 'string', 'max:500'],
            'price' => ['required', 'min:0.01', 'max:999.99', 'regex:/^\s*-?[1-9]\d{0,3}(\.\d{1,2})?\s*$/'],
            'photo_url' => ['required'],
        ]);

        // TODO: Must Auth User?
        // $user = Auth::user();

        // Find product
        $product = Product::findOrFail($data['id']);

        // Update product data
        $product->name = $data['name'];
        $product->type = $data['type'];
        $product->description = $data['description'];
        $product->price = $data['price'];
        $product->photo_url = $data['photo_url'];


        // Commit Update
        $product->save();

        // Return OK
        return response()->json($product);
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['Drink', 'Dessert', 'Cold dish', 'Hot dish'])],
            'description' => ['required', 'string', 'max:500'],
            'photo_url' => ['required'],
            'price' => ['required', 'min:0.01', 'max:999.99', 'regex:/^\s*-?[1-9]\d{0,3}(\.\d{1,2})?\s*$/'],
            'updated_at' => [],
            'created_at' => [],
        ]);

        $data['updated_at'] = date(env('INPUT_FORMAT_DATE'));
        $data['created_at'] = date(env('INPUT_FORMAT_DATE'));


        // Create Product
        Product::create($data);

        // Return OK
        return response()->json(null, 201);


    }
}
