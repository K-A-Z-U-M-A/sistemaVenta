<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadisticaController extends Controller
{
    public function index(Request $request)
    {
        // Filtros de fecha - por defecto últimos 12 meses
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(12)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Ventas del período
        $ventasPeriodo = Venta::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->get();

        // Estadísticas generales
        $totalVentas = $ventasPeriodo->count();
        $totalIngresos = $ventasPeriodo->sum('total');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;

        // Ventas por estado
        $ventasPorEstado = Venta::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->select('estado_pedido', DB::raw('count(*) as total'))
            ->groupBy('estado_pedido')
            ->get();

        // Productos más vendidos
        $productosMasVendidos = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 1)
            ->select(
                'productos.nombre',
                DB::raw('SUM(producto_venta.cantidad) as total_vendido'),
                DB::raw('SUM(producto_venta.cantidad * producto_venta.precio_venta) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        // Ventas por día de la semana - TODOS los días (Lunes a Domingo)
        $ventasPorDiaRaw = DB::table('ventas')
            ->join('producto_venta', 'ventas.id', '=', 'producto_venta.venta_id')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->where('ventas.estado', 1)
            ->select(
                DB::raw('EXTRACT(DOW FROM ventas.fecha_hora) as dia_semana'),
                DB::raw("SUM(CASE WHEN productos.tipo_producto != 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento ELSE 0 END) as total_comida"),
                DB::raw("SUM(CASE WHEN productos.tipo_producto = 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento - (producto_venta.cantidad * producto_venta.descuento_trago) ELSE 0 END) as total_tragos")
            )
            ->groupBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        // Crear array completo de días de la semana
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $ventasPorDia = collect();
        for ($i = 0; $i < 7; $i++) {
            $ventasPorDia->push((object)[
                'dia' => $diasSemana[$i],
                'total_comida' => $ventasPorDiaRaw->get($i)->total_comida ?? 0,
                'total_tragos' => $ventasPorDiaRaw->get($i)->total_tragos ?? 0,
            ]);
        }

        // Ventas por tipo de producto
        $ventasPorTipo = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 1)
            ->select(
                'productos.tipo_producto',
                DB::raw('SUM(producto_venta.cantidad) as cantidad'),
                DB::raw('SUM(producto_venta.cantidad * producto_venta.precio_venta) as total')
            )
            ->groupBy('productos.tipo_producto')
            ->get();

        // Ventas por mes - TODOS los 12 meses
        $ventasPorMesRaw = DB::table('ventas')
            ->join('producto_venta', 'ventas.id', '=', 'producto_venta.venta_id')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->where('ventas.estado', 1)
            ->select(
                DB::raw("EXTRACT(MONTH FROM ventas.fecha_hora) as mes_num"),
                DB::raw("SUM(CASE WHEN productos.tipo_producto != 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento ELSE 0 END) as total_comida"),
                DB::raw("SUM(CASE WHEN productos.tipo_producto = 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento - (producto_venta.cantidad * producto_venta.descuento_trago) ELSE 0 END) as total_tragos")
            )
            ->groupBy('mes_num')
            ->get()
            ->keyBy('mes_num');

        // Crear array completo de 12 meses
        $mesesNombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $ventasPorMes = collect();
        for ($i = 1; $i <= 12; $i++) {
            $ventasPorMes->push((object)[
                'mes' => $mesesNombres[$i - 1],
                'total_comida' => $ventasPorMesRaw->get($i)->total_comida ?? 0,
                'total_tragos' => $ventasPorMesRaw->get($i)->total_tragos ?? 0,
            ]);
        }

        return view('estadisticas.index', compact(
            'totalVentas',
            'totalIngresos',
            'promedioVenta',
            'ventasPorEstado',
            'productosMasVendidos',
            'ventasPorDia',
            'ventasPorTipo',
            'ventasPorMes',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        $ventas = Venta::with(['cliente.persona', 'user', 'comprobante'])
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->get();

        $csvFileName = 'ventas_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = array('Fecha', 'Comprobante', 'Numero', 'Cliente', 'Vendedor', 'Total', 'Estado');

        $callback = function() use($ventas, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($ventas as $venta) {
                $row['Fecha']  = $venta->fecha_hora;
                $row['Comprobante'] = $venta->comprobante->tipo_comprobante ?? 'N/A';
                $row['Numero'] = $venta->numero_comprobante;
                $row['Cliente'] = $venta->cliente->persona->razon_social ?? ($venta->nombre_pedido ?? 'Sin Nombre');
                $row['Vendedor'] = $venta->user->name ?? 'N/A';
                $row['Total'] = $venta->total;
                $row['Estado'] = $venta->estado_pedido;

                fputcsv($file, array($row['Fecha'], $row['Comprobante'], $row['Numero'], $row['Cliente'], $row['Vendedor'], $row['Total'], $row['Estado']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf(Request $request)
    {
        // Usar los mismos datos que el index
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(12)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Ventas del período
        $ventasPeriodo = Venta::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->get();

        // Estadísticas generales
        $totalVentas = $ventasPeriodo->count();
        $totalIngresos = $ventasPeriodo->sum('total');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;

        // Productos más vendidos
        $productosMasVendidos = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 1)
            ->select(
                'productos.nombre',
                DB::raw('SUM(producto_venta.cantidad) as total_vendido'),
                DB::raw('SUM(producto_venta.cantidad * producto_venta.precio_venta) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        // Ventas por día de la semana
        $ventasPorDiaRaw = DB::table('ventas')
            ->join('producto_venta', 'ventas.id', '=', 'producto_venta.venta_id')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->where('ventas.estado', 1)
            ->select(
                DB::raw('EXTRACT(DOW FROM ventas.fecha_hora) as dia_semana'),
                DB::raw("SUM(CASE WHEN productos.tipo_producto != 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento ELSE 0 END) as total_comida"),
                DB::raw("SUM(CASE WHEN productos.tipo_producto = 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento - (producto_venta.cantidad * producto_venta.descuento_trago) ELSE 0 END) as total_tragos")
            )
            ->groupBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $ventasPorDia = collect();
        for ($i = 0; $i < 7; $i++) {
            $ventasPorDia->push((object)[
                'dia' => $diasSemana[$i],
                'total_comida' => $ventasPorDiaRaw->get($i)->total_comida ?? 0,
                'total_tragos' => $ventasPorDiaRaw->get($i)->total_tragos ?? 0,
            ]);
        }

        // Ventas por tipo de producto
        $ventasPorTipo = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 1)
            ->select(
                'productos.tipo_producto',
                DB::raw('SUM(producto_venta.cantidad) as cantidad'),
                DB::raw('SUM(producto_venta.cantidad * producto_venta.precio_venta) as total')
            )
            ->groupBy('productos.tipo_producto')
            ->get();

        // Ventas por mes
        $ventasPorMesRaw = DB::table('ventas')
            ->join('producto_venta', 'ventas.id', '=', 'producto_venta.venta_id')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->where('ventas.estado', 1)
            ->select(
                DB::raw("EXTRACT(MONTH FROM ventas.fecha_hora) as mes_num"),
                DB::raw("SUM(CASE WHEN productos.tipo_producto != 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento ELSE 0 END) as total_comida"),
                DB::raw("SUM(CASE WHEN productos.tipo_producto = 'trago' THEN (producto_venta.cantidad * producto_venta.precio_venta) - producto_venta.descuento - (producto_venta.cantidad * producto_venta.descuento_trago) ELSE 0 END) as total_tragos")
            )
            ->groupBy('mes_num')
            ->get()
            ->keyBy('mes_num');

        $mesesNombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $ventasPorMes = collect();
        for ($i = 1; $i <= 12; $i++) {
            $ventasPorMes->push((object)[
                'mes' => $mesesNombres[$i - 1],
                'total_comida' => $ventasPorMesRaw->get($i)->total_comida ?? 0,
                'total_tragos' => $ventasPorMesRaw->get($i)->total_tragos ?? 0,
            ]);
        }

        return view('estadisticas.pdf', compact(
            'totalVentas',
            'totalIngresos',
            'promedioVenta',
            'productosMasVendidos',
            'ventasPorDia',
            'ventasPorTipo',
            'ventasPorMes',
            'fechaInicio',
            'fechaFin'
        ));
    }
}
