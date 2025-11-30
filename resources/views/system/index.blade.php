@extends('layouts.app')

@section('title','Mantenimiento del Sistema')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Mantenimiento del Sistema</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Sistema</li>
    </ol>

    <div class="row">
        <!-- Información del Servidor -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-server me-1"></i> Información del Servidor
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Versión de PHP
                            <span class="badge bg-primary rounded-pill">{{ $serverInfo['php_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Versión de Laravel
                            <span class="badge bg-primary rounded-pill">{{ $serverInfo['laravel_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Base de Datos
                            <span class="badge bg-success rounded-pill">{{ $serverInfo['db_driver'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nombre DB
                            <span class="badge bg-secondary rounded-pill">{{ $serverInfo['db_connection'] }}</span>
                        </li>
                        <li class="list-group-item">
                            <small class="text-muted">Sistema Operativo:</small><br>
                            {{ $serverInfo['server_os'] }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Acciones de Mantenimiento -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-tools me-1"></i> Acciones de Mantenimiento
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Estas acciones pueden afectar temporalmente el rendimiento mientras se regeneran los archivos.
                    </div>

                    <div class="d-grid gap-3">
                        <form action="{{ route('system.optimize') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 text-start">
                                <i class="fas fa-broom me-2"></i> Limpiar Todo (Optimize:clear)
                                <small class="d-block text-white-50">Borra caché de configuración, rutas y vistas.</small>
                            </button>
                        </form>

                        <form action="{{ route('system.cache-config') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 text-start">
                                <i class="fas fa-cogs me-2"></i> Cachear Configuración
                                <small class="d-block text-white-50">Mejora la velocidad de carga de configuración.</small>
                            </button>
                        </form>

                        <form action="{{ route('system.cache-route') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 text-start">
                                <i class="fas fa-route me-2"></i> Cachear Rutas
                                <small class="d-block text-white-50">Optimiza el registro de rutas.</small>
                            </button>
                        </form>
                        
                        <form action="{{ route('system.cache-view') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info text-white w-100 text-start">
                                <i class="fas fa-eye me-2"></i> Cachear Vistas
                                <small class="d-block text-white-50">Pre-compila las vistas Blade.</small>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
