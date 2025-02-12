<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function getSnapToken($order_id)
    {
        $order = Order::with('items.menu')->where('id', $order_id)->where('pembeli_id', Auth::id())->firstOrFail();

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $transaction_details = [
            'order_id'    => $order->id,
            'gross_amount' => $order->total_harga,
        ];

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id'       => $item->menu->id,
                'price'    => $item->harga / $item->jumlah,
                'quantity' => $item->jumlah,
                'name'     => $item->menu->nama,
            ];
        }

        $customer_details = [
            'first_name' => Auth::user()->nim ?? Auth::user()->name,
            'email'      => Auth::user()->email,
        ];

        $transaction = [
            'transaction_details' => $transaction_details,
            'item_details'        => $items,
            'customer_details'    => $customer_details,
        ];

        $snapToken = Snap::getSnapToken($transaction);

        return response()->json(['snap_token' => $snapToken]);
    }

    public function paymentCallback(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan'], 404);
        }

        if ($request->transaction_status == 'settlement') {
            $order->update(['status' => 'paid']);
        } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
            $order->update(['status' => 'canceled']);
        }

        return response()->json(['message' => 'Payment status updated']);
    }
}
