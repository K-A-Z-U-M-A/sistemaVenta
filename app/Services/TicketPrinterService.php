<?php

namespace App\Services;

use App\Models\Venta;
// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\EscposImage;

class TicketPrinterService
{
    protected $printerName;

    public function __construct()
    {
        // Nombre de la impresora (se puede configurar en .env)
        $this->printerName = env('TICKET_PRINTER_NAME', 'POS-58');
    }

    /**
     * Imprime un ticket de venta
     * 
     * @param Venta $venta
     * @return bool
     */
    public function imprimirTicket(Venta $venta)
    {
        // Verificar si la librería está instalada
        if (!class_exists('Mike42\Escpos\Printer')) {
            \Log::warning('Librería de impresión no instalada. Ejecuta: composer require mike42/escpos-php');
            return false;
        }

        try {
            // Determinar el tipo de conector basado en el nombre
            $connector = null;
            if (preg_match('/^COM\d+$/i', $this->printerName) || preg_match('/^LPT\d+$/i', $this->printerName)) {
                // Puerto Serial/Paralelo (común en Bluetooth/USB sin driver de spooler)
                if (!class_exists('Mike42\Escpos\PrintConnectors\FilePrintConnector')) {
                   // Fallback seguro si no está la clase, aunque debería estar en el paquete
                   throw new \Exception("FilePrintConnector no disponible para puertos COM/LPT");
                }
                $connector = new \Mike42\Escpos\PrintConnectors\FilePrintConnector($this->printerName);
            } else {
                // Impresora compartida de Windows (SMB/Spooler)
                $connector = new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($this->printerName);
            }

            $printer = new \Mike42\Escpos\Printer($connector);

            // Encabezado
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text(env('APP_NAME', 'Sistema de Ventas') . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("--------------------------------\n");
            
            // Información del negocio
            $printer->text("RUC: " . env('BUSINESS_RUC', '80000000-0') . "\n");
            $printer->text("Tel: " . env('BUSINESS_PHONE', '0000-000000') . "\n");
            $printer->text("--------------------------------\n");
            
            // Información de la venta
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
            $printer->text("Ticket: " . $venta->numero_comprobante . "\n");
            $printer->text("Fecha: " . $venta->fecha_hora->format('d/m/Y H:i') . "\n");
            
            if ($venta->numero_mesa) {
                $printer->text("Mesa: " . $venta->numero_mesa . "\n");
            }
            
            if ($venta->cliente) {
                $printer->text("Cliente: " . $venta->cliente->persona->razon_social . "\n");
            } elseif ($venta->nombre_pedido) {
                $printer->text("Pedido: " . $venta->nombre_pedido . "\n");
            } else {
                $printer->text("Cliente: Sin identificar\n");
            }
            
            $printer->text("Cajero: " . $venta->user->name . "\n");
            $printer->text("Estado: " . strtoupper($venta->estado_pedido ?? 'pendiente') . "\n");
            $printer->text("--------------------------------\n");
            
            // Productos
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-20s %3s %8s\n", "PRODUCTO", "CANT", "PRECIO"));
            $printer->setEmphasis(false);
            $printer->text("--------------------------------\n");
            
            $subtotal = 0;
            foreach ($venta->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $precio = $producto->pivot->precio_venta;
                $total = $cantidad * $precio;
                $subtotal += $total;
                
                // Nombre del producto (puede ser largo)
                $nombre = substr($producto->nombre, 0, 20);
                $printer->text(sprintf("%-20s\n", $nombre));
                
                // Tipo de producto
                $tipo = strtoupper($producto->tipo_producto ?? 'COMIDA');
                $printer->text(sprintf("  [%s]", $tipo));
                
                // Cantidad y precio
                $printer->text(sprintf("%15s %3d %8s\n", "", $cantidad, number_format($precio, 0, ',', '.')));
                
                // Si es trago con descuento
                if ($producto->pivot->es_trago_con_descuento) {
                    $descuento = $producto->pivot->descuento_trago;
                    $printer->text(sprintf("  Desc. Trago: -%s Gs\n", number_format($descuento, 0, ',', '.')));
                }
            }
            
            $printer->text("--------------------------------\n");
            
            // Totales
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-20s %11s\n", "SUBTOTAL:", number_format($subtotal, 0, ',', '.') . " Gs"));
            
            if ($venta->descuento_tragos > 0) {
                $printer->setEmphasis(false);
                $printer->text(sprintf("%-20s %11s\n", "Desc. Tragos:", "-" . number_format($venta->descuento_tragos, 0, ',', '.') . " Gs"));
                $printer->text(sprintf("%-20s %11s\n", "Ganancia a Comida:", "+" . number_format($venta->ganancia_tragos_a_comida, 0, ',', '.') . " Gs"));
            }
            
            if ($venta->impuesto > 0) {
                $printer->setEmphasis(false);
                $printer->text(sprintf("%-20s %11s\n", "IVA:", number_format($venta->impuesto, 0, ',', '.') . " Gs"));
            }
            
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text(sprintf("%-12s %11s\n", "TOTAL:", number_format($venta->total, 0, ',', '.') . " Gs"));
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            
            $printer->text("--------------------------------\n");
            
            // Notas
            if ($venta->notas) {
                $printer->text("Notas: " . $venta->notas . "\n");
                $printer->text("--------------------------------\n");
            }
            
            // Pie de página
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->text("¡Gracias por su compra!\n");
            $printer->text(env('BUSINESS_SLOGAN', 'Vuelva pronto') . "\n");
            
            $printer->feed(3);
            $printer->cut();
            
            // Cerrar conexión
            $printer->close();
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Error al imprimir ticket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Imprime un ticket de pedido para cocina
     * 
     * @param Venta $venta
     * @return bool
     */
    public function imprimirTicketCocina(Venta $venta)
    {
        // Verificar si la librería está instalada
        if (!class_exists('Mike42\Escpos\Printer')) {
            \Log::warning('Librería de impresión no instalada. Ejecuta: composer require mike42/escpos-php');
            return false;
        }

        try {
            // Determinar el tipo de conector basado en el nombre
            $connector = null;
            if (preg_match('/^COM\d+$/i', $this->printerName) || preg_match('/^LPT\d+$/i', $this->printerName)) {
                $connector = new \Mike42\Escpos\PrintConnectors\FilePrintConnector($this->printerName);
            } else {
                $connector = new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($this->printerName);
            }
            
            $printer = new \Mike42\Escpos\Printer($connector);

            // Encabezado
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->setTextSize(3, 3);
            $printer->text("PEDIDO\n");
            $printer->setTextSize(1, 1);
            $printer->text("--------------------------------\n");
            
            // Información del pedido
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
            $printer->setTextSize(2, 2);
            $printer->text("Mesa: " . ($venta->numero_mesa ?? 'N/A') . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("Hora: " . $venta->fecha_hora->format('H:i') . "\n");
            $printer->text("Ticket: " . $venta->numero_comprobante . "\n");
            $printer->text("--------------------------------\n");
            
            // Productos agrupados por tipo
            $productosPorTipo = $venta->productos->groupBy('tipo_producto');
            
            foreach ($productosPorTipo as $tipo => $productos) {
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2);
                $printer->text(strtoupper($tipo ?? 'COMIDA') . ":\n");
                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);
                
                foreach ($productos as $producto) {
                    $cantidad = $producto->pivot->cantidad;
                    $printer->text(sprintf(" %2d x %s\n", $cantidad, $producto->nombre));
                }
                
                $printer->text("\n");
            }
            
            // Notas
            if ($venta->notas) {
                $printer->text("--------------------------------\n");
                $printer->setEmphasis(true);
                $printer->text("NOTAS:\n");
                $printer->setEmphasis(false);
                $printer->text($venta->notas . "\n");
            }
            
            $printer->feed(3);
            $printer->cut();
            $printer->close();
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Error al imprimir ticket de cocina: ' . $e->getMessage());
            return false;
        }
    }
}
