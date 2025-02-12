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
        return response()->json(Warung::with('penjual')->get());
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
