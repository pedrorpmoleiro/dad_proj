<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        if ($request->input("products") == null && count($request->input("products")) <= 0)
            return response()->json(["msg" => "Field 'products' must     be an array with at least one product"]);

        $products = [];
        $total = 0;

        foreach ($request->input("products") as $item) {
            $p = Product::findOrFail($item["product_id"]);
            $subTotal = $p->price * $item["quantity"];
            $products[$p->id] = [
                "quantity" => $item["quantity"],
                "unit_price" => $p->price,
                "sub_total_price" => $subTotal
            ];
            $total += $subTotal;
        }

        $data = [];
        $data["customer_id"] = Auth::user()->id;
        $data["status"] = "H";
        $data["total_price"] = $total;

        if ($request->input("notes") != null)
            $data["notes"] = $request->input("notes");

        $data["date"] = date(env('INPUT_FORMAT_DATE'));
        $data["opened_at"] = date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS'));
        $data["current_status_at"] = $data["opened_at"];

        dd($data);
    }
}
