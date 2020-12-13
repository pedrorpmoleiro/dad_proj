<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json(Auth::user());
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    public function logout(): JsonResponse
    {
        Auth::guard('web')->logout();
        return response()->json(['message' => 'User session closed'], 200);
    }

    public function registerCustomer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3'],
            // 'type' => ['required', 'string', Rule::in(['c', 'C', 'ec', 'EC', 'ed', 'ED', 'em', 'EM'])],
            'phone' => ['required', 'integer', 'regex:/^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,8}$/'],
            'address' => ['required', 'string', 'max:255'],
            'nif' => ['integer', 'min:1', 'max:999999999']
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['type'] = 'C';

        $user = User::create($data);

        $response = [];

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            $response["errors"] = [
                "msg" => "Unable to send verification email",
                "error" => $e->getMessage()
                ];
        }

        $data['id'] = $user->id;
        Customer::create($data);

        if (Auth::user())
            Auth::guard('web')->logout();

        return response()->json($response, 201);
    }
}
