<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // **Register Mahasiswa & Dosen**
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string|unique:users',
            'email' => [
                'required',
                'email',
                'unique:users',
                function ($attribute, $value, $fail) use ($request) {
                    // Ambil identifier
                    $identifier = $request->identifier;

                    // Periksa apakah email dimulai dengan identifier
                    if (!str_starts_with($value, $identifier . '@')) {
                        $fail('Email harus diawali dengan identifier yang sesuai.');
                    }

                    // Validasi domain sesuai dengan role
                    if ($request->role === 'mahasiswa' && !str_ends_with($value, '@student.universitaspertamina.ac.id')) {
                        $fail('Email mahasiswa harus menggunakan @student.universitaspertamina.ac.id');
                    }

                    if ($request->role === 'dosen' && !str_ends_with($value, '@universitaspertamina.ac.id')) {
                        $fail('Email dosen harus menggunakan @universitaspertamina.ac.id');
                    }
                },
            ],
            'password' => 'required|min:8',
            'role' => 'required|in:mahasiswa,dosen,penjual',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'identifier' => $request->identifier,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified' => false,
        ]);

        // Kirim email verifikasi
        Mail::raw("Klik link berikut untuk verifikasi akun Anda: " . url("/api/verify-email?email=" . $user->email), function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verifikasi Email Anda');
        });

        return response()->json(['message' => 'Registrasi berhasil, silakan cek email untuk verifikasi'], 201);
    }

    // **Verifikasi Email**
    public function verifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak ditemukan'], 404);
        }

        $user->email_verified = true;
        $user->save();

        return response()->json(['message' => 'Email berhasil diverifikasi'], 200);
    }

    // **Login (Mahasiswa: NIM, Dosen: Nama, Penjual: Admin Buatkan Akun)**
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('identifier', $request->identifier)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'NIM/Nama atau password salah'], 401);
        }

        if (!$user->email_verified) {
            return response()->json(['error' => 'Silakan verifikasi email Anda terlebih dahulu'], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'role' => $user->role], 200);
    }

    // **Logout**
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
