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
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $user->logged_at = date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS'));

            if ($user->type == 'ED' || $user->type == 'EM' || $user->type == 'EC')
                $user->available_at = $user->logged_at;

            $user->save();

            $response = $user->toStdClass();

            if ($user->type == 'C') {
                $customer = Customer::find($user->id);
                $response = $customer->addToStdClass($response, false);
            }

            return response()->json($response);
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        $user->logged_at = null;

        if ($user->type == 'ED' || $user->type == 'EM' || $user->type == 'EC')
            $user->available_at = null;

        $user->save();

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
        $response = [];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3'],
            'phone' => ['required', 'integer', 'regex:/^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,8}$/'],
            'address' => ['required', 'string', 'max:255'],
            'nif' => ['integer', 'min:1', 'max:999999999'],
            "photo" => ["file", "mimetypes:image/png,image/jpeg"]
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['type'] = 'C';

        try {
            if ($data['photo']) {
                $photo = $request->file("photo");
                $photoName = uniqid() . '.' . $photo->extension();
                $photo->storePubliclyAs("public/fotos", $photoName);

                $data['photo_url'] = $photoName;
            }
        } catch (\Exception $e) {
            $response["errors"] = [
                "msg" => "Unable to save user photo",
                "error" => $e->getMessage()
            ];
        }

        $user = User::create($data);

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
