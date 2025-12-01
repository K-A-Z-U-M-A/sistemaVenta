<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('panel');
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Validar credenciales usando Auth::attempt que maneja bcrypt correctamente
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Registrar el login
            \App\Models\ActivityLog::log(
                'login',
                "Inició sesión en el sistema",
                null,
                null,
                ['email' => $user->email]
            );
            
            return redirect()->route('panel')->with('success', 'Bienvenido ' . $user->name);
        }

        return redirect()->to('login')->withErrors(['email' => 'Credenciales incorrectas']);
    }
}
