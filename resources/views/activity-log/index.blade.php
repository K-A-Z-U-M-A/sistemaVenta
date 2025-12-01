@extends('layouts.app')

@section('title','Registro de Actividad')

@section('content')

@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">📋 Registro de Actividad del Sistema</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Registro de Actividad</li>
    </ol>

    {{-- Filtros Avanzados --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa-solid fa-filter"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('activity-log.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="user_id" class="form-label fw-bold">Usuario</label>
                        <select class="form-select" name="user_id" id="user_id">
                            <option value="">Todos los usuarios</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="action" class="form-label fw-bold">Acción</label>
                        <select class="form-select" name="action" id="action">
                            <option value="">Todas las acciones</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="model_type" class="form-label fw-bold">Módulo</label>
                        <select class="form-select" name="model_type" id="model_type">
                            <option value="">Todos los módulos</option>
                            @foreach($modelTypes as $modelType)
                                <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                    {{ $modelType }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="search" class="form-label fw-bold">Buscar</label>
                        <input type="text" class="form-control" name="search" id="search" 
                               placeholder="Descripción..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label fw-bold">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" 
                               value="{{ request('fecha_inicio') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label fw-bold">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" 
                               value="{{ request('fecha_fin') }}">
                    </div>

                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumen de Estadísticas --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #667eea !important;">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fa-solid fa-list fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Total de Actividades</h6>
                    <h2 class="mb-0 fw-bold">{{ $activities->total() }}</h2>
                    <small class="text-muted">Registros</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Usuarios Activos</h6>
                    <h2 class="mb-0 fw-bold">{{ $users->count() }}</h2>
                    <small class="text-muted">Usuarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #17a2b8 !important;">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fa-solid fa-clock fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Última Actividad</h6>
                    <h2 class="mb-0 fw-bold">{{ $activities->first()?->created_at?->diffForHumans() ?? 'N/A' }}</h2>
                    <small class="text-muted">Hace</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Actividades --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fa-solid fa-history"></i> Listado de Actividades</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Módulo</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">#{{ $activity->id }}</span>
                            </td>
                            <td>
                                <i class="fa-solid fa-user text-primary me-1"></i>
                                <strong>{{ $activity->user?->name ?? 'Sistema' }}</strong>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'create' => 'success',
                                        'update' => 'warning',
                                        'delete' => 'danger',
                                        'login' => 'info',
                                        'logout' => 'secondary',
                                    ];
                                    $actionIcons = [
                                        'create' => 'fa-plus',
                                        'update' => 'fa-edit',
                                        'delete' => 'fa-trash',
                                        'login' => 'fa-sign-in-alt',
                                        'logout' => 'fa-sign-out-alt',
                                    ];
                                    $color = $actionColors[$activity->action] ?? 'primary';
                                    $icon = $actionIcons[$activity->action] ?? 'fa-cog';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    <i class="fa-solid {{ $icon }} me-1"></i>
                                    {{ ucfirst($activity->action) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($activity->description, 50) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $activity->model_type ? class_basename($activity->model_type) : 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small class="text-muted">
                                    <i class="fa-solid fa-calendar-alt me-1"></i>
                                    {{ $activity->created_at->format('d/m/Y') }}<br>
                                    <i class="fa-solid fa-clock me-1"></i>
                                    {{ $activity->created_at->format('H:i:s') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal-{{ $activity->id }}">
                                    <i class="fa-solid fa-eye"></i> Ver Detalles
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No se encontraron actividades con los filtros seleccionados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activities->hasPages())
        <div class="card-footer">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modales de Detalle (Fuera de la tabla para evitar parpadeo) --}}
@foreach($activities as $activity)
<div class="modal fade" id="detailModal-{{ $activity->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-info-circle"></i> Detalles de Actividad #{{ $activity->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Usuario:</strong> {{ $activity->user?->name ?? 'Sistema' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha:</strong> {{ $activity->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        @php
                            $actionColors = [
                                'create' => 'success',
                                'update' => 'warning',
                                'delete' => 'danger',
                                'login' => 'info',
                                'logout' => 'secondary',
                            ];
                            $color = $actionColors[$activity->action] ?? 'primary';
                        @endphp
                        <strong>Acción:</strong> <span class="badge bg-{{ $color }}">{{ ucfirst($activity->action) }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Módulo:</strong> {{ $activity->model_type ?? 'N/A' }}
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Descripción:</strong><br>
                    <p class="bg-light p-2 rounded">{{ $activity->description }}</p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Dirección IP:</strong> {{ $activity->ip_address }}
                    </div>
                    <div class="col-md-6">
                        <strong>ID Modelo:</strong> {{ $activity->model_id ?? 'N/A' }}
                    </div>
                </div>
                @if($activity->properties)
                <div class="mb-3">
                    <strong>Datos Adicionales:</strong>
                    <pre class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif
                <div class="mb-3">
                    <strong>User Agent:</strong><br>
                    <small class="text-muted">{{ $activity->user_agent }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
