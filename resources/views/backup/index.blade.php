@extends('layouts.app')

@section('title','Backups')

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/backups.js') }}"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Copias de Seguridad</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Backups</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-database me-1"></i>
            Gestión de Base de Datos
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Aquí puedes generar copias de seguridad de la base de datos PostgreSQL. Asegúrate de que <code>pg_dump</code> esté accesible en el sistema.
            </div>

            @can('crear-backup')
            <form action="{{ route('backup.create') }}" method="POST" class="mb-3" id="createBackupForm">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Generar Nuevo Backup
                </button>
            </form>
            @endcan

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ $backup['file_size'] }}</td>
                            <td>{{ $backup['last_modified'] }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('backup.download', $backup['file_name']) }}" class="btn btn-success btn-sm" title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    
                                    @can('eliminar-backup')
                                    <button type="button" 
                                            class="btn btn-danger btn-sm delete-backup-btn" 
                                            data-filename="{{ $backup['file_name'] }}"
                                            data-url="{{ route('backup.destroy', $backup['file_name']) }}"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay backups disponibles.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
