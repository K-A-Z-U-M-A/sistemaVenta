<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estadísticas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18px;
            color: #34495e;
            font-weight: normal;
            margin-bottom: 10px;
        }
        
        .header .meta {
            font-size: 11px;
            color: #7f8c8d;
        }
        
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 15px;
        }
        
        .card {
            flex: 1;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
        }
        
        .card-title {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .card-value {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 15px;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table thead {
            background: #34495e;
            color: white;
        }
        
        table th {
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 11px;
        }
        
        table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        
        table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-orange {
            background: #ff9f43;
            color: white;
        }
        
        .badge-blue {
            background: #54a0ff;
            color: white;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
        
        .two-columns {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .column {
            flex: 1;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            @page {
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Header -->
    <div class="header">
        <h1>{{ env('APP_NAME', 'Sistema de Ventas') }}</h1>
        <h2>Reporte de Estadísticas de Ventas</h2>
        <div class="meta">
            Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>
            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <div class="card-title">Total Ventas</div>
            <div class="card-value">{{ $totalVentas }}</div>
        </div>
        <div class="card">
            <div class="card-title">Ingresos Totales</div>
            <div class="card-value">{{ number_format($totalIngresos, 0, ',', '.') }} Gs</div>
        </div>
        <div class="card">
            <div class="card-title">Promedio por Venta</div>
            <div class="card-value">{{ number_format($promedioVenta, 0, ',', '.') }} Gs</div>
        </div>
    </div>

    <!-- Ventas por Día de la Semana -->
    <div class="section">
        <div class="section-title">📅 Ventas por Día de la Semana</div>
        <div style="max-width: 100%; height: 300px; margin: 20px auto;">
            <canvas id="chartDia"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th class="text-right">Comida (Gs)</th>
                    <th class="text-right">Tragos (Gs)</th>
                    <th class="text-right">Total (Gs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPorDia as $dia)
                <tr>
                    <td><strong>{{ $dia->dia }}</strong></td>
                    <td class="text-right">
                        <span class="badge badge-orange">{{ number_format($dia->total_comida, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span class="badge badge-blue">{{ number_format($dia->total_tragos, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right"><strong>{{ number_format($dia->total_comida + $dia->total_tragos, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
                <tr style="background: #e8f4f8; font-weight: bold;">
                    <td>TOTAL SEMANAL</td>
                    <td class="text-right">{{ number_format($ventasPorDia->sum('total_comida'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ventasPorDia->sum('total_tragos'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ventasPorDia->sum('total_comida') + $ventasPorDia->sum('total_tragos'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Ventas por Mes -->
    <div class="section">
        <div class="section-title">📊 Ventas por Mes del Año</div>
        <div style="max-width: 100%; height: 300px; margin: 20px auto;">
            <canvas id="chartMes"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th class="text-right">Comida (Gs)</th>
                    <th class="text-right">Tragos (Gs)</th>
                    <th class="text-right">Total (Gs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPorDia as $dia)
                <tr>
                    <td><strong>{{ $dia->dia }}</strong></td>
                    <td class="text-right">
                        <span class="badge badge-orange">{{ number_format($dia->total_comida, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span class="badge badge-blue">{{ number_format($dia->total_tragos, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right"><strong>{{ number_format($dia->total_comida + $dia->total_tragos, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
                <tr style="background: #e8f4f8; font-weight: bold;">
                    <td>TOTAL SEMANAL</td>
                    <td class="text-right">{{ number_format($ventasPorDia->sum('total_comida'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ventasPorDia->sum('total_tragos'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ventasPorDia->sum('total_comida') + $ventasPorDia->sum('total_tragos'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Ventas por Mes -->
    <div class="section">
        <div class="section-title">📊 Ventas por Mes del Año</div>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th class="text-right">Comida (Gs)</th>
                    <th class="text-right">Tragos (Gs)</th>
                    <th class="text-right">Total (Gs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPorMes as $mes)
                <tr>
                    <td><strong>{{ $mes->mes }}</strong></td>
                    <td class="text-right">
                        <span class="badge badge-orange">{{ number_format($mes->total_comida, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span class="badge badge-blue">{{ number_format($mes->total_tragos, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right"><strong>{{ number_format($mes->total_comida + $mes->total_tragos, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
                <tr style="background: #e8f4f8; font-weight: bold;">
                    <td>TOTAL ANUAL</td>
                    <td class="text-right">{{ number_format($ventasPorMes->sum('total_comida'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ventasPorMes->sum('total_tragos'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ventasPorMes->sum('total_comida') + $ventasPorMes->sum('total_tragos'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Top 10 Productos -->
    <div class="section">
        <div class="section-title">🏆 Top 10 Productos Más Vendidos</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th class="text-right">Cantidad Vendida</th>
                    <th class="text-right">Ingresos (Gs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosMasVendidos as $index => $producto)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td class="text-right">{{ $producto->total_vendido }}</td>
                    <td class="text-right">{{ number_format($producto->ingresos, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Ventas por Tipo de Producto -->
    <div class="section">
        <div class="section-title">🍽️ Distribución por Tipo de Producto</div>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th class="text-right">Cantidad Vendida</th>
                    <th class="text-right">Ingresos (Gs)</th>
                    <th class="text-right">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalVentasTipo = $ventasPorTipo->sum('total');
                @endphp
                @foreach($ventasPorTipo as $tipo)
                <tr>
                    <td><strong>{{ ucfirst($tipo->tipo_producto ?? 'Sin tipo') }}</strong></td>
                    <td class="text-right">{{ $tipo->cantidad }}</td>
                    <td class="text-right">{{ number_format($tipo->total, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $totalVentasTipo > 0 ? number_format(($tipo->total / $totalVentasTipo) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ env('APP_NAME', 'Sistema de Ventas') }}</strong></p>
        <p>Este reporte fue generado automáticamente el {{ \Carbon\Carbon::now()->format('d/m/Y') }} a las {{ \Carbon\Carbon::now()->format('H:i') }}</p>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Ventas por Día de la Semana
        const ctxDia = document.getElementById('chartDia').getContext('2d');
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
                maintainAspectRatio: true,
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

        // Gráfico de Ventas por Mes
        const ctxMes = document.getElementById('chartMes').getContext('2d');
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
                maintainAspectRatio: true,
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

        // Esperar a que los gráficos se rendericen antes de imprimir
        setTimeout(function() {
            window.print();
        }, 1000);
    </script>
</body>
</html>
