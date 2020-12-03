<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Product;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function getProducts()
    {
        return Product::all();
    }
}
