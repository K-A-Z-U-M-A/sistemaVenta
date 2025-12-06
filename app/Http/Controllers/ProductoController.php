<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class ProductoController extends Controller
{
    use LogsActivity;
    function __construct()
    {
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-producto', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::with(['categorias.caracteristica','marca.caracteristica','presentacione.caracteristica']);

        // Filtro por tipo de producto
        if ($request->filled('tipo_producto')) {
            $query->where('tipo_producto', $request->tipo_producto);
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->whereHas('categorias', function($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        // Filtro por marca
        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        // Filtro por presentación
        if ($request->filled('presentacione_id')) {
            $query->where('presentacione_id', $request->presentacione_id);
        }

        // Filtro por stock bajo (menor o igual a un valor)
        if ($request->filled('stock_bajo')) {
            $query->where('stock', '<=', $request->stock_bajo);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por nombre o código
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'ILIKE', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'ILIKE', '%' . $request->buscar . '%');
            });
        }

        $productos = $query->latest()->get();

        // Obtener datos para los filtros
        $categorias = \App\Models\Categoria::with('caracteristica')->get();
        $marcas = \App\Models\Marca::with('caracteristica')->get();
        $presentaciones = \App\Models\Presentacione::with('caracteristica')->get();

        return view('producto.index', compact('productos', 'categorias', 'marcas', 'presentaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
            ->select('marcas.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $presentaciones = Presentacione::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
            ->select('presentaciones.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
            ->select('categorias.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        return view('producto.create', compact('marcas', 'presentaciones', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request)
    {
        //dd($request);
        try {
            DB::beginTransaction();
            //Tabla producto
            $producto = new Producto();
            if ($request->hasFile('img_path')) {
                $name = $producto->handleUploadImage($request->file('img_path'));
            } else {
                $name = null;
            }

            $producto->fill([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id,
                'tipo_producto' => $request->tipo_producto,
                'precio_venta' => $request->precio_venta,
                'impuesto' => $request->impuesto,
                'aplica_descuento_trago' => $request->has('aplica_descuento_trago') ? 1 : 0
            ]);

            $producto->save();

            //Tabla categoría producto
            $categorias = $request->get('categorias');
            $producto->categorias()->attach($categorias);


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('productos.index')->with('success', 'Producto registrado');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
            ->select('marcas.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $presentaciones = Presentacione::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
            ->select('presentaciones.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
            ->select('categorias.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        return view('producto.edit',compact('producto','marcas','presentaciones','categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try{
            DB::beginTransaction();

            if ($request->hasFile('img_path')) {
                $name = $producto->handleUploadImage($request->file('img_path'));

                //Eliminar si existiese una imagen antigua
                if($producto->img_path && file_exists(public_path('imagenes-productos/'.$producto->img_path))){
                    unlink(public_path('imagenes-productos/'.$producto->img_path));
                }

            } else {
                $name = $producto->img_path;
            }

            $producto->fill([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id,
                'tipo_producto' => $request->tipo_producto,
                'precio_venta' => $request->precio_venta,
                'stock' => $request->stock,
                'impuesto' => $request->impuesto,
                'aplica_descuento_trago' => $request->has('aplica_descuento_trago') ? 1 : 0
            ]);

            $producto->save();

            //Tabla categoría producto
            $categorias = $request->get('categorias');
            $producto->categorias()->sync($categorias);

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('productos.index')->with('success','Producto editado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $producto = Producto::find($id);
        if ($producto->estado == 1) {
            Producto::where('id', $producto->id)
                ->update([
                    'estado' => 0
                ]);
            $message = 'Producto eliminado';
        } else {
            Producto::where('id', $producto->id)
                ->update([
                    'estado' => 1
                ]);
            $message = 'Producto restaurado';
        }

        return redirect()->route('productos.index')->with('success', $message);
    }
}
