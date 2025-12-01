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
            \App\Models\ActivityLog::log(
                'logout',
                "Cerró sesión en el sistema",
                null,
                null
            );
        }
        
        Session::flush();
        Auth::logout();

        return redirect()->route('login');
    }
}
