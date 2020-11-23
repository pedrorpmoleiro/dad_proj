<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductControllerAPI extends Controller
{
    public function getProducts()
    {
        return Product::all();
    }
}
