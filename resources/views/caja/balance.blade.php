@extends('layouts.app')

@section('title','Balance de Caja')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Balance de Caja</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
        <li class="breadcrumb-item active">Balance</li>
    </ol>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Ingresos</h6>
                    <h2 class="text-success">{{ number_format($totalIngresos, 0, ',', '.') }} Gs</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Egresos</h6>
                    <h2 class="text-danger">{{ number_format($totalEgresos, 0, ',', '.') }} Gs</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-{{ $totalDiferencias >= 0 ? 'success' : 'danger' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted">Diferencias Totales</h6>
                    <h2 class="text-{{ $totalDiferencias >= 0 ? 'success' : 'danger' }}">
                        {{ $totalDiferencias >= 0 ? '+' : '' }}{{ number_format($totalDiferencias, 0, ',', '.') }} Gs
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Cajas -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-table"></i> Detalle de Cajas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Cajero</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Inicial</th>
                            <th>Final</th>
                            <th>Efectivo</th>
                            <th>Tarjeta</th>
                            <th>Egresos</th>
                            <th>Diferencia</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cajas as $caja)
                        <tr>
                            <td>{{ $caja->user->name }}</td>
                            <td>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</td>
                            <td>{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ number_format($caja->monto_inicial, 0, ',', '.') }}</td>
                            <td>{{ number_format($caja->monto_final ?? 0, 0, ',', '.') }}</td>
                            <td class="text-success">{{ number_format($caja->ingresos_efectivo, 0, ',', '.') }}</td>
                            <td class="text-info">{{ number_format($caja->ingresos_tarjeta, 0, ',', '.') }}</td>
                            <td class="text-danger">{{ number_format($caja->egresos, 0, ',', '.') }}</td>
                            <td>
                                @if($caja->diferencia !== null)
                                    @if($caja->diferencia > 0)
                                        <span class="badge bg-success">+{{ number_format($caja->diferencia, 0, ',', '.') }}</span>
                                    @elseif($caja->diferencia < 0)
                                        <span class="badge bg-danger">{{ number_format($caja->diferencia, 0, ',', '.') }}</span>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                            <td>
                                @if($caja->estado === 'abierta')
                                    <span class="badge bg-success">Abierta</span>
                                @else
                                    <span class="badge bg-secondary">Cerrada</span>
                                @endif
                            </td>
                        </tr>
                        @if($caja->estado === 'cerrada')
                        <tr class="table-light">
                            <td colspan="10">
                                @php
                                    // Calcular desglose para esta caja
                                    $ventasCaja = \App\Models\Venta::where('caja_id', $caja->id)->where('estado', 1)->with('productos')->get();
                                    $ingresoComida = 0;
                                    $ingresoTragos = 0;
                                    $descuentoTragosTotal = 0;

                                    foreach($ventasCaja as $v) {
                                        foreach($v->productos as $prod) {
                                            $subtotal = ($prod->pivot->cantidad * $prod->pivot->precio_venta) - $prod->pivot->descuento;
                                            if($prod->tipo_producto === 'trago') {
                                                $descuentoTragoLine = $prod->pivot->cantidad * $prod->pivot->descuento_trago;
                                                $ingresoTragos += $subtotal; // No restar descuento aquí
                                                $descuentoTragosTotal += $descuentoTragoLine;
                                            } else {
                                                $ingresoComida += $subtotal;
                                            }
                                        }
                                    }
                                    
                                    $gananciaComida = $ingresoComida + $descuentoTragosTotal;
                                    $gananciaTragos = $ingresoTragos - $descuentoTragosTotal;
                                @endphp
                                <div class="row text-center py-2">
                                    <div class="col-md-6 border-end">
                                        <small class="text-muted fw-bold">SECTOR COMIDA</small><br>
                                        <span class="text-success fw-bold">{{ number_format($gananciaComida, 0, ',', '.') }} Gs</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted fw-bold">SECTOR TRAGOS</small><br>
                                        <span class="text-primary fw-bold">{{ number_format($gananciaTragos, 0, ',', '.') }} Gs</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
