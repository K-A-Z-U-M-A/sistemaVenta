@extends('layouts.app')

@section('title','Detalle de Caja')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Detalle de Caja</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
        <li class="breadcrumb-item active">Detalle</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-info-circle"></i> Información General
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Cajero:</strong> {{ $caja->user->name }}
                </div>
                <div class="col-md-3">
                    <strong>Apertura:</strong> {{ $caja->fecha_apertura->format('d/m/Y H:i') }}
                </div>
                <div class="col-md-3">
                    <strong>Cierre:</strong> {{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'Abierta' }}
                </div>
                <div class="col-md-3">
                    <strong>Estado:</strong> 
                    @if($caja->estado == 'abierta')
                        <span class="badge bg-success">Abierta</span>
                    @else
                        <span class="badge bg-secondary">Cerrada</span>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row text-center">
                <div class="col-md-3">
                    <h6 class="text-muted">Monto Inicial</h6>
                    <h4>{{ number_format($caja->monto_inicial, 0, ',', '.') }} Gs</h4>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Ingresos Efectivo</h6>
                    <h4 class="text-success">{{ number_format($caja->ingresos_efectivo, 0, ',', '.') }} Gs</h4>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Ingresos Tarjeta</h6>
                    <h4 class="text-info">{{ number_format($caja->ingresos_tarjeta, 0, ',', '.') }} Gs</h4>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Monto Final</h6>
                    <h4>{{ number_format($caja->monto_final, 0, ',', '.') }} Gs</h4>
                </div>
            </div>
            
            @if($caja->observaciones)
            <div class="alert alert-warning mt-3">
                <strong>Observaciones:</strong> {{ $caja->observaciones }}
            </div>
            @endif
        </div>
    </div>

    <!-- Desglose de Ganancias -->
    @php
        $ingresoComida = 0;
        $ingresoTragos = 0;
        $descuentoTragosTotal = 0;

        foreach($ventas as $v) {
            foreach($v->productos as $prod) {
                $subtotal = ($prod->pivot->cantidad * $prod->pivot->precio_venta) - $prod->pivot->descuento;
                if($prod->tipo_producto === 'trago') {
                    $descuentoTragoLine = $prod->pivot->cantidad * $prod->pivot->descuento_trago;
                    $ingresoTragos += $subtotal; // No restar aquí
                    $descuentoTragosTotal += $descuentoTragoLine;
                } else {
                    $ingresoComida += $subtotal;
                }
            }
        }
        
        $gananciaComida = $ingresoComida + $descuentoTragosTotal;
        $gananciaTragos = $ingresoTragos - $descuentoTragosTotal;
    @endphp

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <i class="fa-solid fa-chart-pie"></i> Distribución de Ingresos (Sector Comida vs Tragos)
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6 border-end">
                    <h5 class="text-muted">SECTOR COMIDA</h5>
                    <h2 class="text-success">{{ number_format($gananciaComida, 0, ',', '.') }} Gs</h2>
                    <small class="text-muted">Incluye descuentos de tragos transferidos</small>
                </div>
                <div class="col-md-6">
                    <h5 class="text-muted">SECTOR TRAGOS</h5>
                    <h2 class="text-primary">{{ number_format($gananciaTragos, 0, ',', '.') }} Gs</h2>
                    <small class="text-muted">Neto después de cubrir descuentos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fa-solid fa-table"></i> Ventas de esta sesión
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Hora</th>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Comida</th>
                            <th>Trago</th>
                            <th>Comida + Desc</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventas as $venta)
                            @php
                                $rawFood = 0;
                                $rawDrink = 0;
                                $drinkDiscount = 0;

                                foreach($venta->productos as $prod) {
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
                            <td>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }}</td>
                            <td>{{ $venta->numero_comprobante }}</td>
                            <td>{{ $venta->cliente->persona->razon_social ?? ($venta->nombre_pedido ?? 'N/A') }}</td>
                            <td>{{ number_format($rawFood, 0, ',', '.') }}</td>
                            <td>{{ number_format($rawDrink, 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">{{ number_format($foodProfit, 0, ',', '.') }}</td>
                            <td class="fw-bold">{{ number_format($venta->total, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($venta->forma_pago) }}</td>
                            <td>
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-info" title="Ver Detalle">
                                    <i class="fa-solid fa-eye"></i>
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
