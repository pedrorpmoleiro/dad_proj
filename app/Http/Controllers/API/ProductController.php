<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(Product::all());
    }

    public function getProduct(Request $request): JsonResponse
    {
        $id = $request->id;

        // Find product
        $product = Product::findOrFail($id);

        // Return OK
        return response()->json($product);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;

        // Find product
        $product = Product::findOrFail($id);

        // Validate if model instance has been soft deleted
        if ($product->trashed())
            return response()->json(null, 404);

        // Delete
        $product->delete();

        // Return OK
        return response()->json(null);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['drink', 'dessert', 'cold dish', 'hot dish'])],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            "photo" => ["file", "mimetypes:image/png,image/jpeg"]
        ]);

        // Find product
        $product = Product::findOrFail($data['id']);

        // Update product data
        $product->name = $data['name'];
        $product->type = $data['type'];
        $product->description = $data['description'];
        $product->price = $data['price'];

        try {
            if ($data['photo']) {
                $photo = $request->file("photo");
                $photoName = uniqid() . '.' . $photo->extension();
                $photo->storePubliclyAs("public/products", $photoName);

                $product->photo_url = $photoName;
            }
        } catch (\Exception $e) {
        }

        // Commit Update
        $product->save();

        // Return OK
        return response()->json($product);
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['drink', 'dessert', 'cold dish', 'hot dish'])],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            "photo" => ["required", "file", "mimetypes:image/png,image/jpeg"]
        ]);

        try {
            if ($data['photo']) {
                $photo = $request->file("photo");
                $photoName = uniqid() . '.' . $photo->extension();
                $photo->storePubliclyAs("public/products", $photoName);

                $data['photo_url'] = $photoName;
            }
        } catch (\Exception $e) {
            $data['photo_url'] = "";
        }

        // Create Product
        $product = Product::create($data);

        // Return OK
        return response()->json($product, 201);
    }
}
