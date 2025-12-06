<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PreventMultipleSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = Session::getId();
            
            // Verificar si el usuario tiene una sesión activa diferente
            if ($user->session_id && $user->session_id !== $currentSessionId) {
                // Cerrar la sesión anterior
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Tu sesión fue cerrada porque iniciaste sesión en otro dispositivo/navegador.');
            }
            
            // Actualizar el session_id del usuario
            if ($user->session_id !== $currentSessionId) {
                $user->session_id = $currentSessionId;
                $user->save();
            }
        }
        
        return $next($request);
    }
}
