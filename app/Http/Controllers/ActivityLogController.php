<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Mostrar el registro de actividades (solo administradores)
     */
    public function index(Request $request)
    {
        // Verificar que el usuario sea administrador (el rol puede ser "Administrador" o "administrador")
        if (!auth()->user()->hasAnyRole(['Administrador', 'administrador'])) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Paginación
        $activities = $query->paginate(50);

        // Obtener usuarios para el filtro
        $users = User::orderBy('name')->get();

        // Tipos de acciones disponibles
        $actions = ActivityLog::distinct()->pluck('action')->filter();

        // Tipos de modelos
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter();

        return view('activity-log.index', compact('activities', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Ver detalles de una actividad específica
     */
    public function show(ActivityLog $activityLog)
    {
        if (!auth()->user()->hasAnyRole(['Administrador', 'administrador'])) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        return view('activity-log.show', compact('activityLog'));
    }

    /**
     * Limpiar logs antiguos (opcional)
     */
    public function clean(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['Administrador', 'administrador'])) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $days = $request->input('days', 90);
        
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Se eliminaron {$deleted} registros antiguos.");
    }
}
