<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use stdClass;

class UserController extends Controller
{
    public function all()
    {
        return User::all();
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        $response = $user->toStdClass();

        if ($user->type == 'C') {
            $customer = Customer::find($user->id);
            $response = $customer->addToStdClass($response, false);
        }

        return response()->json($response);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'passwordOld' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string', 'min:3'],
        ]);

        if (!Hash::check($data['passwordOld'], $user->password)) {
            return response()->json(null, 400);
        }

        $user->password = Hash::make($data['password']);

        $user->save();

        return response()->json(null);
    }

    public function updateUserData(Request $request): JsonResponse
    {

        // Authenticate User
        $user = Auth::user();

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/']
        ]);


        // Update user data
        $user->email = $data['email'];
        $user->name = $data['name'];

        // Commit Update
        $user->save();

        // Return
        return response()->json($user);
    }

    public function updateCustomerData(Request $request): JsonResponse
    {
        // Authenticate Customer
        $user = Auth::user();

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id), 'regex:/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/'],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'regex:/^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,8}$/'],
            'nif' => ['integer', 'min:1', 'max:999999999']
        ]);

        $response = new stdClass();

        if ($user->type == 'C') {
            $customer = Customer::findOrFail($user->id);

            $customer->phone = $data['phone'];
            $customer->nif = $data['nif'];
            $customer->address = $data['address'];

            $customer->save();

            $response = $customer->addToStdClass($response, false);
        }

        // Update Customer Data
        $user->email = $data['email'];
        $user->name = $data['name'];

        // Commit Update
        $user->save();

        $response = $user->addToStdClass($response);


        return response()->json($response);
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3'],
            'type' => ['required', 'string', Rule::in(['c', 'C', 'ec', 'EC', 'ed', 'ED', 'em', 'EM'])],
        ]);

        // Hash password
        $data['password'] = Hash::make($data['password']);

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

        return response()->json(null, 201);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;

        // Find User
        $user = User::findOrFail($id);

        // Validate if model instance has been soft deleted
        if ($user->trashed())
            return response()->json(null, 404);

        if ($user->type == 'C') {
            // Find Customer
            $customer = Customer::findOrFail($id);

            // Validate if model instance has been soft deleted
            if ($customer->trashed())
                return response()->json(null, 404);


            // Delete Customer
            $customer->delete();
        }

        // Delete User
        $user->delete();

        // Return OK
        return response()->json(null);
    }

    public function block(Request $request): JsonResponse
    {
        // Get User ID
        $id = $request->id;

        // Find User
        $user = User::findOrFail($id);

        // Se if user is to be blocked or unblocked
        if ($user->blocked == 0)
            $user->blocked = 1;
        else
            $user->blocked = 0;

        // Commit update
        $user->save();

        // Return OK
        return response()->json(null);
    }

    public function patchUserData(Request $request): JsonResponse
    {
        // Get User ID & Find User
        $user = User::findOrFail($request->id);

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/'],
            'type' => ['required', 'string', Rule::in(['EC', 'C', 'EM', 'ED'])],
        ]);


        // Update user data
        $user->email = $data['email'];
        $user->name = $data['name'];
        $user->type = $data['type'];

        // Commit Update
        $user->save();

        // Return OK
        return response()->json(null);
    }
}
