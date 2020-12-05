<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
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
}
