<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\DevolucionItem;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devoluciones = Devolucion::with(['venta', 'user', 'items.producto'])
            ->orderBy('fecha_hora', 'desc')
            ->get();
        
        return view('devoluciones.index', compact('devoluciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ventas = Venta::where('estado_pedido', 'completado')
            ->with(['productos', 'cliente'])
            ->orderBy('fecha_hora', 'desc')
            ->get();
        
        return view('devoluciones.create', compact('ventas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'motivo' => 'required|string',
            'tipo' => 'required|in:total,parcial',
            'items' => 'required_if:tipo,parcial|array',
            'items.*.producto_id' => 'required_with:items|exists:productos,id',
            'items.*.cantidad' => 'required_with:items|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($request->venta_id);
            
            // Calcular monto a devolver
            $montoDevuelto = 0;
            
            if ($request->tipo === 'total') {
                $montoDevuelto = $venta->total;
            } else {
                foreach ($request->items as $item) {
                    $productoVenta = $venta->productos()
                        ->where('producto_id', $item['producto_id'])
                        ->first();
                    
                    if ($productoVenta) {
                        $montoDevuelto += $productoVenta->pivot->precio_venta * $item['cantidad'];
                    }
                }
            }

            // Crear devolución
            $devolucion = Devolucion::create([
                'venta_id' => $request->venta_id,
                'user_id' => auth()->id(),
                'fecha_hora' => now(),
                'monto_devuelto' => $montoDevuelto,
                'motivo' => $request->motivo,
                'tipo' => $request->tipo
            ]);

            // Si es devolución parcial, crear items
            if ($request->tipo === 'parcial') {
                foreach ($request->items as $item) {
                    $productoVenta = $venta->productos()
                        ->where('producto_id', $item['producto_id'])
                        ->first();
                    
                    if ($productoVenta) {
                        DevolucionItem::create([
                            'devolucion_id' => $devolucion->id,
                            'producto_id' => $item['producto_id'],
                            'cantidad' => $item['cantidad'],
                            'precio_unitario' => $productoVenta->pivot->precio_venta,
                            'subtotal' => $productoVenta->pivot->precio_venta * $item['cantidad']
                        ]);

                        // Devolver stock
                        $producto = Producto::find($item['producto_id']);
                        $producto->increment('stock', $item['cantidad']);
                    }
                }
            } else {
                // Devolución total - devolver todo el stock
                foreach ($venta->productos as $producto) {
                    $producto->increment('stock', $producto->pivot->cantidad);
                }
            }

            DB::commit();

            return redirect()->route('devoluciones.index')
                ->with('success', 'Devolución registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Devolucion $devolucion)
    {
        $devolucion->load(['venta.productos', 'user', 'items.producto']);
        return view('devoluciones.show', compact('devolucion'));
    }

    /**
     * Get products from a sale for partial return
     */
    public function getVentaProductos($ventaId)
    {
        $venta = Venta::with('productos')->findOrFail($ventaId);
        return response()->json($venta->productos);
    }
}
