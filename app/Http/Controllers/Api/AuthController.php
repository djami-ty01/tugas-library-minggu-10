<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Cek user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Verifikasi password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return format response sesuai modul
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer'
        ], 200);
    }
    // Menampilkan data user yang sedang login
public function me(Request $request)
{
    return response()->json($request->user(), 200);
}

// Melakukan Logout dan menghapus token saat ini
public function logout(Request $request)
{
    // Cek dulu apakah user terdeteksi sebelum menghapus token
    if ($request->user()) {
        $request->user()->currentAccessToken()->delete();
    }

    return response()->json([
        'message' => 'Logged out'
    ], 200);
}
}
