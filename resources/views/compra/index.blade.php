@extends('layouts.app')

@section('title','compras')
@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush
@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .row-not-space {
        width: 110px;
    }
    
    /* Estilos para el panel de filtros */
    .card-header {
        transition: all 0.3s ease;
    }
    
    .card-header:hover {
        opacity: 0.9;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        margin-right: 0.25rem;
    }
</style>
@endpush

@section('content')

@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Compras</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Compras</li>
    </ol>

    @can('crear-compra')
    <div class="mb-4">
        <a href="{{route('compras.create')}}">
            <button type="button" class="btn btn-primary">Añadir nuevo registro</button>
        </a>
    </div>
    @endcan

    {{-- Panel de Filtros Avanzados --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtrosCompras">
            <div>
                <i class="fas fa-filter me-2"></i>
                <strong>Filtros de Búsqueda</strong>
            </div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="collapse show" id="filtrosCompras">
            <div class="card-body">
                <form method="GET" action="{{ route('compras.index') }}">
                    <div class="row g-3">
                        {{-- Rango de Fechas --}}
                        <div class="col-md-4">
                            <label for="fecha_desde" class="form-label">
                                <i class="fa-solid fa-calendar-day"></i> Fecha Desde
                            </label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                                   value="{{ request('fecha_desde') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="fecha_hasta" class="form-label">
                                <i class="fa-solid fa-calendar-day"></i> Fecha Hasta
                            </label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                                   value="{{ request('fecha_hasta') }}">
                        </div>

                        {{-- Proveedor --}}
                        <div class="col-md-4">
                            <label for="proveedor_id" class="form-label">
                                <i class="fa-solid fa-truck"></i> Proveedor
                            </label>
                            <select class="form-select" id="proveedor_id" name="proveedor_id">
                                <option value="">Todos los proveedores</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ request('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->persona->razon_social }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>

                    {{-- Resumen de filtros activos --}}
                    @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'proveedor_id']))
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fa-solid fa-info-circle"></i> 
                            <strong>Filtros activos:</strong>
                            @if(request('fecha_desde'))
                                <span class="badge bg-info">Desde: {{ \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') }}</span>
                            @endif
                            @if(request('fecha_hasta'))
                                <span class="badge bg-info">Hasta: {{ \Carbon\Carbon::parse(request('fecha_hasta'))->format('d/m/Y') }}</span>
                            @endif
                            @if(request('proveedor_id'))
                                <span class="badge bg-primary">Proveedor: {{ $proveedores->find(request('proveedor_id'))->persona->razon_social ?? 'N/A' }}</span>
                            @endif
                        </small>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla compras
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>Comprobante</th>
                        <th>Proveedor</th>
                        <th>Fecha y hora</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $item)
                    <tr>
                        <td>
                            <p class="fw-semibold mb-1">{{$item->comprobante->tipo_comprobante}}</p>
                            <p class="text-muted mb-0">{{$item->numero_comprobante}}</p>
                        </td>
                        <td>
                            @if($item->proveedore)
                                <p class="fw-semibold mb-1">{{ ucfirst($item->proveedore->persona->tipo_persona) }}</p>
                                <p class="text-muted mb-0">{{$item->proveedore->persona->razon_social}}</p>
                            @else
                                <p class="fw-semibold mb-1">Local</p>
                                <p class="text-muted mb-0">{{ $item->nombre_proveedor_local ?? 'Sin Proveedor' }}</p>
                            @endif
                        </td>
                        <td>
                            <div class="row-not-space">
                                <p class="fw-semibold mb-1"><span class="m-1"><i class="fa-solid fa-calendar-days"></i></span>{{\Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y')}}</p>
                                <p class="fw-semibold mb-0"><span class="m-1"><i class="fa-solid fa-clock"></i></span>{{\Carbon\Carbon::parse($item->fecha_hora)->format('H:i')}}</p>
                            </div>
                        </td>
                        <td>
                            {{$item->total}}
                        </td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">

                                @can('mostrar-compra')
                                <form action="{{route('compras.show', ['compra'=>$item]) }}" method="get">
                                    <button type="submit" class="btn btn-success">
                                        Ver
                                    </button>
                                </form>
                                @endcan

                                <a href="{{ route('compras.imprimir', $item) }}" class="btn btn-warning" target="_blank" title="Imprimir">
                                    <i class="fa-solid fa-print"></i>
                                </a>

                                @can('eliminar-compra')
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}">Eliminar</button>
                                @endcan

                            </div>
                        </td>

                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modales de confirmación -->
    @foreach ($compras as $item)
    <div class="modal fade" id="confirmModal-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Mensaje de confirmación</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres eliminar el registro?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <form action="{{ route('compras.destroy',['compra'=>$item->id]) }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger">Confirmar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush