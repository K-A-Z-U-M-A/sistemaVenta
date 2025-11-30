<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    function __construct()
    {
        // Solo el administrador debería tener acceso a esto
        $this->middleware('role:administrador');
    }

    public function index()
    {
        $serverInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'db_connection' => DB::connection()->getDatabaseName(),
            'db_driver' => DB::connection()->getDriverName(),
            'server_os' => php_uname(),
        ];

        return view('system.index', compact('serverInfo'));
    }

    public function optimize()
    {
        try {
            Artisan::call('optimize:clear');
            return redirect()->back()->with('success', 'Sistema optimizado y cachés limpiadas.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al optimizar: ' . $e->getMessage());
        }
    }

    public function cacheConfig()
    {
        try {
            Artisan::call('config:cache');
            return redirect()->back()->with('success', 'Configuración cacheada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function cacheRoute()
    {
        try {
            Artisan::call('route:cache');
            return redirect()->back()->with('success', 'Rutas cacheadas correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function cacheView()
    {
        try {
            Artisan::call('view:cache');
            return redirect()->back()->with('success', 'Vistas cacheadas correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
