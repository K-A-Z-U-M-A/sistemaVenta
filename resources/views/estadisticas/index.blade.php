@extends('layouts.app')

@section('title','Estadísticas')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    @media print {
        /* Ocultar elementos no necesarios */
        .no-print, 
        .sb-nav-fixed #layoutSidenav_nav, 
        .sb-topnav,
        .breadcrumb,
        button,
        .btn {
            display: none !important;
        }
        
        /* Reset del layout */
        .sb-nav-fixed #layoutSidenav_content {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }
        
        body {
            background: white !important;
        }
        
        .container-fluid {
            padding: 0 !important;
            max-width: 100% !important;
        }
        
        /* Encabezado del reporte */
        h1 {
            font-size: 28px !important;
            text-align: center !important;
            color: #2c3e50 !important;
            margin: 20px 0 10px 0 !important;
            padding-bottom: 10px !important;
            border-bottom: 3px solid #3498db !important;
        }
        
        /* Agregar encabezado con información */
        h1::before {
            content: "REPORTE DE ESTADÍSTICAS\A";
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 10px;
        }
        
        h1::after {
            content: "Generado el " attr(data-date);
            display: block;
            font-size: 12px;
            font-weight: normal;
            color: #7f8c8d;
            margin-top: 10px;
        }
        
        /* Cards de resumen */
        .stat-card {
            border: 2px solid #ecf0f1 !important;
            border-radius: 8px !important;
            background: #f8f9fa !important;
            break-inside: avoid;
            page-break-inside: avoid;
            margin-bottom: 15px !important;
            box-shadow: none !important;
            transform: none !important;
        }
        
        .stat-card .card-body {
            padding: 15px !important;
        }
        
        .stat-card h6 {
            font-size: 11px !important;
            color: #7f8c8d !important;
            margin-bottom: 5px !important;
        }
        
        .stat-card h2 {
            font-size: 24px !important;
            color: #2c3e50 !important;
            margin: 0 !important;
        }
        
        /* Gráficos */
        .card {
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            break-inside: avoid;
            page-break-inside: avoid;
            margin-bottom: 25px !important;
            overflow: hidden !important;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            padding: 12px 15px !important;
            font-size: 14px !important;
            font-weight: bold !important;
            border-bottom: none !important;
        }
        
        .card-body {
            padding: 20px !important;
        }
        
        /* Canvas de gráficos */
        canvas {
            max-height: 250px !important;
            margin: 10px auto !important;
            display: block !important;
        }
        
        /* Tablas */
        .table {
            font-size: 9px !important;
            margin-top: 15px !important;
        }
        
        .table thead {
            background: #34495e !important;
            color: white !important;
        }
        
        .table thead th {
            padding: 8px 5px !important;
            font-weight: bold !important;
        }
        
        .table tbody td {
            padding: 6px 5px !important;
            border-bottom: 1px solid #ecf0f1 !important;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa !important;
        }
        
        /* Rows */
        .row {
            page-break-inside: avoid;
        }
        
        /* Evitar cortes de página */
        .mb-4 {
            margin-bottom: 20px !important;
        }
        
        /* Footer en cada página */
        @page {
            margin: 1.5cm;
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 10px;
                color: #7f8c8d;
            }
        }
        
        /* Saltos de página estratégicos */
        .page-break-before {
            page-break-before: always;
        }
        
        .page-break-after {
            page-break-after: always;
        }
        
        /* Optimización de colores para impresión */
        .bg-primary, .border-primary { border-color: #3498db !important; }
        .bg-success, .border-success { border-color: #27ae60 !important; }
        .bg-info, .border-info { border-color: #3498db !important; }
        .text-primary { color: #2980b9 !important; }
        .text-success { color: #27ae60 !important; }
        .text-danger { color: #c0392b !important; }
        .text-warning { color: #f39c12 !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center" data-date="{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}">📊 Estadísticas y Reportes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Estadísticas</li>
    </ol>

    <!-- Filtros de Fecha Mejorados -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa-solid fa-filter"></i> Filtros de Fecha</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('estadisticas.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label fw-bold">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label fw-bold">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary no-print">
                        <i class="fa-solid fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('estadisticas.index') }}" class="btn btn-outline-secondary no-print">
                        <i class="fa-solid fa-times"></i> Limpiar
                    </a>
                    <a href="{{ route('estadisticas.exportar-excel', request()->all()) }}" class="btn btn-success no-print">
                        <i class="fa-solid fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('estadisticas.pdf', request()->all()) }}" class="btn btn-danger no-print" target="_blank">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary no-print">
                        <i class="fa-solid fa-print"></i> Imprimir
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Debug Info --}}
    <div class="alert alert-info no-print">
        <strong>Debug Info:</strong><br>
        Total Ventas Filtradas: {{ $totalVentas }}<br>
        Ventas Por Día: {{ $ventasPorDia->count() }} días<br>
        Ventas Por Mes: {{ $ventasPorMes->count() }} meses<br>
        Ventas Por Tipo: {{ $ventasPorTipo->count() }} tipos<br>
        Productos Vendidos: {{ $productosMasVendidos->count() }}<br>
        Rango: {{ $fechaInicio }} a {{ $fechaFin }}
    </div>

    <!-- Tarjetas de Resumen Mejoradas -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 5px solid #667eea !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Total Ventas</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalVentas }}</h2>
                            <small class="text-muted">Transacciones</small>
                        </div>
                        <div class="text-primary" style="font-size: 3.5rem; opacity: 0.2;">
                            <i class="fa-solid fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Ingresos Totales</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalIngresos, 0, ',', '.') }}</h2>
                            <small class="text-muted">Guaraníes</small>
                        </div>
                        <div class="text-success" style="font-size: 3.5rem; opacity: 0.2;">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 5px solid #17a2b8 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Promedio por Venta</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($promedioVenta, 0, ',', '.') }}</h2>
                            <small class="text-muted">Guaraníes</small>
                        </div>
                        <div class="text-info" style="font-size: 3.5rem; opacity: 0.2;">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Compras -->
    <h5 class="mb-3 text-muted"><i class="fa-solid fa-shopping-bag"></i> Resumen de Compras</h5>
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Total Compras</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalCompras }}</h2>
                            <small class="text-muted">Transacciones</small>
                        </div>
                        <div class="text-danger" style="font-size: 3.5rem; opacity: 0.2;">
                            <i class="fa-solid fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">Gastos Totales</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($totalGastos, 0, ',', '.') }}</h2>
                            <small class="text-muted">Guaraníes</small>
                        </div>
                        <div class="text-warning" style="font-size: 3.5rem; opacity: 0.2;">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Ventas por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-calendar"></i> Ventas Por Día de la Semana</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartVentasPorDia"></canvas>
                </div>
            </div>
        </div>

        <!-- Ventas por Tipo de Producto -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-pie-chart"></i> Ventas por Tipo de Producto</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartVentasPorTipo"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas por Mes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-calendar-alt"></i> Ventas por Mes (Histórico)</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartVentasPorMes" height="100"></canvas>
                    <div class="mt-4 table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Comida</th>
                                    <th>Tragos</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasPorMes as $ventaMes)
                                <tr>
                                    <td>{{ $ventaMes->mes }}</td>
                                    <td class="text-warning">{{ number_format($ventaMes->total_comida, 0, ',', '.') }} Gs</td>
                                    <td class="text-primary">{{ number_format($ventaMes->total_tragos, 0, ',', '.') }} Gs</td>
                                    <td class="fw-bold">{{ number_format($ventaMes->total_comida + $ventaMes->total_tragos, 0, ',', '.') }} Gs</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-trophy"></i> Top 10 Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Cantidad Vendida</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosMasVendidos as $index => $producto)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td><span class="badge bg-primary">{{ $producto->total_vendido }}</span></td>
                                    <td class="fw-bold">{{ number_format($producto->ingresos, 0, ',', '.') }} Gs</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas por Estado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-tasks"></i> Distribución por Estado</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($ventasPorEstado as $estado)
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>{{ ucfirst($estado->estado_pedido) }}</h5>
                                    <h2 class="text-primary">{{ $estado->total }}</h2>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Gráfico de Ventas por Día de la Semana
    const ctxDia = document.getElementById('chartVentasPorDia').getContext('2d');
    new Chart(ctxDia, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ventasPorDia->pluck('dia')) !!},
            datasets: [
                {
                    label: 'Comida',
                    data: {!! json_encode($ventasPorDia->pluck('total_comida')) !!},
                    backgroundColor: 'rgba(255, 159, 64, 0.8)',
                    borderColor: 'rgb(255, 159, 64)',
                    borderWidth: 1
                },
                {
                    label: 'Tragos',
                    data: {!! json_encode($ventasPorDia->pluck('total_tragos')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Gráfico de Ventas por Tipo
    const ctxTipo = document.getElementById('chartVentasPorTipo').getContext('2d');
    new Chart(ctxTipo, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ventasPorTipo->pluck('tipo_producto')->map(function($tipo) {
                return ucfirst($tipo ?? 'Sin tipo');
            })) !!},
            datasets: [{
                data: {!! json_encode($ventasPorTipo->pluck('total')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Ventas por Mes
    const ctxMes = document.getElementById('chartVentasPorMes').getContext('2d');
    new Chart(ctxMes, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ventasPorMes->pluck('mes')) !!},
            datasets: [
                {
                    label: 'Comida',
                    data: {!! json_encode($ventasPorMes->pluck('total_comida')) !!},
                    backgroundColor: 'rgba(255, 159, 64, 0.8)',
                    borderColor: 'rgb(255, 159, 64)',
                    borderWidth: 1
                },
                {
                    label: 'Tragos',
                    data: {!! json_encode($ventasPorMes->pluck('total_tragos')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>
@endpush
