<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WarungController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warungs = Warung::with('penjual', 'menus')->get();

        return response()->json([
            'warungs' => $warungs->map(function ($warung) {
                return [
                    'id' => $warung->id,
                    'nama' => $warung->nama,
                    'penjual' => $warung->penjual,
                    'menus' => $warung->menus->map(function ($menu) {
                        return [
                            'id' => $menu->id,
                            'nama' => $menu->nama,
                            'kategori' => $menu->kategori, // Ambil nilai enum kategori
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'penjual') {
            return response()->json(['error' => 'Hanya penjual yang bisa menambahkan warung'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|unique:warungs',
            'lokasi' => 'required|in:kantin atas,kantin bawah'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $warung = Warung::create([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'penjual_id' => Auth::id()
        ]);

        return response()->json($warung, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
        $warung = Warung::where('id', $id)->first();
        if (!$warung) {
            return response()->json(['message' => 'Warung tidak ditemukan'], 404);
        }
        return response()->json([
            'warung' => $warung,
            'menus' => $warung->menus // Pastikan ini sesuai dengan relasi di model
        ]);
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
