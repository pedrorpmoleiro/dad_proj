<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Http\Request;

class LoginControllerAPI extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post( env('APP_URL').'/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSPORT_ID'),
                    'client_secret' => env('PASSPORT_SECRET'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => ''
                ],
                'exceptions' => false,
            ]);

            $errorCode = $response->getStatusCode();
            if ($errorCode == '200') {
                return json_decode((string) $response->getBody(), true);
            } else {
                return response()->json(['msg' => 'User credentials are invalid'], $errorCode);
            }
        } catch (GuzzleException $e) {
            return response()->json(['msg' => 'User credentials are invalid'], $e->getCode());
        }

    }

    public function logout()
    {
        \Auth::guard('api')->user()->token()->revoke();
        \Auth::guard('api')->user()->token()->delete();
        return response()->json(['msg' => 'Token revoked'], 200);
    }
}
