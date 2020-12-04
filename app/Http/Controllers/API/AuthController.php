<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;
use App\Models\User;

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

    public function register(Request $request)
    {
        // ! TODO
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'integer', 'regex:/^([\+]|[0]{2})?[1-9]\d{1,14}$/'],
            'type' => ['required', 'string', Rule::in(['c', 'C', 'ec', 'EC', 'ed', 'ED', 'em', 'EM'])],
            'nif' => ['integer', 'regex:/^\d{0,8}[1-9]$/']
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $user->sendEmailVerificationNotification();

        //

        return response()->json($user, 201);
    }
}
