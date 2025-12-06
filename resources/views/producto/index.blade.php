@extends('layouts.app')

@section('title','Productos')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Estilos para el panel de filtros */
    .card-header {
        transition: all 0.3s ease;
    }
    
    .card-header:hover {
        opacity: 0.9;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        margin-right: 0.25rem;
    }
</style>
@endpush

@section('content')

@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">📦 Productos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol>

    @can('crear-producto')
    <div class="mb-4">
        <a href="{{route('productos.create')}}">
            <button type="button" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Añadir Nuevo Producto
            </button>
        </a>
    </div>
    @endcan

    {{-- Panel de Filtros Avanzados --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtrosProductos">
            <div>
                <i class="fas fa-filter me-2"></i>
                <strong>Filtros de Búsqueda</strong>
            </div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="collapse show" id="filtrosProductos">
            <div class="card-body">
                <form method="GET" action="{{ route('productos.index') }}">
                    <div class="row g-3">
                        {{-- Búsqueda por Nombre o Código --}}
                        <div class="col-md-4">
                            <label for="buscar" class="form-label">
                                <i class="fa-solid fa-magnifying-glass"></i> Buscar Producto
                            </label>
                            <input type="text" class="form-control" id="buscar" name="buscar" 
                                   placeholder="Nombre o código del producto..." 
                                   value="{{ request('buscar') }}">
                        </div>

                        {{-- Tipo de Producto --}}
                        <div class="col-md-4">
                            <label for="tipo_producto" class="form-label">
                                <i class="fa-solid fa-layer-group"></i> Tipo de Producto
                            </label>
                            <select class="form-select" id="tipo_producto" name="tipo_producto">
                                <option value="">Todos los tipos</option>
                                <option value="comida" {{ request('tipo_producto') == 'comida' ? 'selected' : '' }}>Comida</option>
                                <option value="bebida" {{ request('tipo_producto') == 'bebida' ? 'selected' : '' }}>Bebida</option>
                                <option value="trago" {{ request('tipo_producto') == 'trago' ? 'selected' : '' }}>Trago</option>
                            </select>
                        </div>

                        {{-- Categoría --}}
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">
                                <i class="fa-solid fa-tag"></i> Categoría
                            </label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->caracteristica->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Marca --}}
                        <div class="col-md-3">
                            <label for="marca_id" class="form-label">
                                <i class="fa-solid fa-bullhorn"></i> Marca
                            </label>
                            <select class="form-select" id="marca_id" name="marca_id">
                                <option value="">Todas las marcas</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->caracteristica->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Presentación --}}
                        <div class="col-md-3">
                            <label for="presentacione_id" class="form-label">
                                <i class="fa-solid fa-box-archive"></i> Presentación
                            </label>
                            <select class="form-select" id="presentacione_id" name="presentacione_id">
                                <option value="">Todas las presentaciones</option>
                                @foreach($presentaciones as $presentacion)
                                    <option value="{{ $presentacion->id }}" {{ request('presentacione_id') == $presentacion->id ? 'selected' : '' }}>
                                        {{ $presentacion->caracteristica->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Stock Bajo --}}
                        <div class="col-md-3">
                            <label for="stock_bajo" class="form-label">
                                <i class="fa-solid fa-box-open"></i> Stock Bajo (≤)
                            </label>
                            <input type="number" class="form-control" id="stock_bajo" name="stock_bajo" 
                                   placeholder="Ej: 10" min="0"
                                   value="{{ request('stock_bajo') }}">
                        </div>

                        {{-- Estado --}}
                        <div class="col-md-3">
                            <label for="estado" class="form-label">
                                <i class="fa-solid fa-toggle-on"></i> Estado
                            </label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Eliminado</option>
                            </select>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>

                    {{-- Resumen de filtros activos --}}
                    @if(request()->hasAny(['buscar', 'tipo_producto', 'categoria_id', 'marca_id', 'presentacione_id', 'stock_bajo', 'estado']))
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fa-solid fa-info-circle"></i> 
                            <strong>Filtros activos:</strong>
                            @if(request('buscar'))
                                <span class="badge bg-secondary">Búsqueda: "{{ request('buscar') }}"</span>
                            @endif
                            @if(request('tipo_producto'))
                                <span class="badge bg-info">Tipo: {{ ucfirst(request('tipo_producto')) }}</span>
                            @endif
                            @if(request('categoria_id'))
                                <span class="badge bg-warning text-dark">Categoría: {{ $categorias->find(request('categoria_id'))->caracteristica->nombre ?? 'N/A' }}</span>
                            @endif
                            @if(request('marca_id'))
                                <span class="badge bg-primary">Marca: {{ $marcas->find(request('marca_id'))->caracteristica->nombre ?? 'N/A' }}</span>
                            @endif
                            @if(request('presentacione_id'))
                                <span class="badge bg-success">Presentación: {{ $presentaciones->find(request('presentacione_id'))->caracteristica->nombre ?? 'N/A' }}</span>
                            @endif
                            @if(request('stock_bajo'))
                                <span class="badge bg-danger">Stock ≤ {{ request('stock_bajo') }}</span>
                            @endif
                            @if(request('estado') !== null && request('estado') !== '')
                                <span class="badge {{ request('estado') == '1' ? 'bg-success' : 'bg-danger' }}">
                                    {{ request('estado') == '1' ? 'Activo' : 'Eliminado' }}
                                </span>
                            @endif
                        </small>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-table me-1"></i>
                Listado de Productos
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover mb-0 fs-6">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th class="d-none-mobile">Marca</th>
                        <th class="d-none-mobile">Presentación</th>
                        <th class="d-none-mobile">Categorías</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $item)
                    <tr>
                        <td>
                            {{$item->codigo}}
                        </td>
                        <td>
                            {{$item->nombre}}
                        </td>
                        <td class="d-none-mobile">
                            {{$item->marca->caracteristica->nombre}}
                        </td>
                        <td class="d-none-mobile">
                            {{$item->presentacione->caracteristica->nombre}}
                        </td>
                        <td class="d-none-mobile">
                            @foreach ($item->categorias as $category)
                            <div class="container" style="font-size: small;">
                                <div class="row">
                                    <span class="m-1 rounded-pill p-1 bg-secondary text-white text-center">{{$category->caracteristica->nombre}}</span>
                                </div>
                            </div>
                            @endforeach
                        </td>
                        <td>
                            @if ($item->estado == 1)
                            <span class="badge rounded-pill text-bg-success">activo</span>
                            @else
                            <span class="badge rounded-pill text-bg-danger">eliminado</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Acciones">
                                @can('editar-producto')
                                <a href="{{route('productos.edit',['producto' => $item])}}" class="btn btn-warning btn-sm text-white" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                @endcan

                                @can('ver-producto')
                                <button type="button" class="btn btn-info btn-sm text-white btn-ver-producto" 
                                        data-nombre="{{$item->nombre}}"
                                        data-descripcion="{{$item->descripcion}}"
                                        data-vencimiento="{{$item->fecha_vencimiento}}"
                                        data-stock="{{$item->stock}}"
                                        data-img="{{ $item->img_path ? url('imagenes-productos/'.$item->img_path) : '' }}"
                                        title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @endcan

                                @can('eliminar-producto')
                                    @if ($item->estado == 1)
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Eliminar">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Restaurar">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                    @endif
                                @endcan
                            </div>




                    <!-- Modal de confirmación-->
                    <div class="modal fade" id="confirmModal-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Mensaje de confirmación</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ $item->estado == 1 ? '¿Seguro que quieres eliminar el producto?' : '¿Seguro que quieres restaurar el producto?' }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <form action="{{ route('productos.destroy',['producto'=>$item->id]) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Confirmar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Genérico para Ver Detalles -->
<div class="modal fade" id="modalVerProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalVerTitulo">Detalles del producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <p><span class="fw-bolder">Descripción: </span><span id="modalVerDesc"></span></p>
                    </div>
                    <div class="col-12">
                        <p><span class="fw-bolder">Fecha de vencimiento: </span><span id="modalVerVenc"></span></p>
                    </div>
                    <div class="col-12">
                        <p><span class="fw-bolder">Stock: </span><span id="modalVerStock"></span></p>
                    </div>
                    <div class="col-12">
                        <p class="fw-bolder">Imagen:</p>
                        <div id="modalVerImgContainer">
                            <img id="modalVerImg" src="" alt="" class="img-fluid img-thumbnail border border-4 rounded" style="display: none;">
                            <div id="modalVerImgError" class="text-muted" style="display: none;">
                                <i class="fas fa-image-slash"></i> No se pudo cargar la imagen
                            </div>
                            <div id="modalVerImgPlaceholder" class="text-muted" style="display: none;">
                                <i class="fas fa-box-open"></i> Sin imagen
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
<script>
    $(document).ready(function() {
        // Move modal to body to avoid z-index/clipping issues with the sidebar layout
        $('#modalVerProducto').appendTo("body");

        // Event delegation para botones en el datatable
        $(document).on('click', '.btn-ver-producto', function() {
            var nombre = $(this).data('nombre');
            var desc = $(this).data('descripcion');
            var venc = $(this).data('vencimiento');
            var stock = $(this).data('stock');
            var imgUrl = $(this).data('img');
            
            // Set Text
            $('#modalVerTitulo').text('Detalles del producto: ' + nombre);
            $('#modalVerDesc').text(desc);
            $('#modalVerVenc').text(venc ? venc : 'No tiene');
            $('#modalVerStock').text(stock);
            
            // Set Image
            var img = $('#modalVerImg');
            var imgError = $('#modalVerImgError');
            var imgPlaceholder = $('#modalVerImgPlaceholder');
            
            img.hide();
            imgError.hide();
            imgPlaceholder.hide();
            
            if (imgUrl) {
                img.attr('src', imgUrl);
                img.show();
                
                // Reset error handler
                img.off('error').on('error', function() {
                    $(this).hide();
                    imgError.show();
                });
            } else {
                imgPlaceholder.show();
            }
            
            // Show Modal - Reuse instance if exists
            var modalEl = document.getElementById('modalVerProducto');
            var myModal = bootstrap.Modal.getInstance(modalEl);
            if (!myModal) {
                myModal = new bootstrap.Modal(modalEl);
            }
            myModal.show();
        });
    });
</script>
@endpush