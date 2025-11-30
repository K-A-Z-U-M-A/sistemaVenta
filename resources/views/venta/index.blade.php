@extends('layouts.app')

@section('title','ventas')

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
    #filtrosAvanzados .card-header {
        transition: all 0.3s ease;
    }
    
    #filtrosAvanzados .card-header:hover {
        background-color: #0056b3 !important;
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
    <h1 class="mt-4 text-center">Ventas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Ventas</li>
    </ol>

    @can('crear-venta')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{route('ventas.create')}}">
            <button type="button" class="btn btn-primary">Añadir nuevo registro</button>
        </a>
        
        @if(request()->has('ver_todo'))
            <a href="{{route('ventas.index')}}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-calendar-day"></i> Ver solo hoy
            </a>
        @else
            <a href="{{route('ventas.index', ['ver_todo' => 1])}}" class="btn btn-outline-primary">
                <i class="fa-solid fa-history"></i> Ver historial completo
            </a>
        @endif
    </div>
    @endcan

    {{-- Panel de Filtros Avanzados --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados">
            <div>
                <i class="fas fa-filter me-2"></i>
                <strong>Filtros de Búsqueda</strong>
            </div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="collapse show" id="filtrosAvanzados">
            <div class="card-body">
                <form method="GET" action="{{ route('ventas.index') }}" id="formFiltros">
                    <div class="row g-3">
                        {{-- Rango de Fechas --}}
                        <div class="col-md-3">
                            <label for="fecha_desde" class="form-label">
                                <i class="fa-solid fa-calendar-day"></i> Fecha Desde
                            </label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                                   value="{{ request('fecha_desde') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="fecha_hasta" class="form-label">
                                <i class="fa-solid fa-calendar-day"></i> Fecha Hasta
                            </label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                                   value="{{ request('fecha_hasta') }}">
                        </div>

                        {{-- Estado del Pedido --}}
                        <div class="col-md-3">
                            <label for="estado_pedido" class="form-label">
                                <i class="fa-solid fa-list-check"></i> Estado
                            </label>
                            <select class="form-select" id="estado_pedido" name="estado_pedido">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" {{ request('estado_pedido') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="preparacion" {{ request('estado_pedido') == 'preparacion' ? 'selected' : '' }}>En Preparación</option>
                                <option value="completado" {{ request('estado_pedido') == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="entregado" {{ request('estado_pedido') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="cancelado" {{ request('estado_pedido') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        {{-- Forma de Pago --}}
                        <div class="col-md-3">
                            <label for="forma_pago" class="form-label">
                                <i class="fa-solid fa-credit-card"></i> Forma de Pago
                            </label>
                            <select class="form-select" id="forma_pago" name="forma_pago">
                                <option value="">Todas las formas</option>
                                <option value="efectivo" {{ request('forma_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ request('forma_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ request('forma_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="mixto" {{ request('forma_pago') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                            </select>
                        </div>

                        {{-- Vendedor --}}
                        <div class="col-md-4">
                            <label for="vendedor_id" class="form-label">
                                <i class="fa-solid fa-user-tie"></i> Vendedor
                            </label>
                            <select class="form-select" id="vendedor_id" name="vendedor_id">
                                <option value="">Todos los vendedores</option>
                                @foreach($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Búsqueda de Cliente --}}
                        <div class="col-md-4">
                            <label for="cliente_buscar" class="form-label">
                                <i class="fa-solid fa-magnifying-glass"></i> Buscar Cliente/Pedido
                            </label>
                            <input type="text" class="form-control" id="cliente_buscar" name="cliente_buscar" 
                                   placeholder="Nombre del cliente o pedido..." 
                                   value="{{ request('cliente_buscar') }}">
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa-solid fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('ventas.index') }}" class="btn btn-secondary flex-fill">
                                <i class="fa-solid fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>

                    {{-- Resumen de filtros activos --}}
                    @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'estado_pedido', 'forma_pago', 'vendedor_id', 'cliente_buscar']))
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
                            @if(request('estado_pedido'))
                                <span class="badge bg-warning text-dark">Estado: {{ ucfirst(request('estado_pedido')) }}</span>
                            @endif
                            @if(request('forma_pago'))
                                <span class="badge bg-success">Pago: {{ ucfirst(request('forma_pago')) }}</span>
                            @endif
                            @if(request('vendedor_id'))
                                <span class="badge bg-primary">Vendedor: {{ $vendedores->find(request('vendedor_id'))->name ?? 'N/A' }}</span>
                            @endif
                            @if(request('cliente_buscar'))
                                <span class="badge bg-secondary">Cliente: "{{ request('cliente_buscar') }}"</span>
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
            @if(request()->has('ver_todo'))
                Historial completo de ventas
            @else
                Ventas del día - {{ \Carbon\Carbon::today()->format('d/m/Y') }}
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>Imprimir</th>
                        <th>Cliente</th>
                        <th>Fecha y hora</th>
                        <th>Estado</th>
                        <th>Vendedor</th>
                        <th>Comida</th>
                        <th>Trago</th>
                        <th>Comida + Desc</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventas as $item)
                    @php
                        $rawFood = 0;
                        $rawDrink = 0;
                        $drinkDiscount = 0;

                        foreach($item->productos as $prod) {
                            $subtotal = ($prod->pivot->cantidad * $prod->pivot->precio_venta) - $prod->pivot->descuento;
                            if($prod->tipo_producto === 'trago') {
                                $rawDrink += $subtotal;
                                $drinkDiscount += ($prod->pivot->cantidad * $prod->pivot->descuento_trago);
                            } else {
                                $rawFood += $subtotal;
                            }
                        }
                        $foodProfit = $rawFood + $drinkDiscount;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('ventas.imprimir-ticket', $item) }}" class="btn btn-sm btn-primary" target="_blank" title="Imprimir Ticket">
                                <i class="fa-solid fa-print"></i> Imprimir
                            </a>
                        </td>
                        <td>
                            @if($item->cliente && $item->cliente->persona)
                                <p class="fw-semibold mb-1">{{ ucfirst($item->cliente->persona->tipo_persona) }}</p>
                                <p class="text-muted mb-0">{{$item->cliente->persona->razon_social}}</p>
                            @elseif($item->nombre_pedido)
                                <p class="fw-semibold mb-1">Pedido</p>
                                <p class="text-muted mb-0">{{$item->nombre_pedido}}</p>
                            @else
                                <p class="text-muted mb-0">Sin identificar</p>
                            @endif
                        </td>
                        <td>
                            <div class="row-not-space">
                                <p class="fw-semibold mb-1"><span class="m-1"><i class="fa-solid fa-calendar-days"></i></span>{{\Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y')}}</p>
                                <p class="fw-semibold mb-0"><span class="m-1"><i class="fa-solid fa-clock"></i></span>{{\Carbon\Carbon::parse($item->fecha_hora)->format('H:i')}}</p>
                            </div>
                        </td>
                        <td>
                            @php
                                $estado = $item->estado_pedido ?? 'pendiente';
                                $badgeClass = match($estado) {
                                    'pendiente' => 'bg-warning text-dark',
                                    'preparacion' => 'bg-info text-white',
                                    'completado' => 'bg-success',
                                    'entregado' => 'bg-primary',
                                    'cancelado' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $badgeClass }} px-3 py-2">
                                {{ ucfirst($estado) }}
                            </span>
                        </td>
                        <td>
                            {{$item->user->name ?? 'Usuario Desconocido'}}
                        </td>
                        <td>
                            {{ number_format($rawFood, 0, ',', '.') }}
                        </td>
                        <td>
                            {{ number_format($rawDrink, 0, ',', '.') }}
                        </td>
                        <td class="text-success fw-bold">
                            {{ number_format($foodProfit, 0, ',', '.') }}
                        </td>
                        <td class="fw-bold">
                            {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="btn-group-vertical gap-1" role="group">
                                @can('mostrar-venta')
                                <a href="{{route('ventas.show', ['venta'=>$item])}}" class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                                @endcan

                                @can('crear-venta')
                                <a href="{{route('ventas.edit', ['venta'=>$item])}}" class="btn btn-sm btn-warning">
                                    <i class="fa-solid fa-edit"></i> Editar
                                </a>
                                @endcan

                                @if($item->estado_pedido !== 'completado' && $item->estado_pedido !== 'cancelado')
                                    @can('crear-venta')
                                    <form action="{{route('ventas.cambiar-estado', ['venta'=>$item])}}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado_pedido" value="completado">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            <i class="fa-solid fa-check-circle"></i> Completar
                                        </button>
                                    </form>
                                    @endcan
                                @endif

                                @if($item->estado_pedido !== 'cancelado')
                                    @can('eliminar-venta')
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}">
                                        <i class="fa-solid fa-ban"></i> Anular
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Modal de confirmación-->
                    <div class="modal fade" id="confirmModal-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Mensaje de confirmación</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Seguro que quieres anular esta venta? Se devolverá el stock de los productos.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <form action="{{ route('ventas.destroy',['venta'=>$item->id]) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script>
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki
    window.addEventListener('DOMContentLoaded', event => {
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {})
    });
</script>
@endpush