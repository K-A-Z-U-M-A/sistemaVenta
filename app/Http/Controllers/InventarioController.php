<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['marca.caracteristica', 'presentacione.caracteristica', 'categorias']);

        // Filtros
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo_producto', $request->tipo);
        }

        if ($request->has('stock_bajo') && $request->stock_bajo == '1') {
            $query->where('stock', '<=', 10);
        }

        if ($request->has('sin_stock') && $request->sin_stock == '1') {
            $query->where('stock', '=', 0);
        }

        $productos = $query->orderBy('stock', 'asc')->get();

        // Estadísticas de inventario
        $totalProductos = Producto::where('estado', 1)->count();
        $productosSinStock = Producto::where('estado', 1)->where('stock', 0)->count();
        $productosStockBajo = Producto::where('estado', 1)->where('stock', '>', 0)->where('stock', '<=', 10)->count();
        return view('inventario.index', compact(
            'productos',
            'totalProductos',
            'productosSinStock',
            'productosStockBajo'
        ));
    }
}
