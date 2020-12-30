<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\Finder\Exception\AccessDeniedException;

class OrderController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        if ($request->input("products") == null && count($request->input("products")) <= 0)
            return response()->json(["msg" => "Field 'products' must     be an array with at least one product"]);

        $products = [];
        $total = 0;

        foreach ($request->input("products") as $item) {
            $p = Product::findOrFail($item["product_id"]);
            $subTotal = $p->price * $item["quantity"];
            $products[$p->id] = [
                "quantity" => $item["quantity"],
                "unit_price" => $p->price,
                "sub_total_price" => round($subTotal, 2)
            ];
            $total += $subTotal;
        }

        $total = round($total, 2);

        $data = [];
        $data["customer_id"] = Auth::user()->id;
        $data["status"] = "H";
        $data["total_price"] = $total;

        if ($request->input("notes") != null)
            $data["notes"] = $request->input("notes");

        $data["date"] = date(env('INPUT_FORMAT_DATE'));
        $data["opened_at"] = date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS'));
        $data["current_status_at"] = $data["opened_at"];

        $order = Order::create($data);

        $orderItems = [];

        foreach ($products as $k => $i) {
            $aux = array_merge($i, ["order_id" => $order->id, "product_id" => $k]);
            array_push($orderItems, $aux);
            OrderItem::create($aux);
        }

        return response()->json($order, 201);
    }

    public function getCustomerOrders(): JsonResponse
    {
        $customer = Customer::findOrFail(Auth::user()->id);

        $orders = $customer->orders;

        foreach ($orders as $order) {
            $order->cook;
            $order->delivery_man;
        }

        return response()->json($orders);
    }

    public function getCustomerOpenOrders(): JsonResponse
    {
        $customer = Customer::findOrFail(Auth::user()->id);

        $orders = $customer->orders()->whereNotIn('status', ['C', 'D'])->get();

        foreach ($orders as $order) {
            $order->cook;
            $order->delivery_man;
        }

        return response()->json($orders);
    }

    public function getCustomerOrderHistory(): JsonResponse
    {
        $customer = Customer::findOrFail(Auth::user()->id);

        $orders = $customer->orders()->whereIn('status', ['C', 'D'])->get();

        foreach ($orders as $order) {
            $order->cook;
            $order->delivery_man;
        }

        return response()->json($orders);
    }

    public function getCurrentCookOrder(): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('status', 'P')->where('prepared_by', $user->id)->first();

        if ($order != null) {
            $order->customer;
            $customerUser = User::findOrFail($order->customer->id)->toStdClass();
            $order->customer_extra = $customerUser;
            $order->items;
        } else
            $order = ["error" => "No order to prepare"];

        return response()->json($order);
    }

    public function setOrderPrepared(): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('status', 'P')->where('prepared_by', $user->id)->first();

        if ($order == null)
            return response()->json(null, 404);

        $order->status = 'R';
        $order->preparation_time = time() - strtotime($order->current_status_at);
        $order->current_status_at = date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS'));

        $order->save();

        return response()->json(null);
    }

    public function getDeliverymanOrders(): JsonResponse
    {
        $user = Auth::user();
        $currentOrder = Order::where('status', 'T')->where('delivered_by', $user->id)->first();

        if ($currentOrder != null) {
            $currentOrder->customer;
            $customerUser = User::findOrFail($currentOrder->customer->id)->toStdClass();
            $currentOrder->customer_extra = $customerUser;
            $currentOrder->items;

            return response()->json(["currentOrder" => $currentOrder]);
        } else {
            $ordersReady = Order::where('status', 'R')->get();

            foreach ($ordersReady as $order) {
                $order->customer;
                $customerUser = User::findOrFail($order->customer->id)->toStdClass();
                $order->customer_extra = $customerUser;
            }

            if ($ordersReady != null)
                return response()->json(["availableOrders" => $ordersReady]);
            else
                return response()->json(["error" => "No orders to deliver"]);
        }
    }

    public function setOrderInTransit(Request $request): JsonResponse
    {
        $id = $request->id;

        $order = Order::findOrFail($id);
        $user = Auth::user();

        $order->status = 'T';
        $order->delivered_by = $user->id;
        $order->current_status_at = date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS'));

        $order->save();

        return response()->json(null);
    }

    public function setOrderDelivered(): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('status', 'T')->where('delivered_by', $user->id)->first();

        if ($order == null)
            return response()->json(null, 404);

        $order->status = 'D';

        $order->save();

        $order->delivery_time = time() - strtotime($order->current_status_at);
        $order->current_status_at = date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS'));
        $order->closed_at = $order->current_status_at;
        $order->total_time = strtotime($order->closed_at) - strtotime($order->opened_at);

        $order->save();

        return response()->json(null);
    }

    public function getOrder($id): JsonResponse
    {
        $user = Auth::user();

        $order = Order::findOrFail($id);

        $order->customer;
        $order->cook;
        $order->delivery_man;

        switch ($user->type) {
            case 'C':
                if ($user->id != $order->customer->id)
                    throw new AccessDeniedException("Requested Order doesn't belong to this customer");
                break;
                // TODO REVIEW
                // ! Ready for custom checks for other user types
        }

        return response()->json($order);
    }
}
