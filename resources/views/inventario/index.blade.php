@extends('layouts.app')

@section('title','Inventario')

@push('css')
<style>
    .stock-alert {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .producto-row:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Control de Inventario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Inventario</li>
    </ol>

    <!-- Resumen de Inventario -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Productos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProductos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Sin Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productosSinStock }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stock Bajo (≤10)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productosStockBajo }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inventario.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="tipo" class="form-label">Tipo de Producto</label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="comida" {{ request('tipo') == 'comida' ? 'selected' : '' }}>Comida</option>
                        <option value="bebida" {{ request('tipo') == 'bebida' ? 'selected' : '' }}>Bebida</option>
                        <option value="trago" {{ request('tipo') == 'trago' ? 'selected' : '' }}>Trago</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">Alertas</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="stock_bajo" value="1" id="stock_bajo" {{ request('stock_bajo') ? 'checked' : '' }}>
                        <label class="form-check-label" for="stock_bajo">
                            Stock Bajo
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="sin_stock" value="1" id="sin_stock" {{ request('sin_stock') ? 'checked' : '' }}>
                        <label class="form-check-label" for="sin_stock">
                            Sin Stock
                        </label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('inventario.index') }}" class="btn btn-secondary w-100">
                        <i class="fa-solid fa-refresh"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-warehouse me-1"></i>
            Listado de Productos
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Stock</th>
                            <th>Precio Venta</th>

                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr class="producto-row">
                            <td><code>{{ $producto->codigo }}</code></td>
                            <td>
                                <strong>{{ $producto->nombre }}</strong>
                                @if($producto->marca && $producto->marca->caracteristica)
                                <br><small class="text-muted">{{ $producto->marca->caracteristica->nombre }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($producto->tipo_producto ?? 'N/A') }}</span>
                            </td>
                            <td>
                                @if($producto->stock == 0)
                                    <span class="badge bg-danger stock-alert">SIN STOCK</span>
                                @elseif($producto->stock <= 10)
                                    <span class="badge bg-warning text-dark stock-alert">{{ $producto->stock }}</span>
                                @else
                                    <span class="badge bg-success">{{ $producto->stock }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($producto->precio_venta, 0, ',', '.') }} Gs</td>

                            <td>
                                @if($producto->estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
