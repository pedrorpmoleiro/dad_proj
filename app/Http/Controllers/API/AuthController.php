<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return Auth::user();
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return response()->json(['message' => 'User session closed'], 200);
    }

    public function register()
    {
        // ! TODO
    }
}
