@extends('layouts.app')

@section('title','Balance de Caja')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">💰 Balance de Caja</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
        <li class="breadcrumb-item active">Balance</li>
    </ol>

    <!-- Filtros Mejorados -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa-solid fa-filter"></i> Filtros de Fecha</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label fw-bold">Fecha Inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label fw-bold">Fecha Fin</label>
                    <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fa-solid fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('caja.balance') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen Mejorado -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fa-solid fa-arrow-trend-up fa-3x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Total Ingresos</h6>
                    <h2 class="text-success fw-bold mb-0">{{ number_format($totalIngresos, 0, ',', '.') }}</h2>
                    <small class="text-muted">Guaraníes</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fa-solid fa-arrow-trend-down fa-3x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Total Egresos</h6>
                    <h2 class="text-danger fw-bold mb-0">{{ number_format($totalEgresos, 0, ',', '.') }}</h2>
                    <small class="text-muted">Guaraníes</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid {{ $totalDiferencias >= 0 ? '#28a745' : '#dc3545' }} !important;">
                <div class="card-body text-center">
                    <div class="text-{{ $totalDiferencias >= 0 ? 'success' : 'danger' }} mb-2">
                        <i class="fa-solid fa-scale-balanced fa-3x"></i>
                    </div>
                    <h6 class="text-muted text-uppercase small mb-1">Diferencias Totales</h6>
                    <h2 class="text-{{ $totalDiferencias >= 0 ? 'success' : 'danger' }} fw-bold mb-0">
                        {{ $totalDiferencias >= 0 ? '+' : '' }}{{ number_format($totalDiferencias, 0, ',', '.') }}
                    </h2>
                    <small class="text-muted">Guaraníes</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Cajas Mejorada -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fa-solid fa-cash-register"></i> Detalle de Cajas ({{ $cajas->count() }} cajas)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center"><i class="fa-solid fa-user"></i> Cajero</th>
                            <th class="text-center"><i class="fa-solid fa-clock"></i> Apertura</th>
                            <th class="text-center"><i class="fa-solid fa-clock"></i> Cierre</th>
                            <th class="text-end">Inicial</th>
                            <th class="text-end">Final</th>
                            <th class="text-end">Efectivo</th>
                            <th class="text-end">Tarjeta</th>
                            <th class="text-end">Egresos</th>
                            <th class="text-center">Diferencia</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                        <tr>
                            <td class="text-center fw-bold">{{ $caja->user->name }}</td>
                            <td class="text-center">
                                <small>{{ $caja->fecha_apertura->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $caja->fecha_apertura->format('H:i') }}</small>
                            </td>
                            <td class="text-center">
                                @if($caja->fecha_cierre)
                                    <small>{{ $caja->fecha_cierre->format('d/m/Y') }}</small><br>
                                    <small class="text-muted">{{ $caja->fecha_cierre->format('H:i') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($caja->monto_inicial, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">{{ number_format($caja->monto_final ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end text-success">{{ number_format($caja->ingresos_efectivo, 0, ',', '.') }}</td>
                            <td class="text-end text-info">{{ number_format($caja->ingresos_tarjeta, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">{{ number_format($caja->egresos, 0, ',', '.') }}</td>
                            <td class="text-center">
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
                            <td class="text-center">
                                @if($caja->estado === 'abierta')
                                    <span class="badge bg-success"><i class="fa-solid fa-lock-open"></i> Abierta</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-lock"></i> Cerrada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($caja->estado === 'cerrada')
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detalle-{{ $caja->id }}" aria-expanded="false">
                                        <i class="fa-solid fa-chart-pie"></i> Ver Desglose
                                    </button>
                                @else
                                    <span class="text-muted small">En curso</span>
                                @endif
                            </td>
                        </tr>
                        @if($caja->estado === 'cerrada')
                        <tr class="collapse" id="detalle-{{ $caja->id }}">
                            <td colspan="11" class="p-0">
                                <div class="bg-light p-4">
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
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="card border-success">
                                                <div class="card-body text-center">
                                                    <i class="fa-solid fa-utensils text-success fa-2x mb-2"></i>
                                                    <h6 class="text-muted text-uppercase small mb-1">Sector Comida</h6>
                                                    <h4 class="text-success fw-bold mb-0">{{ number_format($gananciaComida, 0, ',', '.') }} Gs</h4>
                                                    <small class="text-muted">Incluye descuentos de tragos</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <i class="fa-solid fa-wine-glass text-primary fa-2x mb-2"></i>
                                                    <h6 class="text-muted text-uppercase small mb-1">Sector Tragos</h6>
                                                    <h4 class="text-primary fw-bold mb-0">{{ number_format($gananciaTragos, 0, ',', '.') }} Gs</h4>
                                                    <small class="text-muted">Neto después de descuentos</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <a href="{{ route('caja.show', $caja->id) }}" class="btn btn-primary">
                                            <i class="fa-solid fa-eye"></i> Ver Detalle Completo
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-4x mb-3 d-block"></i>
                                <h5>No hay cajas registradas en este período</h5>
                                <p>Ajusta los filtros de fecha o abre una nueva caja</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
