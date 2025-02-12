<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Warung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Menu::with('warung');

        // Filter berdasarkan kategori (jika ada)
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan warung_id (jika ada)
        if ($request->has('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request, $warung_id)
    {
        $warung = Warung::find($warung_id);

        if (!$warung) {
            return response()->json(['error' => 'Warung tidak ditemukan'], 404);
        }

        if (Auth::id() !== $warung->penjual_id) {
            return response()->json(['error' => 'Anda bukan pemilik warung ini'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|in:Makanan,Minuman,Snack'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $menu = Menu::create([
            'warung_id' => $warung_id,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'kategori' => $request->kategori
        ]);

        return response()->json($menu, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
