@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('title','Realizar venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .producto-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        backface-visibility: hidden; /* Fix mostly for Chrome */
    }
    .producto-card:hover {
        transform: translateY(-4px); /* Move up slightly instead of scaling */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        background-color: #f8f9fa; /* Slight background highlight */
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Realizar Venta</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index')}}">Ventas</a></li>
        <li class="breadcrumb-item active">Realizar Venta</li>
    </ol>
</div>

<form action="{{ route('ventas.store') }}" method="post">
    @csrf
    <div class="container-lg mt-4">
        <div class="row gy-4">

            <!------venta producto---->
            <div class="col-xl-8">
                <div class="text-white bg-primary p-1 text-center">
                    Detalles de la venta
                </div>
                <div class="p-3 border border-3 border-primary">
                    <div class="row gy-4">

                        <!-----Producto---->
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <select name="producto_id" id="producto_id" class="form-control selectpicker" data-live-search="true" data-size="1" title="Busque un producto aquí">
                                        @foreach ($productos as $item)
                                        <option value="{{$item->id}}-{{$item->stock}}-{{$item->precio_venta}}-{{$item->impuesto}}">{{$item->codigo.' '.$item->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary ms-2" type="button" data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                                    <i class="fa-solid fa-search"></i> Ver Todo
                                </button>
                            </div>
                        </div>

                        <!-----Stock--->
                        <div class="d-flex justify-content-end">
                            <div class="col-12 col-sm-6">
                                <div class="row">
                                    <label for="stock" class="col-form-label col-4">Stock:</label>
                                    <div class="col-8">
                                        <input disabled id="stock" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-----Precio de venta---->
                        <div class="col-sm-4">
                            <label for="precio_venta" class="form-label">Precio de venta:</label>
                            <input disabled type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.1">
                        </div>

                        <!-----Cantidad---->
                        <div class="col-sm-4">
                            <label for="cantidad" class="form-label">Cantidad:</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control">
                        </div>

                        <!----Descuento---->
                        <div class="col-sm-4">
                            <label for="descuento" class="form-label">Descuento:</label>
                            <input type="number" name="descuento" id="descuento" class="form-control">
                        </div>

                        <!-----botón para agregar--->
                        <div class="col-12 text-end">
                            <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                        </div>

                        <!-----Tabla para el detalle de la venta--->
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tabla_detalle" class="table table-hover">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th class="text-white">Producto</th>
                                            <th class="text-white">Cantidad</th>
                                            <th class="text-white">Precio venta</th>
                                            <th class="text-white">Descuento</th>
                                            <th class="text-white">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th></th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Sumas</th>
                                            <th colspan="2"><span id="sumas">0</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">IVA %</th>
                                            <th colspan="2"><span id="igv">0</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Total</th>
                                            <th colspan="2"> <input type="hidden" name="total" value="0" id="inputTotal"> <span id="total">0</span></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!--Boton para cancelar venta--->
                        <div class="col-12">
                            <button id="cancelar" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Cancelar venta
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-----Venta---->
            <div class="col-xl-4">
                <div class="text-white bg-success p-1 text-center">
                    Datos generales
                </div>
                <div class="p-3 border border-3 border-success">
                    <div class="row gy-4">
                        <!--Cliente (Opcional)-->
                        <div class="col-12">
                            <label for="cliente_id" class="form-label">Cliente frecuente: <small class="text-muted">(Opcional)</small></label>
                            <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona" data-size='2'>
                                <option value="">-- Sin cliente --</option>
                                @foreach ($clientes as $item)
                                <option value="{{$item->id}}">{{$item->persona->razon_social}}</option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Nombre del Pedido (Opcional)-->
                        <div class="col-12">
                            <label for="nombre_pedido" class="form-label">Nombre del pedido: <small class="text-muted">(Opcional)</small></label>
                            <input type="text" name="nombre_pedido" id="nombre_pedido" class="form-control" placeholder="Ej: Juan, Mesa 5, etc.">
                            <small class="form-text text-muted">Para ventas rápidas sin cliente registrado</small>
                            @error('nombre_pedido')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Tipo de comprobante (Oculto - Solo Ticket)-->
                        <input type="hidden" name="comprobante_id" value="{{ $comprobantes->first()->id ?? 1 }}">



                        <!--Numero de ticket (Generado automáticamente)-->
                        <?php
                        $numeroTicket = 'T' . date('YmdHis'); // Ej: T20251125143022
                        ?>
                        <input type="hidden" name="numero_comprobante" value="{{$numeroTicket}}">


                        <!--Mesa (Opcional)-->
                        <div class="col-12">
                            <label for="numero_mesa" class="form-label">Mesa: <small class="text-muted">(Opcional)</small></label>
                            <input type="text" name="numero_mesa" id="numero_mesa" class="form-control" placeholder="Ej: Mesa 5, T1, etc.">
                            @error('numero_mesa')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Notas (Opcional)-->
                        <div class="col-12">
                            <label for="notas" class="form-label">Notas: <small class="text-muted">(Opcional)</small></label>
                            <textarea name="notas" id="notas" class="form-control" rows="2" placeholder="Instrucciones especiales..."></textarea>
                            @error('notas')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Forma de Pago-->
                        <div class="col-12">
                            <label for="forma_pago" class="form-label">Forma de Pago: *</label>
                            <select name="forma_pago" id="forma_pago" class="form-select" required>
                                <option value="efectivo" selected>💵 Efectivo</option>
                                <option value="tarjeta">💳 Tarjeta</option>
                                <option value="transferencia">🏦 Transferencia</option>
                            </select>
                            @error('forma_pago')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Impuesto---->
                        <div class="col-sm-6">
                            <label for="impuesto" class="form-label">Impuesto(IVA):</label>
                            <input readonly type="text" name="impuesto" id="impuesto" class="form-control border-success">
                            @error('impuesto')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Fecha--->
                        <div class="col-sm-6">
                            <label for="fecha" class="form-label">Fecha:</label>
                            <input readonly type="date" name="fecha" id="fecha" class="form-control border-success" value="<?php echo date("Y-m-d") ?>">
                            <?php

                            use Carbon\Carbon;

                            $fecha_hora = Carbon::now()->toDateTimeString();
                            ?>
                            <input type="hidden" name="fecha_hora" value="{{$fecha_hora}}">
                        </div>

                        <!----User--->
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                        <!--Botones--->
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success" id="guardar">Realizar venta</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar la venta -->
    </div>

    <!-- Modal para cancelar la venta -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Advertencia</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres cancelar la venta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btnCancelarVenta" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buscar Producto Avanzado -->
    <div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="inputBusquedaModal" class="form-control mb-3" placeholder="Buscar por nombre o código...">
                    <div class="container-fluid px-0">
                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2" id="contenedorProductosModal">
                            @foreach ($productos as $item)
                            <div class="col item-producto-modal">
                                <div class="card h-100 shadow-sm border-0 producto-card btn-seleccionar-modal" 
                                     data-value="{{$item->id}}-{{$item->stock}}-{{$item->precio_venta}}-{{$item->impuesto}}">
                                    
                                    <div class="position-relative" style="padding-top: 100%; overflow: hidden;">
                                        @if($item->img_path)
                                            <img src="{{ url('imagenes-productos/'.$item->img_path) }}" 
                                                 class="card-img-top producto-imagen" 
                                                 alt="{{$item->nombre}}" 
                                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="bg-secondary align-items-center justify-content-center text-white" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none;">
                                                <i class="fas fa-box-open fa-2x"></i>
                                            </div>
                                        @else
                                            <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                                <i class="fas fa-box-open fa-2x"></i>
                                            </div>
                                        @endif
                                        <span class="position-absolute top-0 end-0 badge rounded-pill bg-{{ $item->stock > 0 ? 'success' : 'danger' }} m-1" style="font-size: 0.65rem;">
                                            {{$item->stock}}
                                        </span>
                                    </div>

                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title mb-1 text-truncate nombre-producto small" title="{{$item->nombre}}">{{$item->nombre}}</h6>
                                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">{{$item->codigo}}</small>
                                        <div class="fw-bold text-primary" style="font-size: 0.9rem;">{{ number_format($item->precio_venta, 0, ',', '.') }} Gs</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="noResults" class="text-center mt-4" style="display: none;">
                            <p class="text-muted">No se encontraron productos</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {

        $('#producto_id').change(mostrarValores);


        $('#btn_agregar').click(function() {
            agregarProducto();
        });

        $('#btnCancelarVenta').click(function() {
            cancelarVenta();
        });

        disableButtons();

        $('#impuesto').val(impuesto + '%');
        
        // Lógica del buscador modal
        $('#inputBusquedaModal').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            var visibleItems = 0;
            
            $("#contenedorProductosModal .item-producto-modal").filter(function() {
                var text = $(this).find('.nombre-producto').text().toLowerCase() + " " + $(this).find('small').text().toLowerCase();
                var match = text.indexOf(value) > -1;
                $(this).toggle(match);
                if(match) visibleItems++;
            });

            if(visibleItems === 0) {
                $('#noResults').show();
            } else {
                $('#noResults').hide();
            }
        });

        // Seleccionar producto del modal (Click en la tarjeta)
        $(document).on('click', '.btn-seleccionar-modal', function() {
            var value = $(this).data('value');
            
            // Seleccionar en el dropdown principal
            $('#producto_id').val(value);
            $('#producto_id').selectpicker('refresh');
            
            // Disparar evento change para actualizar stock y precio
            mostrarValores();
            
            // Cerrar modal
            $('#modalBuscarProducto').modal('hide');
        });
    });

    //Variables
    let cont = 0;
    let subtotal = [];
    let ivas = []; // Array para guardar el IVA de cada línea
    let sumas = 0; // Total Venta (Precio Final)
    let igvAcumulado = 0; // Total Impuestos
    let total = 0; // Total a Pagar (igual a sumas en modelo IVA incluido)

    function mostrarValores() {
        let dataProducto = document.getElementById('producto_id').value.split('-');
        $('#stock').val(dataProducto[1]);
        $('#precio_venta').val(dataProducto[2]);
    }

    function agregarProducto() {
        let dataProducto = document.getElementById('producto_id').value.split('-');
        //Obtener valores de los campos
        let idProducto = dataProducto[0];
        let nameProducto = $('#producto_id option:selected').text();
        let cantidad = $('#cantidad').val();
        let precioVenta = $('#precio_venta').val();
        let descuento = $('#descuento').val();
        let stock = $('#stock').val();
        let impuestoProducto = parseFloat(dataProducto[3]) || 0; // 0, 5, 10

        if (descuento == '') {
            descuento = 0;
        }

        //Validaciones
        //1.Para que los campos no esten vacíos
        if (idProducto != '' && cantidad != '') {

            //2. Para que los valores ingresados sean los correctos
            if (parseInt(cantidad) > 0 && (cantidad % 1 == 0) && parseFloat(descuento) >= 0) {

                //3. Para que la cantidad no supere el stock
                if (parseInt(cantidad) <= parseInt(stock)) {
                    //Calcular valores
                    // Asumimos Precio Venta es IVA Incluido
                    let subtotalLinea = round(cantidad * precioVenta - descuento);
                    subtotal[cont] = subtotalLinea;
                    
                    // Calcular IVA de esta línea: Total - (Total / (1 + Tasa/100))
                    let ivaLinea = round(subtotalLinea - (subtotalLinea / (1 + (impuestoProducto / 100))));
                    ivas[cont] = ivaLinea;

                    sumas += subtotalLinea;
                    igvAcumulado += ivaLinea;
                    
                    // Total a pagar es la suma de los precios finales
                    total = sumas;
                    
                    // Base imponible (Sumas en la tabla visual)
                    let baseImponible = round(total - igvAcumulado);

                    //Crear la fila
                    let fila = '<tr id="fila' + cont + '">' +
                        '<th>' + (cont + 1) + '</th>' +
                        '<td><input type="hidden" name="arrayidproducto[]" value="' + idProducto + '">' + nameProducto + '</td>' +
                        '<td><input type="hidden" name="arraycantidad[]" value="' + cantidad + '">' + cantidad + '</td>' +
                        '<td><input type="hidden" name="arrayprecioventa[]" value="' + precioVenta + '">' + precioVenta + '</td>' +
                        '<td><input type="hidden" name="arraydescuento[]" value="' + descuento + '">' + descuento + '</td>' +
                        '<td>' + subtotal[cont] + '</td>' +
                        '<td><button class="btn btn-danger" type="button" onClick="eliminarProducto(' + cont + ')"><i class="fa-solid fa-trash"></i></button></td>' +
                        '</tr>';

                    //Acciones después de añadir la fila
                    $('#tabla_detalle').append(fila);
                    limpiarCampos();
                    cont++;
                    disableButtons();

                    //Mostrar los campos calculados
                    $('#sumas').html(baseImponible); // Mostrar Base Imponible
                    $('#igv').html(igvAcumulado);    // Mostrar Total Impuestos
                    $('#total').html(total);         // Mostrar Total a Pagar
                    $('#impuesto').val(igvAcumulado);
                    $('#inputTotal').val(total);
                } else {
                    showModal('Cantidad incorrecta');
                }

            } else {
                showModal('Valores incorrectos');
            }

        } else {
            showModal('Le faltan campos por llenar');
        }

    }

    function eliminarProducto(indice) {
        //Calcular valores
        sumas -= round(subtotal[indice]);
        igvAcumulado -= round(ivas[indice]);
        
        total = sumas;
        let baseImponible = round(total - igvAcumulado);

        //Mostrar los campos calculados
        $('#sumas').html(baseImponible);
        $('#igv').html(igvAcumulado);
        $('#total').html(total);
        $('#impuesto').val(igvAcumulado);
        $('#inputTotal').val(total);

        //Eliminar el fila de la tabla
        $('#fila' + indice).remove();

        disableButtons();
    }

    function cancelarVenta() {
        //Elimar el tbody de la tabla
        $('#tabla_detalle tbody').empty();

        //Añadir una nueva fila a la tabla
        let fila = '<tr>' +
            '<th></th>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '</tr>';
        $('#tabla_detalle').append(fila);

        //Reiniciar valores de las variables
        cont = 0;
        subtotal = [];
        sumas = 0;
        igv = 0;
        total = 0;

        //Mostrar los campos calculados
        $('#sumas').html(sumas);
        $('#igv').html(igv);
        $('#total').html(total);
        $('#impuesto').val(impuesto + '%');
        $('#inputTotal').val(total);

        limpiarCampos();
        disableButtons();
    }

    function disableButtons() {
        if (total == 0) {
            $('#guardar').hide();
            $('#cancelar').hide();
        } else {
            $('#guardar').show();
            $('#cancelar').show();
        }
    }

    function limpiarCampos() {
        let select = $('#producto_id');
        select.selectpicker('val', '');
        $('#cantidad').val('');
        $('#precio_venta').val('');
        $('#descuento').val('');
        $('#stock').val('');
    }

    function showModal(message, icon = 'error') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: icon,
            title: message
        })
    }

    function round(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }
    //Fuente: https://es.stackoverflow.com/questions/48958/redondear-a-dos-decimales-cuando-sea-necesario
</script>
@endpush