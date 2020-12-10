<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use stdClass;

class UserController extends Controller
{
    public function me()
    {
        $user = Auth::user();

        $response = $user->toStdClass();

        if ($user->type == 'C') {
            $customer = Customer::find($user->id);
            $response = $customer->addToStdClass($response, false);
        }

        return response()->json($response);
    }

    public function updatePassword(Request $request)
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

    public function updateUserData(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'regex:/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/'],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/']
        ]);

        // Authenticate User
        $user = Auth::user();

        // Update user data
        $user->email = $data['email'];
        $user->name = $data['name'];

        // Commit Update
        $user->save();

        // Return OK
        return response()->json($user);
    }

    public function updateCustomerData(Request $request)
    {
        // Authenticate Customer
        $user = Auth::user();

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id), 'regex:/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/'],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'regex:/^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,8}$/'],
            'nif' => ['min:1', 'max:999999999', 'regex:/^\d{0,8}[1-9]$/']
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
}
