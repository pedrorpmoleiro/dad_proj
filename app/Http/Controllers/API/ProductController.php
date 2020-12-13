<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Product;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function all()
    {
        return Product::all();
    }
    
    public function delete(Request $request)
    {
        
    }

    public function update(Request $request)
    {

    }

    public function create(Request $request)
    {
        
    }
}
