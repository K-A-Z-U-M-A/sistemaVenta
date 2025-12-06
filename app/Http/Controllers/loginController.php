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

        // Primero, verificar que las credenciales sean válidas
        if (!Auth::validate($credentials)) {
            return redirect()->to('login')->withErrors(['email' => 'Credenciales incorrectas']);
        }

        // Buscar el usuario por email
        $user = User::where('email', $credentials['email'])->first();

        // Verificar si el usuario ya tiene una sesión activa
        if ($user && $user->session_id) {
            // Verificar si se está forzando el cierre de la sesión anterior
            if (!$request->has('force_login')) {
                // Guardar credenciales temporalmente en sesión (se eliminarán después)
                $request->session()->put('temp_credentials', [
                    'email' => $credentials['email'],
                    'password' => $credentials['password'],
                    'timestamp' => now()->timestamp
                ]);
                
                // Hay una sesión activa, pedir confirmación
                return redirect()->to('login')
                    ->with('warning', 'Ya existe una sesión activa con esta cuenta en otro dispositivo/navegador.')
                    ->with('show_force_login', true);
            }
            
            // Si se forzó el login, recuperar credenciales de la sesión
            if ($request->session()->has('temp_credentials')) {
                $tempCreds = $request->session()->get('temp_credentials');
                
                // Verificar que las credenciales no sean muy antiguas (5 minutos)
                if ((now()->timestamp - $tempCreds['timestamp']) < 300) {
                    $credentials = [
                        'email' => $tempCreds['email'],
                        'password' => $tempCreds['password']
                    ];
                }
                
                // Eliminar credenciales temporales
                $request->session()->forget('temp_credentials');
            }
            
            // Limpiar la sesión anterior
            $user->session_id = null;
            $user->save();
        }

        // Validar credenciales usando Auth::attempt
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Guardar session_id del usuario
            $user->session_id = session()->getId();
            $user->save();
            
            // Limpiar cualquier credencial temporal que pudiera existir
            $request->session()->forget('temp_credentials');
            
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
