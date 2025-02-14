<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warung_id' => 'required|exists:warungs,id',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $totalHarga = 0;

            $order = Order::create([
                'user_id' => Auth::id(),
                'warung_id' => $request->warung_id,
                'status' => 'pending',
                'total_harga' => 0,
            ]);

            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                $harga = $menu->harga;

                // Cek diskon yang berlaku
                $diskon = $menu->discount()
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_berakhir', '>=', now())
                    ->first();

                if ($diskon) {
                    $harga -= ($harga * ($diskon->persentase_diskon / 100));
                }

                $subtotal = $harga * $item['jumlah'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $subtotal,
                ]);

                $totalHarga += $subtotal;
            }

            $order->update(['total_harga' => $totalHarga]);

            DB::commit();

            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        return response()->json(Order::with('items.menu', 'warung')->where('user_id', Auth::id())->get());
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'warung', 'items.menu'])->find($id);

        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }


    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,diproses,selesai'
        ]);

        $order->update(['status' => $request->status]);

        // Simpan ke riwayat pembelian
        OrderHistory::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'warung_id' => $order->warung_id,
            'total_harga' => $order->total_harga,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Status updated and order history recorded']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
