<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ActivityLog;

class logoutController extends Controller
{
    public function logout(){
        // Registrar el logout antes de cerrar sesión
        if (Auth::check()) {
            $user = Auth::user();
            
            \App\Models\ActivityLog::log(
                'logout',
                "Cerró sesión en el sistema",
                null,
                null
            );
            
            // Limpiar session_id del usuario
            $user->session_id = null;
            $user->save();
        }
        
        Session::flush();
        Auth::logout();

        return redirect()->route('login');
    }
}
