<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Customer;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        // if (Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1]))

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
        return response()->json(['msg' => 'User session closed'], 200);
    }

    public function resendVerificationEmail(): JsonResponse
    {
        $user = Auth::user();
        if ($user->email_verified_at == null)
            return response()->json(['msg' => 'Email already verified']);

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return response()->json([
                "msg" => "Unable to send verification email",
                "error" => $e->getMessage()
            ]);
        }

        return response()->json(['msg' => 'Email verification sent']);
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();
        return response()->json(['msg' => 'Email verified']);
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
