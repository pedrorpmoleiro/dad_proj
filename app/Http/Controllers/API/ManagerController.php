<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Models\User;
use App\Models\Order;

class ManagerController extends Controller
{
    public function getEmployees(): JsonResponse
    {
        $employees = User::where('type', 'EC')->orWhere('type', 'ED')->orWhere('type', 'EM')->get();

        foreach ($employees as $employee)
            try {
                if ($employee->type == 'EC')
                    $employee->order = Order::where('status', 'P')->where('prepared_by', $employee->id)->first();
                else if ($employee->type == 'ED')
                    $employee->order = Order::where('status', 'T')->where('delivered_by', $employee->id)->first();
            } catch (\Exception $e) {
            }

        return response()->json($employees);
    }

    public function getOpenOrders(): JsonResponse
    {
        $orders = Order::where('status', 'H')->orWhere('status', 'P')->orWhere('status', 'R')->orWhere('status', 'T')->get();

        foreach ($orders as $order) {
            $order->customer;
            $customerUser = User::findOrFail($order->customer->id)->toStdClass();
            $order->customer_extra = $customerUser;
            $order->cook;
            $order->delivery_man;
        }

        return response()->json($orders);
    }
}
