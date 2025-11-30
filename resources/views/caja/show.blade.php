@extends('layouts.app')

@section('title','Detalle de Caja')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">💰 Detalle de Caja</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
        <li class="breadcrumb-item active">Detalle</li>
    </ol>

    <!-- Información General Mejorada -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fa-solid fa-user fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Cajero</h6>
                    <h5 class="mb-0 fw-bold">{{ $caja->user->name }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fa-solid fa-door-open fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Apertura</h6>
                    <h5 class="mb-0 fw-bold">{{ $caja->fecha_apertura->format('d/m/Y') }}</h5>
                    <small class="text-muted">{{ $caja->fecha_apertura->format('H:i') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fa-solid fa-door-closed fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Cierre</h6>
                    @if($caja->fecha_cierre)
                        <h5 class="mb-0 fw-bold">{{ $caja->fecha_cierre->format('d/m/Y') }}</h5>
                        <small class="text-muted">{{ $caja->fecha_cierre->format('H:i') }}</small>
                    @else
                        <h5 class="mb-0 fw-bold text-success">En curso</h5>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2" style="color: {{ $caja->estado == 'abierta' ? '#28a745' : '#6c757d' }};">
                        <i class="fa-solid fa-circle-check fa-2x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Estado</h6>
                    @if($caja->estado == 'abierta')
                        <span class="badge bg-success fs-6">Abierta</span>
                    @else
                        <span class="badge bg-secondary fs-6">Cerrada</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Financiero -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="mb-0"><i class="fa-solid fa-wallet"></i> Resumen Financiero</h5>
        </div>
        <div class="card-body">
            <div class="row text-center g-3">
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded">
                        <p class="text-muted mb-1 small">Monto Inicial</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($caja->monto_inicial, 0, ',', '.') }}</h4>
                        <small class="text-muted">Gs</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-success bg-opacity-10 rounded">
                        <p class="text-success mb-1 small fw-bold">Ingresos Efectivo</p>
                        <h4 class="mb-0 fw-bold text-success">{{ number_format($caja->ingresos_efectivo, 0, ',', '.') }}</h4>
                        <small class="text-muted">Gs</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-info bg-opacity-10 rounded">
                        <p class="text-info mb-1 small fw-bold">Ingresos Tarjeta</p>
                        <h4 class="mb-0 fw-bold text-info">{{ number_format($caja->ingresos_tarjeta, 0, ',', '.') }}</h4>
                        <small class="text-muted">Gs</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                        <p class="text-primary mb-1 small fw-bold">Monto Final</p>
                        <h4 class="mb-0 fw-bold text-primary">{{ number_format($caja->monto_final, 0, ',', '.') }}</h4>
                        <small class="text-muted">Gs</small>
                    </div>
                </div>
            </div>
            
            @if($caja->diferencia != 0)
            <div class="alert {{ $caja->diferencia > 0 ? 'alert-success' : 'alert-danger' }} mt-3 mb-0">
                <strong><i class="fa-solid fa-info-circle"></i> Diferencia:</strong> 
                {{ number_format($caja->diferencia, 0, ',', '.') }} Gs
                @if($caja->diferencia > 0)
                    (Sobrante)
                @else
                    (Faltante)
                @endif
            </div>
            @endif
            
            @if($caja->observaciones)
            <div class="alert alert-warning mt-2 mb-0">
                <strong><i class="fa-solid fa-comment"></i> Observaciones:</strong> {{ $caja->observaciones }}
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
                    $ingresoTragos += $subtotal;
                    $descuentoTragosTotal += $descuentoTragoLine;
                } else {
                    $ingresoComida += $subtotal;
                }
            }
        }
        
        $gananciaComida = $ingresoComida + $descuentoTragosTotal;
        $gananciaTragos = $ingresoTragos - $descuentoTragosTotal;
    @endphp

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fa-solid fa-utensils fa-3x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-2">Sector Comida</h6>
                    <h2 class="text-success fw-bold mb-0">{{ number_format($gananciaComida, 0, ',', '.') }}</h2>
                    <p class="text-muted mb-0">Guaraníes</p>
                    <small class="text-muted">Incluye descuentos de tragos transferidos</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #667eea !important;">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fa-solid fa-wine-glass fa-3x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-2">Sector Tragos</h6>
                    <h2 class="text-primary fw-bold mb-0">{{ number_format($gananciaTragos, 0, ',', '.') }}</h2>
                    <p class="text-muted mb-0">Guaraníes</p>
                    <small class="text-muted">Neto después de cubrir descuentos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas Mejorada -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fa-solid fa-receipt"></i> Ventas de esta sesión ({{ $ventas->count() }} ventas)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center"><i class="fa-solid fa-clock"></i> Hora</th>
                            <th><i class="fa-solid fa-file-invoice"></i> Comprobante</th>
                            <th><i class="fa-solid fa-user"></i> Cliente</th>
                            <th class="text-end">Comida</th>
                            <th class="text-end">Trago</th>
                            <th class="text-end">Comida + Desc</th>
                            <th class="text-end"><strong>Total</strong></th>
                            <th class="text-center">Pago</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
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
                            <td class="text-center">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }}</td>
                            <td><span class="badge bg-secondary">{{ $venta->numero_comprobante }}</span></td>
                            <td>{{ $venta->cliente->persona->razon_social ?? ($venta->nombre_pedido ?? 'N/A') }}</td>
                            <td class="text-end">{{ number_format($rawFood, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($rawDrink, 0, ',', '.') }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($foodProfit, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold fs-6">{{ number_format($venta->total, 0, ',', '.') }} Gs</td>
                            <td class="text-center">
                                <span class="badge {{ $venta->forma_pago == 'efectivo' ? 'bg-success' : 'bg-info' }}">
                                    {{ ucfirst($venta->forma_pago) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-inbox fa-3x mb-2 d-block"></i>
                                No hay ventas registradas en esta caja
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="text-center mb-4">
        <a href="{{ route('caja.index') }}" class="btn btn-secondary btn-lg">
            <i class="fa-solid fa-arrow-left"></i> Volver a Caja
        </a>
    </div>
</div>
@endsection
