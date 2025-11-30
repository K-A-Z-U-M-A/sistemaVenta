<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\TicketPrinterService;
use App\Services\TragoDescuentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ventaController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store', 'edit', 'update']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        $this->middleware('permission:eliminar-venta', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cliente ahora es nullable, cargar solo si existe
        $query = Venta::with(['comprobante', 'user', 'cliente.persona', 'productos'])
            ->where('estado',1);
            
        // Filtro de rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }
        
        // Si no hay filtros de fecha y no se pide ver todo, mostrar solo hoy
        if (!$request->filled('fecha_desde') && !$request->filled('fecha_hasta') && !$request->has('ver_todo')) {
            $query->whereDate('fecha_hora', \Carbon\Carbon::today());
        }
        
        // Filtro por estado del pedido
        if ($request->filled('estado_pedido')) {
            $query->where('estado_pedido', $request->estado_pedido);
        }
        
        // Filtro por forma de pago
        if ($request->filled('forma_pago')) {
            $query->where('forma_pago', $request->forma_pago);
        }
        
        // Filtro por vendedor
        if ($request->filled('vendedor_id')) {
            $query->where('user_id', $request->vendedor_id);
        }
        
        // Filtro por cliente (búsqueda en nombre)
        if ($request->filled('cliente_buscar')) {
            $query->whereHas('cliente.persona', function($q) use ($request) {
                $q->where('razon_social', 'ILIKE', '%' . $request->cliente_buscar . '%');
            })->orWhere('nombre_pedido', 'ILIKE', '%' . $request->cliente_buscar . '%');
        }
            
        $ventas = $query->latest()->get();
        
        // Obtener lista de vendedores para el filtro
        $vendedores = \App\Models\User::orderBy('name')->get();

        return view('venta.index', compact('ventas', 'vendedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar si hay una caja abierta
        $cajaAbierta = \App\Models\Caja::cajaAbierta();
        
        if (!$cajaAbierta) {
            return redirect()->route('ventas.index')
                ->with('error', 'Debe abrir la caja antes de realizar una venta. Por favor, diríjase a la sección de Caja.');
        }

        // Obtener productos directamente sin necesidad de compras
        $productos = Producto::select('productos.nombre', 'productos.id', 'productos.stock', 'productos.precio_venta', 'productos.tipo_producto', 'productos.aplica_descuento_trago', 'productos.impuesto')
            ->where('productos.estado', 1)
            ->where('productos.stock', '>', 0)
            ->get();

        // Cliente es opcional para comida rápida
        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();
        
        $comprobantes = Comprobante::all();

        return view('venta.create', compact('productos', 'clientes', 'comprobantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentaRequest $request)
    {
        try{
            DB::beginTransaction();

            // Obtener la caja abierta del usuario
            $cajaAbierta = \App\Models\Caja::where('user_id', auth()->id())
                ->where('estado', 'abierta')
                ->first();

            // Crear array de datos validados
            $ventaData = $request->validated();
            
            // Agregar caja_id si hay una caja abierta
            if ($cajaAbierta) {
                $ventaData['caja_id'] = $cajaAbierta->id;
            }

            //Llenar mi tabla venta
            $venta = Venta::create($ventaData);

            //Llenar mi tabla venta_producto
            //1. Recuperar los arrays
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioVenta = $request->get('arrayprecioventa');
            $arrayDescuento = $request->get('arraydescuento');

            //2.Realizar el llenado y aplicar descuentos
            $siseArray = count($arrayProducto_id);
            $cont = 0;
            $totalDescuentoTragos = 0;

            while($cont < $siseArray){
                $producto = Producto::find($arrayProducto_id[$cont]);
                $cantidad = intval($arrayCantidad[$cont]);
                
                // Verificar si es trago con descuento
                $esTragoConDescuento = false;
                $descuentoTrago = 0;
                
                if ($producto->tipo_producto === 'trago' && $producto->aplica_descuento_trago) {
                    $esTragoConDescuento = true;
                    $descuentoTrago = 5000; // 5000 Gs por trago
                    $totalDescuentoTragos += $descuentoTrago * $cantidad;
                }
                
                $venta->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'cantidad' => $cantidad,
                        'precio_venta' => $arrayPrecioVenta[$cont],
                        'descuento' => $arrayDescuento[$cont],
                        'es_trago_con_descuento' => $esTragoConDescuento,
                        'descuento_trago' => $descuentoTrago
                    ]
                ]);

                //Actualizar stock
                $stockActual = $producto->stock;
                DB::table('productos')
                ->where('id',$producto->id)
                ->update([
                    'stock' => $stockActual - $cantidad
                ]);

                $cont++;
            }
            
            // Actualizar la venta con los descuentos de tragos
            if ($totalDescuentoTragos > 0) {
                $venta->update([
                    'descuento_tragos' => $totalDescuentoTragos,
                    'ganancia_tragos_a_comida' => $totalDescuentoTragos
                ]);
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }

        return redirect()->route('ventas.index')->with('success','Venta exitosa');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        return view('venta.show',compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();
        
        return view('venta.edit', compact('venta', 'clientes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_pedido' => 'nullable|string|max:100',
            'numero_mesa' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
            'estado_pedido' => 'required|in:pendiente,preparacion,completado,entregado,cancelado'
        ]);

        $venta->update([
            'cliente_id' => $request->cliente_id,
            'nombre_pedido' => $request->nombre_pedido,
            'numero_mesa' => $request->numero_mesa,
            'notas' => $request->notas,
            'estado_pedido' => $request->estado_pedido
        ]);

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $venta = Venta::with('productos')->findOrFail($id);
            
            // Devolver stock de productos
            foreach ($venta->productos as $producto) {
                $stockActual = $producto->stock;
                $cantidad = $producto->pivot->cantidad;
                
                $producto->update([
                    'stock' => $stockActual + $cantidad
                ]);
            }
            
            // Cambiar estado a anulado (0)
            $venta->update([
                'estado' => 0,
                'estado_pedido' => 'cancelado'
            ]);

            DB::commit();
            return redirect()->route('ventas.index')->with('success','Venta anulada y stock restaurado');
            
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.index')->with('error','Error al anular venta: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir ticket de venta
     */
    /**
     * Imprimir ticket de venta
     */
    public function imprimirTicket(Venta $venta)
    {
        return view('venta.ticket', compact('venta'));
    }

    /**
     * Imprimir ticket de cocina
     */
    public function imprimirTicketCocina(Venta $venta)
    {
        $printerService = new TicketPrinterService();
        $resultado = $printerService->imprimirTicketCocina($venta);

        if ($resultado) {
            return back()->with('success', 'Ticket de cocina impreso exitosamente');
        } else {
            return back()->with('error', 'Error al imprimir el ticket de cocina. Verifique la impresora.');
        }
    }

    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado(Request $request, Venta $venta)
    {
        $request->validate([
            'estado_pedido' => 'required|in:pendiente,preparacion,completado,entregado,cancelado'
        ]);

        try {
            DB::beginTransaction();
            
            // Cargar productos con su información de stock
            $venta->load('productos');
            
            $estadoAnterior = $venta->estado_pedido;
            $estadoNuevo = $request->estado_pedido;
            
            // Si se está cancelando un pedido que no estaba cancelado
            if ($estadoNuevo === 'cancelado' && $estadoAnterior !== 'cancelado') {
                // Devolver stock de productos
                foreach ($venta->productos as $producto) {
                    $stockActual = $producto->stock;
                    $cantidad = $producto->pivot->cantidad;
                    
                    $producto->update([
                        'stock' => $stockActual + $cantidad
                    ]);
                }
                
                $venta->update([
                    'estado_pedido' => $estadoNuevo,
                    'estado' => 0 // Marcar como anulado
                ]);
                
                DB::commit();
                return back()->with('success', 'Pedido cancelado y stock restaurado');
            }
            
            // Si se está reactivando un pedido cancelado
            if ($estadoAnterior === 'cancelado' && $estadoNuevo !== 'cancelado') {
                // Verificar que haya stock disponible
                foreach ($venta->productos as $producto) {
                    $cantidad = $producto->pivot->cantidad;
                    if ($producto->stock < $cantidad) {
                        DB::rollBack();
                        return back()->with('error', "No hay stock suficiente de {$producto->nombre}. Disponible: {$producto->stock}, Necesario: {$cantidad}");
                    }
                }
                
                // Descontar stock nuevamente
                foreach ($venta->productos as $producto) {
                    $stockActual = $producto->stock;
                    $cantidad = $producto->pivot->cantidad;
                    
                    $producto->update([
                        'stock' => $stockActual - $cantidad
                    ]);
                }
                
                $venta->update([
                    'estado_pedido' => $estadoNuevo,
                    'estado' => 1 // Reactivar
                ]);
                
                DB::commit();
                return back()->with('success', 'Pedido reactivado y stock descontado');
            }
            
            // Cambio de estado normal (sin afectar stock)
            $venta->update([
                'estado_pedido' => $estadoNuevo
            ]);
            
            DB::commit();
            return back()->with('success', 'Estado del pedido actualizado a: ' . ucfirst($estadoNuevo));
            
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }
}
