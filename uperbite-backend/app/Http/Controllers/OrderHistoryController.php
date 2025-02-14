<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Jika user adalah pembeli, tampilkan riwayat belanja
        if ($user->role == 'mahasiswa' ||'dosen' ) {
            $history = OrderHistory::where('user_id', $user->id)->with('order')->get();
        }
        // Jika user adalah penjual, tampilkan riwayat transaksi di warungnya
        elseif ($user->role == 'penjual') {
            $history = OrderHistory::whereHas('warung', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with('order')->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($history);
    }

    public function show($id)
    {
        $history = OrderHistory::with(['order', 'warung'])->findOrFail($id);
        return response()->json($history);
    }
}
