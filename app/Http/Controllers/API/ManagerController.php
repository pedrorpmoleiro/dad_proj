<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\User;

class ManagerController extends Controller
{
    public function getEmployees(): JsonResponse
    {
        $employees = User::where('type', 'EC')->orWhere('type', 'ED')->orWhere('type', 'EM')->get();

        return response()->json($employees);
    }
}
