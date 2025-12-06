@extends('layouts.app')

@section('title','Editar Producto')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Editar Producto</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index')}}">Productos</a></li>
        <li class="breadcrumb-item active">Editar producto</li>
    </ol>

    <div class="card text-bg-light">
        <form action="{{route('productos.update',['producto'=>$producto])}}" method="post" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <div class="card-body">

                <div class="row g-4">
                    <!----Codigo---->
                    <div class="col-md-6">
                        <label for="codigo" class="form-label">Código:</label>
                        <input type="text" name="codigo" id="codigo" class="form-control" value="{{old('codigo',$producto->codigo)}}">
                        @error('codigo')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Nombre---->
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre',$producto->nombre)}}">
                        @error('nombre')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Descripción---->
                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{old('descripcion',$producto->descripcion)}}</textarea>
                        @error('descripcion')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Fecha de vencimiento---->
                    <div class="col-md-6">
                        <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento:</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{old('fecha_vencimiento',$producto->fecha_vencimiento)}}">
                        @error('fecha_vencimiento')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Imagen---->
                    <div class="col-md-6">
                        <label for="img_path" class="form-label">Imagen:</label>
                        <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
                        @error('img_path')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                        
                        {{-- Vista previa de la imagen actual --}}
                        @if($producto->img_path)
                        <div class="mt-3">
                            <label class="form-label">Imagen actual:</label>
                            <div class="border rounded p-2">
                                <img src="{{ url('imagenes-productos/'.$producto->img_path) }}" 
                                     alt="{{$producto->nombre}}" 
                                     class="img-fluid img-thumbnail"
                                     style="max-height: 200px;"
                                     onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="text-muted" style="display: none;">
                                    <i class="fas fa-image-slash"></i> No se pudo cargar la imagen
                                </div>
                            </div>
                            <small class="text-muted">Nombre del archivo: {{ $producto->img_path }}</small>
                        </div>
                        @else
                        <div class="mt-2">
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Este producto no tiene imagen.</small>
                        </div>
                        @endif
                    </div>

                    <!---Marca---->
                    <div class="col-md-6">
                        <label for="marca_id" class="form-label">Marca:</label>
                        <select data-size="4" title="Seleccione una marca" data-live-search="true" name="marca_id" id="marca_id" class="form-control selectpicker show-tick">
                            @foreach ($marcas as $item)
                            @if ($producto->marca_id == $item->id)
                            <option selected value="{{$item->id}}" {{ old('marca_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                            @else
                            <option value="{{$item->id}}" {{ old('marca_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                            @endif
                            @endforeach
                        </select>
                        @error('marca_id')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Presentaciones---->
                    <div class="col-md-6">
                        <label for="presentacione_id" class="form-label">Presentación:</label>
                        <select data-size="4" title="Seleccione una presentación" data-live-search="true" name="presentacione_id" id="presentacione_id" class="form-control selectpicker show-tick">
                            @foreach ($presentaciones as $item)
                            @if ($producto->presentacione_id == $item->id)
                            <option selected value="{{$item->id}}" {{ old('presentacione_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                            @else
                            <option value="{{$item->id}}" {{ old('presentacione_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                            @endif
                            @endforeach
                        </select>
                        @error('presentacione_id')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Categorías---->
                    <div class="col-md-6">
                        <label for="categorias" class="form-label">Categorías:</label>
                        <select data-size="4" title="Seleccione las categorías" data-live-search="true" name="categorias[]" id="categorias" class="form-control selectpicker show-tick" multiple>
                            @foreach ($categorias as $item)
                            @if (in_array($item->id,$producto->categorias->pluck('id')->toArray()))
                            <option selected value="{{$item->id}}" {{ (in_array($item->id , old('categorias',[]))) ? 'selected' : '' }}>{{$item->nombre}}</option>
                            @else
                            <option value="{{$item->id}}" {{ (in_array($item->id , old('categorias',[]))) ? 'selected' : '' }}>{{$item->nombre}}</option>
                            @endif
                            @endforeach
                        </select>
                        @error('categorias')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Tipo de Producto---->
                    <div class="col-md-6">
                        <label for="tipo_producto" class="form-label">Tipo de Producto:</label>
                        <select name="tipo_producto" id="tipo_producto" class="form-control selectpicker show-tick">
                            <option value="comida" {{ old('tipo_producto', $producto->tipo_producto) == 'comida' ? 'selected' : '' }}>Comida</option>
                            <option value="bebida" {{ old('tipo_producto', $producto->tipo_producto) == 'bebida' ? 'selected' : '' }}>Bebida</option>
                            <option value="trago" {{ old('tipo_producto', $producto->tipo_producto) == 'trago' ? 'selected' : '' }}>Trago</option>
                        </select>
                        @error('tipo_producto')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Precio de Venta---->
                    <div class="col-md-6">
                        <label for="precio_venta" class="form-label">Precio de Venta:</label>
                        <input type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.01" min="0" value="{{old('precio_venta', $producto->precio_venta)}}">
                        @error('precio_venta')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Stock---->
                    <div class="col-md-6">
                        <label for="stock" class="form-label">Stock Actual:</label>
                        <input type="number" name="stock" id="stock" class="form-control" min="0" value="{{old('stock', $producto->stock)}}">
                        <small class="text-muted">Edita el stock manualmente si es necesario</small>
                        @error('stock')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Impuesto (IVA)---->
                    <div class="col-md-6">
                        <label for="impuesto" class="form-label">Impuesto (IVA):</label>
                        <select name="impuesto" id="impuesto" class="form-control selectpicker show-tick">
                            <option value="10" {{ old('impuesto', $producto->impuesto) == '10' ? 'selected' : '' }}>IVA 10%</option>
                            <option value="5" {{ old('impuesto', $producto->impuesto) == '5' ? 'selected' : '' }}>IVA 5%</option>
                            <option value="0" {{ old('impuesto', $producto->impuesto) == '0' ? 'selected' : '' }}>Exento (0%)</option>
                        </select>
                        @error('impuesto')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Aplica Descuento Trago---->
                    <div class="col-md-6" id="div_descuento_trago" style="display: none;">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="aplica_descuento_trago" id="aplica_descuento_trago" value="1" {{ old('aplica_descuento_trago', $producto->aplica_descuento_trago) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aplica_descuento_trago">
                                Aplica descuento de 5.000 Gs (Solo para tragos)
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="reset" class="btn btn-secondary">Reiniciar</button>
            </div>
        </form>
    </div>



</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        // Ejecutar al cargar
        toggleDescuentoTrago();

        // Ejecutar al cambiar
        $('#tipo_producto').change(function() {
            toggleDescuentoTrago();
        });

        function toggleDescuentoTrago() {
            var tipo = $('#tipo_producto').val();
            if (tipo === 'trago') {
                $('#div_descuento_trago').show();
            } else {
                $('#div_descuento_trago').hide();
                $('#aplica_descuento_trago').prop('checked', false);
            }
        }
    });
</script>
@endpush