@extends('layouts.app')

@section('title','Editar Venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Editar Venta</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index')}}">Ventas</a></li>
        <li class="breadcrumb-item active">Editar Venta</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            Datos de la venta #{{ $venta->numero_comprobante }}
        </div>
        <div class="card-body">
            <form action="{{ route('ventas.update', $venta) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Cliente -->
                    <div class="col-md-6">
                        <label for="cliente_id" class="form-label">Cliente:</label>
                        <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona">
                            <option value="">-- Sin cliente --</option>
                            @foreach ($clientes as $item)
                            <option value="{{$item->id}}" {{ old('cliente_id', $venta->cliente_id) == $item->id ? 'selected' : '' }}>{{$item->persona->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nombre Pedido -->
                    <div class="col-md-6">
                        <label for="nombre_pedido" class="form-label">Nombre del Pedido:</label>
                        <input type="text" name="nombre_pedido" id="nombre_pedido" class="form-control" value="{{ old('nombre_pedido', $venta->nombre_pedido) }}">
                    </div>

                    <!-- Mesa -->
                    <div class="col-md-6">
                        <label for="numero_mesa" class="form-label">Mesa:</label>
                        <input type="text" name="numero_mesa" id="numero_mesa" class="form-control" value="{{ old('numero_mesa', $venta->numero_mesa) }}">
                    </div>

                    <!-- Estado Pedido -->
                    <div class="col-md-6">
                        <label for="estado_pedido" class="form-label">Estado del Pedido:</label>
                        <select name="estado_pedido" id="estado_pedido" class="form-select">
                            <option value="pendiente" {{ $venta->estado_pedido == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="preparacion" {{ $venta->estado_pedido == 'preparacion' ? 'selected' : '' }}>En Preparación</option>
                            <option value="completado" {{ $venta->estado_pedido == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="entregado" {{ $venta->estado_pedido == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="cancelado" {{ $venta->estado_pedido == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <!-- Notas -->
                    <div class="col-12">
                        <label for="notas" class="form-label">Notas:</label>
                        <textarea name="notas" id="notas" class="form-control" rows="3">{{ old('notas', $venta->notas) }}</textarea>
                    </div>

                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary">Actualizar Venta</button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de productos (Solo lectura) -->
    <div class="card mb-4">
        <div class="card-header">
            Productos (Para modificar productos, anule la venta y cree una nueva)
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->productos as $item)
                    <tr>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ $item->pivot->cantidad }}</td>
                        <td>{{ number_format($item->pivot->precio_venta, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->pivot->cantidad * $item->pivot->precio_venta, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush
