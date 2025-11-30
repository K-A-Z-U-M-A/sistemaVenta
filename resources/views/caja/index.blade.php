@extends('layouts.app')

@section('title','Caja')

@push('css')
<style>
    .caja-card {
        border-left: 5px solid;
        transition: all 0.3s;
    }
    .caja-abierta {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
    }
    .caja-cerrada {
        border-left-color: #6c757d;
    }
    .monto-display {
        font-size: 2.5rem;
        font-weight: bold;
        color: #28a745;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Gestión de Caja</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Caja</li>
    </ol>

    @include('layouts.partials.alert')

    <!-- Estado de Caja Actual -->
    <div class="row mb-4">
        <div class="col-12">
            @if($cajaAbierta)
            <!-- Caja Abierta -->
            <div class="card caja-card caja-abierta shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fa-solid fa-cash-register"></i> Caja Abierta
                        <span class="badge bg-light text-success float-end">Activa</span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-muted">Monto Inicial</h6>
                            <p class="h4">{{ number_format($cajaAbierta->monto_inicial, 0, ',', '.') }} Gs</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Ingresos Efectivo</h6>
                            <p class="h4 text-success">{{ number_format($cajaAbierta->ventas()->where('forma_pago', 'efectivo')->where('estado', 1)->sum('total'), 0, ',', '.') }} Gs</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Ingresos Tarjeta</h6>
                            <p class="h4 text-info">{{ number_format($cajaAbierta->ventas()->where('forma_pago', 'tarjeta')->where('estado', 1)->sum('total'), 0, ',', '.') }} Gs</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Esperado</h6>
                            <p class="monto-display">
                                {{ number_format($cajaAbierta->monto_inicial + $cajaAbierta->ventas()->where('forma_pago', 'efectivo')->where('estado', 1)->sum('total'), 0, ',', '.') }} Gs
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        @php
                            $ventasCaja = $cajaAbierta->ventas()->where('estado', 1)->with('productos')->get();
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
                        <div class="col-md-6 text-center border-end">
                            <h5 class="text-muted fw-bold">SECTOR COMIDA</h5>
                            <h3 class="text-success">{{ number_format($gananciaComida, 0, ',', '.') }} Gs</h3>
                            @if($descuentoTragosTotal > 0)
                                <small class="text-success">(+{{ number_format($descuentoTragosTotal, 0, ',', '.') }} Gs de desc. tragos)</small>
                            @endif
                        </div>
                        <div class="col-md-6 text-center">
                            <h5 class="text-muted fw-bold">SECTOR TRAGOS</h5>
                            <h3 class="text-primary">{{ number_format($gananciaTragos, 0, ',', '.') }} Gs</h3>
                            @if($descuentoTragosTotal > 0)
                                <small class="text-danger">(-{{ number_format($descuentoTragosTotal, 0, ',', '.') }} Gs de desc.)</small>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Apertura:</strong> {{ $cajaAbierta->fecha_apertura->format('d/m/Y H:i') }}</p>
                            <p><strong>Cajero:</strong> {{ $cajaAbierta->user->name }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#cerrarCajaModal">
                                <i class="fa-solid fa-lock"></i> Cerrar Caja
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Cerrar Caja -->
            <div class="modal fade" id="cerrarCajaModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('caja.cerrar', $cajaAbierta) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Cerrar Caja</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>Total Esperado:</strong> 
                                    {{ number_format($cajaAbierta->monto_inicial + $cajaAbierta->ventas()->where('forma_pago', 'efectivo')->where('estado', 1)->sum('total'), 0, ',', '.') }} Gs
                                </div>
                                <div class="mb-3">
                                    <label for="monto_final" class="form-label">Monto Final Contado *</label>
                                    <input type="number" class="form-control form-control-lg" id="monto_final" name="monto_final" required step="0.01" min="0">
                                    <small class="text-muted">Ingresa el dinero que realmente hay en caja</small>
                                </div>
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas sobre el cierre..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @else
            <!-- No hay caja abierta -->
            <div class="card caja-card border-warning shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0"><i class="fa-solid fa-exclamation-triangle"></i> No hay caja abierta</h4>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-cash-register fa-5x text-muted mb-4"></i>
                    <h3>Abre la caja para comenzar a trabajar</h3>
                    <button type="button" class="btn btn-success btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#abrirCajaModal">
                        <i class="fa-solid fa-unlock"></i> Abrir Caja
                    </button>
                </div>
            </div>

            <!-- Modal Abrir Caja -->
            <div class="modal fade" id="abrirCajaModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('caja.abrir') }}" method="POST">
                            @csrf
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Abrir Caja</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="monto_inicial" class="form-label">Monto Inicial *</label>
                                    <input type="number" class="form-control form-control-lg" id="monto_inicial" name="monto_inicial" required step="0.01" min="0" value="0">
                                    <small class="text-muted">Dinero con el que inicias la caja</small>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle"></i> Al abrir la caja, todas las ventas se registrarán en esta sesión.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Abrir Caja</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Historial de Cajas Cerradas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-history"></i> Últimas 10 Cajas Cerradas
                    <a href="{{ route('caja.balance') }}" class="btn btn-sm btn-primary float-end">
                        <i class="fa-solid fa-chart-bar"></i> Ver Balance Completo
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha Apertura</th>
                                    <th>Fecha Cierre</th>
                                    <th>Monto Inicial</th>
                                    <th>Monto Final</th>
                                    <th>Ingresos</th>
                                    <th>Diferencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cajasCerradas as $caja)
                                <tr>
                                    <td>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</td>
                                    <td>{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ number_format($caja->monto_inicial, 0, ',', '.') }} Gs</td>
                                    <td>{{ number_format($caja->monto_final, 0, ',', '.') }} Gs</td>
                                    <td>{{ number_format($caja->ingresos_efectivo + $caja->ingresos_tarjeta, 0, ',', '.') }} Gs</td>
                                    <td>
                                        @if($caja->diferencia > 0)
                                            <span class="badge bg-success">+{{ number_format($caja->diferencia, 0, ',', '.') }} Gs</span>
                                        @elseif($caja->diferencia < 0)
                                            <span class="badge bg-danger">{{ number_format($caja->diferencia, 0, ',', '.') }} Gs</span>
                                        @else
                                            <span class="badge bg-secondary">0 Gs</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('caja.show', $caja) }}" class="btn btn-sm btn-info">
                                            <i class="fa-solid fa-eye"></i> Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay cajas cerradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
