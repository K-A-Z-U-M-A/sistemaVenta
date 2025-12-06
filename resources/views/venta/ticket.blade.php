<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            margin: 0;
            padding: 5px;
            width: 80mm;
            background-color: #fff;
        }
        .ticket {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
        }
        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        .info-section {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        .info-label {
            font-weight: bold;
        }
        .products-table {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .product-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 10px;
        }
        .product-row {
            margin-bottom: 5px;
        }
        .product-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .product-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #333;
        }
        .totals {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .total-row.main {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
        }
        @media print {
            body {
                margin: 0;
                padding: 5px;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <h2>{{ env('APP_NAME', "Doggie's") }}</h2>
            @if(isset($tipo))
                <p><strong>*** {{ strtoupper($tipo) }} ***</strong></p>
            @endif
            <p>{{ env('BUSINESS_SLOGAN', 'Gracias por su preferencia') }}</p>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Ticket:</span>
                <span>{{ $venta->numero_comprobante }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}</span>
            </div>
            @if($venta->numero_mesa)
            <div class="info-row">
                <span class="info-label">Mesa:</span>
                <span>{{ $venta->numero_mesa }}</span>
            </div>
            @endif
            @if($venta->cliente)
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span>{{ $venta->cliente->persona->razon_social }}</span>
            </div>
            @elseif($venta->nombre_pedido)
            <div class="info-row">
                <span class="info-label">Pedido:</span>
                <span>{{ $venta->nombre_pedido }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Cajero:</span>
                <span>{{ $venta->user->name }}</span>
            </div>
        </div>

        <!-- Products -->
        <div class="products-table">
            <div class="product-header">
                <span>PRODUCTO</span>
                <span>TOTAL</span>
            </div>
            @php
                $productosFiltrados = $venta->productos;
                if (isset($tipo)) {
                    if ($tipo === 'comidas') {
                        $productosFiltrados = $productosFiltrados->filter(function($p) {
                            return $p->tipo_producto !== 'trago';
                        });
                    } elseif ($tipo === 'tragos') {
                        $productosFiltrados = $productosFiltrados->filter(function($p) {
                            return $p->tipo_producto === 'trago';
                        });
                    }
                }
                $totalFiltrado = 0;
            @endphp
            @foreach ($productosFiltrados as $producto)
            <div class="product-row">
                <div class="product-name">{{ $producto->nombre }}</div>
                <div class="product-details">
                    <span>{{ $producto->pivot->cantidad }} x {{ number_format($producto->pivot->precio_venta, 0, ',', '.') }}</span>
                    @php
                        $lineTotal = ($producto->pivot->cantidad * $producto->pivot->precio_venta) - $producto->pivot->descuento - ($producto->pivot->cantidad * $producto->pivot->descuento_trago);
                        $totalFiltrado += $lineTotal;
                    @endphp
                    <span><strong>{{ number_format($lineTotal, 0, ',', '.') }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row main">
                <span>TOTAL{{ isset($tipo) ? ' (' . strtoupper($tipo) . ')' : '' }}:</span>
                <span>{{ number_format(isset($tipo) ? $totalFiltrado : $venta->total, 0, ',', '.') }} Gs</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>¡Gracias por su compra!</strong></p>
            <p>{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
