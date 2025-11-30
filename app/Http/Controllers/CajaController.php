<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CajaController extends Controller
{
    /**
     * Mostrar el estado actual de la caja
     */
    public function index()
    {
        $cajaAbierta = Caja::where('estado', 'abierta')
            ->where('user_id', auth()->id())
            ->first();

        $cajasCerradas = Caja::where('user_id', auth()->id())
            ->where('estado', 'cerrada')
            ->orderBy('fecha_cierre', 'desc')
            ->limit(10)
            ->get();

        return view('caja.index', compact('cajaAbierta', 'cajasCerradas'));
    }

    /**
     * Abrir caja
     */
    public function abrir(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        // Verificar que no haya una caja abierta
        $cajaAbierta = Caja::where('estado', 'abierta')
            ->where('user_id', auth()->id())
            ->first();

        if ($cajaAbierta) {
            return back()->with('error', 'Ya tienes una caja abierta. Debes cerrarla primero.');
        }

        Caja::create([
            'user_id' => auth()->id(),
            'monto_inicial' => $request->monto_inicial,
            'fecha_apertura' => Carbon::now(),
            'estado' => 'abierta'
        ]);

        return back()->with('success', 'Caja abierta exitosamente');
    }

    /**
     * Cerrar caja
     */
    public function cerrar(Request $request, Caja $caja)
    {
        $request->validate([
            'monto_final' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        // Verificar que la caja pertenezca al usuario
        if ($caja->user_id !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para cerrar esta caja.');
        }

        // Verificar que la caja esté abierta
        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Esta caja ya está cerrada.');
        }

        // Calcular ingresos de ventas de esta caja
        $ventasEfectivo = Venta::where('caja_id', $caja->id)
            ->where('forma_pago', 'efectivo')
            ->where('estado', 1)
            ->sum('total');

        $ventasTarjeta = Venta::where('caja_id', $caja->id)
            ->where('forma_pago', 'tarjeta')
            ->where('estado', 1)
            ->sum('total');

        // Calcular diferencia
        $totalEsperado = $caja->monto_inicial + $ventasEfectivo - $caja->egresos;
        $diferencia = $request->monto_final - $totalEsperado;

        $caja->update([
            'monto_final' => $request->monto_final,
            'ingresos_efectivo' => $ventasEfectivo,
            'ingresos_tarjeta' => $ventasTarjeta,
            'diferencia' => $diferencia,
            'observaciones' => $request->observaciones,
            'fecha_cierre' => Carbon::now(),
            'estado' => 'cerrada'
        ]);

        // --- INICIO BACKUP AUTOMATICO ---
        try {
            $filename = 'backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Asegurar que existe el directorio
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            // Configuración DB
            $dbHost = config('database.connections.pgsql.host');
            $dbPort = config('database.connections.pgsql.port');
            $dbName = config('database.connections.pgsql.database');
            $dbUser = config('database.connections.pgsql.username');
            $dbPass = config('database.connections.pgsql.password');

            // Comando pg_dump (Ruta absoluta para Windows)
            $pgDumpPath = '"C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe"';
            
            // Construir comando
            $command = "set PGPASSWORD={$dbPass} && {$pgDumpPath} -h {$dbHost} -p {$dbPort} -U {$dbUser} -F p -f \"{$path}\" {$dbName}";
            
            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                // Subir a Drive si está configurado
                if (config('filesystems.disks.google.clientId')) {
                    try {
                        Storage::disk('google')->put('Backups/' . $filename, file_get_contents($path));
                        Log::info("Backup subido a Drive: " . $filename);
                    } catch (\Exception $e) {
                        Log::error("Error subiendo a Drive: " . $e->getMessage());
                        $request->session()->flash('warning', 'Caja cerrada, pero falló la subida a Drive. El backup local está seguro.');
                    }
                }
            } else {
                Log::error("Error generando backup local. Código: " . $returnVar);
            }

        } catch (\Exception $e) {
            Log::error("Error en proceso de backup: " . $e->getMessage());
        }
        // --- FIN BACKUP AUTOMATICO ---

        return back()->with('success', 'Caja cerrada exitosamente');
    }

    /**
     * Ver detalle de una caja cerrada
     */
    public function show(Caja $caja)
    {
        // Verificar que la caja pertenezca al usuario o sea admin
        if ($caja->user_id !== auth()->id() && !auth()->user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para ver esta caja.');
        }

        $ventas = Venta::where('caja_id', $caja->id)
            ->where('estado', 1)
            ->with(['productos', 'cliente'])
            ->get();

        return view('caja.show', compact('caja', 'ventas'));
    }

    /**
     * Balance general
     */
    public function balance(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        $cajas = Caja::whereBetween('fecha_apertura', [$fechaInicio, $fechaFin])
            ->with('user')
            ->orderBy('fecha_apertura', 'desc')
            ->get();

        $totalIngresos = $cajas->sum('ingresos_efectivo') + $cajas->sum('ingresos_tarjeta');
        $totalEgresos = $cajas->sum('egresos');
        $totalDiferencias = $cajas->sum('diferencia');

        return view('caja.balance', compact('cajas', 'totalIngresos', 'totalEgresos', 'totalDiferencias', 'fechaInicio', 'fechaFin'));
    }
}
