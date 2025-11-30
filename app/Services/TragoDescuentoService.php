<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Venta;

class TragoDescuentoService
{
    const DESCUENTO_TRAGO = 5000; // 5000 Guaraníes

    /**
     * Calcula los descuentos de tragos y la ganancia transferida a comida
     * 
     * @param array $productos Array de productos con cantidad y precio
     * @return array
     */
    public function calcularDescuentos($productos)
    {
        $totalDescuentoTragos = 0;
        $productosConDescuento = [];
        $cantidadTragosConDescuento = 0;

        foreach ($productos as $productoData) {
            $producto = Producto::find($productoData['producto_id']);
            
            if ($producto && $producto->tipo_producto === 'trago' && $producto->aplica_descuento_trago) {
                $cantidad = $productoData['cantidad'];
                $descuentoPorProducto = self::DESCUENTO_TRAGO * $cantidad;
                $totalDescuentoTragos += $descuentoPorProducto;
                $cantidadTragosConDescuento += $cantidad;

                $productosConDescuento[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'descuento_unitario' => self::DESCUENTO_TRAGO,
                    'descuento_total' => $descuentoPorProducto,
                    'es_trago_con_descuento' => true
                ];
            }
        }

        return [
            'total_descuento_tragos' => $totalDescuentoTragos,
            'ganancia_tragos_a_comida' => $totalDescuentoTragos,
            'cantidad_tragos_con_descuento' => $cantidadTragosConDescuento,
            'productos_con_descuento' => $productosConDescuento
        ];
    }

    /**
     * Aplica los descuentos de tragos a una venta
     * 
     * @param Venta $venta
     * @param array $productos
     * @return void
     */
    public function aplicarDescuentosAVenta(Venta $venta, $productos)
    {
        $descuentos = $this->calcularDescuentos($productos);

        // Actualizar la venta con los descuentos
        $venta->update([
            'descuento_tragos' => $descuentos['total_descuento_tragos'],
            'ganancia_tragos_a_comida' => $descuentos['ganancia_tragos_a_comida']
        ]);

        // Marcar los productos con descuento en la tabla pivot
        foreach ($descuentos['productos_con_descuento'] as $productoDescuento) {
            $venta->productos()
                ->updateExistingPivot($productoDescuento['producto_id'], [
                    'es_trago_con_descuento' => true,
                    'descuento_trago' => $productoDescuento['descuento_unitario']
                ]);
        }
    }

    /**
     * Genera un reporte de descuentos de tragos
     * 
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function generarReporte($fechaInicio, $fechaFin)
    {
        $ventas = Venta::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('descuento_tragos', '>', 0)
            ->with('productos')
            ->get();

        $totalDescuentos = $ventas->sum('descuento_tragos');
        $totalGananciasTransferidas = $ventas->sum('ganancia_tragos_a_comida');
        $cantidadVentas = $ventas->count();

        return [
            'total_descuentos' => $totalDescuentos,
            'total_ganancias_transferidas' => $totalGananciasTransferidas,
            'cantidad_ventas' => $cantidadVentas,
            'ventas' => $ventas
        ];
    }
}
