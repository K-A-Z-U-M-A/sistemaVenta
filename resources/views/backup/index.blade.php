@extends('layouts.app')

@section('title','Backups')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <form action="{{ route('backup.create') }}" method="POST" class="mb-3">
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
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ str_replace('.', '-', $backup['file_name']) }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>

                                <!-- Modal Eliminar -->
                                <div class="modal fade" id="deleteModal-{{ str_replace('.', '-', $backup['file_name']) }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de eliminar el backup <strong>{{ $backup['file_name'] }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('backup.destroy', $backup['file_name']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
