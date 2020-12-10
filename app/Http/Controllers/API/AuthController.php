<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rule;

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
        // ! TODO
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3'],
            // 'type' => ['required', 'string', Rule::in(['c', 'C', 'ec', 'EC', 'ed', 'ED', 'em', 'EM'])],
            'phone' => ['required', 'integer', 'regex:/^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,7}$/'],
            'address' => ['required', 'string', ''],
            'nif' => ['integer', 'regex:/^\d{0,8}[1-9]$/']
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['type'] = 'C';

        $user = User::create($data);
        $user->sendEmailVerificationNotification();

        $data['id'] = $user->id;
        $customer = Customer::create($data);

        $response = $user->toStdClass();
        $response = $customer->addToStdClass($response, false);

        return response()->json($response, 201);
    }
}
