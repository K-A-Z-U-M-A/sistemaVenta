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
        $isSessionActive = false;
        
        if ($user && $user->session_id) {
            // Verificar si la sesión está realmente activa (actividad reciente < 2 min)
            // Esto evita bloquear al usuario si solo cerró el navegador sin hacer logout
            
            $driver = config('session.driver');
            
            if ($driver === 'file') {
                $sessionPath = config('session.files') . '/' . $user->session_id;
                if (file_exists($sessionPath)) {
                    // Si el archivo existe y se modificó hace menos de 120 segundos
                    if ((time() - filemtime($sessionPath)) < 120) {
                        $isSessionActive = true;
                    }
                }
            } elseif ($driver === 'database') {
                $table = config('session.table');
                // Need to import DB facade if not already imported
                // use Illuminate\Support\Facades\DB;
                $session = \Illuminate\Support\Facades\DB::table($table)->where('id', $user->session_id)->first();
                if ($session && (time() - $session->last_activity < 120)) {
                    $isSessionActive = true;
                }
            }
            // Si usas otro driver (redis, etc), asume false o implementa lógica específica
        }

        if ($isSessionActive) {
            // Verificar si se está forzando el cierre de la sesión anterior
            if (!$request->has('force_login')) {
                // Guardar credenciales temporalmente en sesión (se eliminarán después)
                $request->session()->put('temp_credentials', [
                    'email' => $credentials['email'],
                    'password' => $credentials['password'],
                    'timestamp' => now()->timestamp
                ]);
                
                // Hay una sesión activa RECIENTE, pedir confirmación
                return redirect()->to('login')
                    ->with('warning', 'Se detectó actividad reciente en otra sesión con esta cuenta.')
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
            
            // Limpiar la sesión anterior (solo referencia en DB)
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
