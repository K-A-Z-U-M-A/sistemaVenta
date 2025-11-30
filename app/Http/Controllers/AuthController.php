<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $credentials['password'] === $user->password) {
            // Autenticación exitosa
            return response()->json(['message' => 'Login exitoso'], 200);
        } else {
            // Fallo en la autenticación
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
    }
}
