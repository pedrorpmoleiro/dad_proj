<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data = $request->validate([
            'oldPassword' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string', 'min:3'],
        ]);

        if (!Hash::check($data['oldPassword'], $user->password)) {
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
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'regex:/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/'],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'regex:/^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,7}$/'],
            'nif' => ['min:1', 'max:999999999', 'regex:/^\d{0,8}[1-9]$/']
        ]);

        // Authenticate Customer
        $user = Auth::user();

        // Update Customer Data
        $user->email = $data['email'];
        $user->name = $data['name'];
        $user->address = $data['address'];
        $user->phone = $data['phone'];
        $user->nif = $data['nif'];

        // Commit Update
        $user->save();

        // Return OK & Updated User/Customer Data
        $response = $user->toStdClass();

        $customer = Customer::find($user->id);
        $response = $customer->addToStdClass($response, false);


        return response()->json($response);
    }
}
