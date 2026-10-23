@extends('insumos.proforma.base')
@section('action-content')
<style>
    /* Clases para el buscador */
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .lds-ellipsis div {
        position: absolute;
        top: 33px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: var(--colorPrimario);
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }

    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(0);
        }
    }

    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }

        100% {
            transform: translate(24px, 0);
        }
    }

    /* Fin de las clases para el buscador */
    .ancho {
        width: 100%;
    }

    @media (min-width: 768px) {
        .ancho {
            width: 40%;
        }
    }

    .borde {
        border-radius: 6px;
    }

    .btn-general {
        font-size: 17px;
        border-radius: 5px;
        padding: 1px 20px;
        background: transparent;
        transition: all 0.4s ease-in-out;
    }

    .btn-eliminar {
        border: 3px solid #dd4b39;
        color: #dd4b39;
    }

    .btn-editar {
        border: 3px solid #f39c12;
        color: #f39c12;
    }

    .btn-eliminar:hover {
        color: white;
        background: #dd4b39;
    }

    .btn-editar:hover {
        color: white;
        background-color: #f39c12;
    }

    .swal2-icon.swal2-warning {
        font-size: 37px !important;
    }

    .swal2-icon-content {
        font-size: 102px;
    }

    .buscador_proforma {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    @media (max-width: 666px) {
        .buscador_proforma {
            flex-direction: column;
        }
    }


    .notification {
        position: fixed;
        bottom: 50px;
        left: 50px;
        width: max-content;
        padding: 20px 15px;
        border-radius: 4px;
        background-color: #141619;
        color: #f6f5f9;
        box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
        transform: translateY(30px);
        opacity: 0;
        visibility: hidden;
        animation: fade-in 4s linear forwards;
    }

    .notification__progress {
        position: absolute;
        left: 5px;
        bottom: 5px;
        width: calc(100% - 10px);
        height: 3px;
        transform: scaleX(0);
        transform-origin: left;
        background-image: linear-gradient(to right, #539bdb, #3250bf);
        border-radius: inherit;
        animation: load 3s 0.25s linear forwards;
    }

    @keyframes fade-in {
        5% {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        90% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes load {
        to {
            transform: scaleX(1);
        }
    }

    input[type=checkbox] {
        scale: 1.8;
    }
</style>
<div style="background-color: white; border-radius: 15px; padding: 20px;">
    <div style="display: flex; justify-content: space-between; ">
        <div class="grid w-full">
            <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
                PROFORMA
            </h3>
            <input name="secuencia" onchange="handlePublica(this)" class="form-control borde md:w-[30%]" type="text" value="{{$proforma->secuencia }}">
        </div>

        <a href="{{ route('insumos.proformaInsumos.index') }}">
            <button class="bg-red-500 hover:bg-red-700 text-white hover:text-white font-semibold py-2 px-4 rounded duration-300 transition-all flex gap-3 items-center">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar
            </button>
        </a>
    </div>
    <br>
    <hr>
    <hr>
    <div class="row mt-4">
        <div class="col-md-3">
            <div style="width:100%">
                <label for="">Cliente</label>
            </div>
            <select style="width: 100%;" name="id_cliente" id="id_cliente" class="form-control select2_cliente" onchange="guardarCabecera(1)">
                <option value="">Seleccione...</option>
                @if(isset($proforma->cliente))
                <option selected value="{{$proforma->cliente->identificacion}}">{{$proforma->cliente->nombre}}</option>
                @endif
            </select>
        </div>
        <div class="col-md-3">
            <label for="">Correo Cliente</label>
            <input type="text" id="correo_cliente" name="correo_cliente" class="form-control borde" value="{{ $proforma->correo_cliente }}" onchange="handlePublica(this)">
        </div>
        <div class="col-md-3">
            <label for="">Fecha Exp:</label>
            <input type="date" id="fecha_exp" class="form-control borde" value="{{ $proforma->fecha_exp }}" onchange="guardarCabecera(3)">
        </div>

        <div class="col-md-3">
            <label for="">Tipo necesidad:</label>
            <input type="text" name="tipo_necesidad" id="tipo_necesidad" class="form-control borde" value="{{ $proforma->tipo_necesidad }}" onchange="handlePublica(this)">
        </div>

        <div class="col-md-3">
            <label for="">Codigo CPC:</label>
            <input type="text" name="codigo_cpc" id="codigo_cpc" class="form-control borde" value="{{ $proforma->codigo_cpc }}" onchange="handlePublica(this)">
        </div>

        <div class="col-md-3">
            <label for="">Cod. de contratacion:</label>
            <input type="text" name="cod_contratacion" id="cod_contratacion" class="form-control borde" value="{{ $proforma->cod_contratacion }}" onchange="handlePublica(this)">
        </div>

        <div class="col-md-3">
            <div style="width:100%">
                <label for="">Responsable</label>
            </div>
            <select style="width: 100%;" name="id_responsable" id="id_responsable" class="form-control select2_responsable" onchange="handlePublica(this)">
                <option value="">Seleccione...</option>
                @if(isset($proforma->responsable))
                <option selected value="{{$proforma->responsable->id}}">{{$proforma->responsable->nombre1}} {{$proforma->responsable->apellido1}} </option>
                @endif
            </select>
        </div>

        <div class="col-md-3">
            <div style="width:100%">
                <label for="mostrar">Mostrar</label>
            </div>
            <input type="checkbox" name="mostrar" id="mostrar" onchange="handlePublica(this)" @if($proforma->mostrar == 1) checked @endif>
        </div>


        <div class="col-md-12">
            <div class="">
                <label for="descripcion" class="block mb-2 text-md font-bold text-gray-900">Descripcion</label>
                <input type="text" id="descripcion" value="{{ $proforma->descripcion }}" onchange="guardarCabecera(2)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>
    </div>
    <div style="width: 100%">
        <div style="margin-top: 10px;">
            <div style="text-align: center;">
                <p style="font-weight: bold;">Buscador</p>
            </div>
        </div>
        <div class="buscador_proforma">
            <div class="ancho">
                <abbr title="CODIGO PRODUCTO, NOMBRE PRODUCTO">
                    <p><i class="fa fa-info-circle" aria-hidden="true"></i></p>
                </abbr>
                <input placeholder="Escriba por lo menos 3 caracteres..." type="text" id="buscador" class="form-control borde" onkeyup="buscador()">
            </div>
        </div>
    </div>
    <div style="width: 100%; margin-top: 20px;">
        <div id="contenedor_buscador" style="text-align: center; font-size: 12px;  overflow-y: scroll;scroll-behavior: smooth;">

        </div>
    </div>

    <div style="width: 100%; margin-top: 20px;">
        <div style="text-align: center; font-size: 12px; overflow-y: scroll;scroll-behavior: smooth;">
            <table class='table table-bordered table-striped table-hover'>
                <thead>
                    <tr>
                        <th colspan="12" class="text-center w-full bg-blue-500 rounded text-white text-lg uppercase font-bold p-2">
                            Detalles
                        </th>
                    </tr>
                    <tr>
                        <th>Cod. Producto</th>
                        <th>Nombre</th>
                        <th>Registro Sanitario</th>
                        @if($proforma->mostrar == 1)
                        <th>Espef. Tecnica</th>
                        <th>Espef. Tecnica Ofertada</th>
                        @endif
                        <th>Cantidad</th>
                        <th>Presentación</th>
                        <th>Precio</th>
                        <th>Descuento</th>
                        <th>Iva</th>
                        <th>Total</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody class="font-semibold" id="detalles">
                    @php echo $detalle["table"]; @endphp
                </tbody>
            </table>
        </div>
    </div>


    <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-5 mb-10">
        <div class="">
            <label for="diasPlazo" class="block mb-2 text-sm font-semibold text-gray-900">Dias Plazo</label>
            <input type="number" id="diasPlazo" value="{{ is_null($proforma->dias_plazo) ? 30 : $proforma->dias_plazo }}" onchange="handlePublica(this)" name="dias_plazo" value="30" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>

        <div class="">
            <p class="block mb-2 text-sm font-semibold text-gray-900">Forma de pago</p>
            <input onchange="handlePublica(this)" type="text" name="forma_pago" value="{{ $proforma->forma_pago }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>

        <div class="">
            <p class="block mb-2 text-sm font-semibold text-gray-900 truncate">Tiempo de entrega (Dias habiles)</p>
            <input onchange="handlePublica(this)" type="number" name="tiempo_entrega" value="{{ $proforma->tiempo_entrega }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>

        <div class="">
            <p class="block mb-2 text-sm font-semibold text-gray-900">Garantia</p>
            <input onchange="handlePublica(this)" type="text" name="garantia" value="{{ $proforma->garantia }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>

        <div class="">
            <p class="block mb-2 text-sm font-semibold text-gray-900">Vigencia de la oferta</p>
            <input onchange="handlePublica(this)" type="text" name="vigencia_oferta" value="{{ $proforma->vigencia_oferta }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>

        <div class="">
            <p class="block mb-2 text-sm font-semibold text-gray-900">Procedencia</p>
            <input onchange="handlePublica(this)" type="text" name="procedencia" id="procedencia" value="{{ $proforma->procedencia }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div class="">
            <p class="block mb-2 text-sm font-semibold text-gray-900">Marca</p>
            <input onchange="handlePublica(this)" type="text" name="marca" id="marca" value="{{ $proforma->marca }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>

        <div class="col-span-2 sm:col-span-3 md:col-span-5">
            <p class="font-semibold whitespace-nowrap">Observaci&oacute;n Adicional</p>
            <textarea onchange="handlePublica(this)" type="text" name="obs_adicional" id="obs_adicional" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ $proforma->obs_adicional }}</textarea>
        </div>
    </div>

    @if(!is_null($permiso_aprobado) or Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
    @if($proforma->estado == 2)
    <div style="text-align: center;" class="mt-3">
        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded capitalize transition-all duration-500 shadow-2xl" onclick="aprobarProforma(`{{$proforma->id}}`, 1)">
            <i class="fa fa-check" aria-hidden="true"></i> Aprobar
        </button>
    </div>
    @endif
    @endif

    @if($proforma->pre_aprobada == 0)
    <div style="text-align: center;" class="mt-3">
        <button type="button" class="bg-cyan-500 hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded capitalize transition-all duration-500 shadow-2xl" onclick="preaprobarProforma()">
            <i class="fa fa-check" aria-hidden="true"></i> Pre aprobar
        </button>
    </div>
    @endif

</div>
<script>
    const handlePublica = (e) => {
        let value;
        if (e.type === 'checkbox') {
            value = e.checked ? 1 : 0;
        } else {
            value = e.value;
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: `{{ route('insumos.proformaInsumos.updatePublica') }}`,
            data: {
                column: e.name,
                value: value,
                id_proforma: `{{$proforma->id}}`
            },
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            success: function(data) {
                console.log(data)
            },
            error: function(data) {
                console.log(data)
            }
        })
    }

    const handleDetail = (e, id) => {
        $.ajax({
            type: "post",
            dataType: "json",
            url: `{{ route('insumos.proformaInsumos.updatePublicaDetails') }}`,
            data: {
                column: e.name,
                value: e.value,
                id: id
            },
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            success: function(data) {
                if (data.table) {
                    $('#detalles').empty().append(data.table)
                }
            },
            error: function(data) {
                console.log(data)
            }
        })
    }
</script>

<script>
    const popUpAlert = (msj, icon) => {
        let notificacion = $('body');
        notificacion.append(`
            <div class="notification">
               <p>${msj} ${icon}</p>
               <span class="notification__progress"></span>
            </div>`)
    }

    const guardarCabecera = (tipo) => {


        let valor = '';
        if (tipo == 1) {
            valor = document.getElementById("id_cliente").value
        } else if (tipo == 2) {
            valor = document.getElementById("descripcion").value
        } else if (tipo == 3) {
            valor = document.getElementById("fecha_exp").value
        } else if (tipo == 5) {
            valor = document.getElementById("correo_cliente").value
        } else {
            valor = document.getElementById("diasPlazo").value
        }

        $.ajax({
            url: `{{ route('insumos.proformaInsumos.updateCabecera') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id_proforma': `{{ $proforma->id }}`,
                'tipo': tipo,
                'valor': valor
            },
            success: function(data) {
                console.log(data)
                if (data.status == "error") {
                    let notificacion = document.querySelectorAll(".notification").remove()
                    popUpAlert(data.msj, '❌')
                }
            },
            error: function(data) {

            }
        })
    }

    const buscador = () => {
        // let bodega = document.getElementById('id_bodega')
        let buscador = document.getElementById('buscador')

        if (buscador.value.trim().length < 3) {
            $('#contenedor_buscador').empty()
            return;
        }

        // if (bodega.value == "") {
        //     bodega.style.border = "1px solid red";
        //     return;
        // } else {
        //     bodega.style.border = "1px solid #d2d6de";
        // }

        let contenedor_buscador = $('#contenedor_buscador')

        $.ajax({
            url: `{{ route('insumos.proformaInsumos.proformaSinInventarioBuscador') }}`,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'buscador': buscador.value.trim(),
                // 'bodega': bodega.value
            },
            beforeSend: function() {
                contenedor_buscador.empty()
                contenedor_buscador.append(`<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>`)
            },
            success: function(data) {
                console.log(data)

                if (data.status == 'success') {

                    contenedor_buscador.empty()
                    contenedor_buscador.append(data.table)
                    if ($('#mostrar').is(":checked")) {
                        $('.ocultar').show();
                    } else {
                        $('.ocultar').hide();
                    }
                    return;
                }

                let htmlCssError = `
            <div style="text-align: center;color: #e64942;">
               <h4 style="font-weight: 600;"><i class="fa fa-times-circle" aria-hidden="true"></i> ${data.msj}</h4>
            </div>`;
                contenedor_buscador.empty().append(htmlCssError);
            },
            error: function(data) {

            }
        })

    }

    const agregarDetalle = (id_inventario) => {

        if (validarDatos()) {
            return alertas("error", "Error..", "Faltan campos por llenar")
        }

        let especificacion_tecnica = document.getElementById('espec_tecnica_' + id_inventario).value;
        let especificacion_tecnica_ofertada = document.getElementById('espec_tecnica_ofertada_' + id_inventario).value;

        // let existencia = document.getElementById('existencia_' + id_inventario).value;
        let cantidad = document.getElementById('cantidad_' + id_inventario);
        let precio = document.getElementById('precio_' + id_inventario).value
        let descuento = document.getElementById('descuento_' + id_inventario).value
        let iva = document.getElementById('iva_' + id_inventario).value

        let presentacion = document.getElementById('presentacion_' + id_inventario).value;

        if (cantidad.value <= 0) {
            alertas('error', "Error...", "Cantidad incorreta")
            return;
        }


        let detalles = $('#detalles')

        $.ajax({
            url: `{{ route('insumos.proformaInsumos.updateDetailsSinInventario') }}`,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            beforeSend: function() {
                detalles.empty()
                detalles.append(`<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>`)
            },
            data: {
                'id': `{{$proforma->id}}`,
                'id_producto': id_inventario,
                'cantidad': cantidad.value,
                'precio': precio,
                'descuento': descuento,
                'iva': iva,
                'accion': 1,
                especificacion_tecnica_ofertada,
                especificacion_tecnica,
                presentacion
            },
            success: function(data) {
                if (data.status == 'success') {
                    detalles.empty()
                    detalles.append(data.table)
                    return;
                }
                popUpAlert(data.msj, '❌')

            },
            error: function(data) {
                console.log(data)
            }
        })
    }

    const calcularProducto = (id_inventario) => {
        let cantidad = document.getElementById('cantidad_' + id_inventario).value;
        let precio = document.getElementById('precio_' + id_inventario).value;
        let decuenta = document.getElementById('descuento_' + id_inventario).value;
        let iva = document.getElementById('iva_' + id_inventario).value
        let calcIva = ((cantidad * precio) - descuento) * iva;
        let total = ((cantidad * precio) - descuento) + calcIva;
    }

    const eliminarDetalle = (id_detalle) => {
        $.ajax({
            url: `{{ route('insumos.proformaInsumos.updateDetailsSinInventario') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },

            data: {
                'id_detalle': id_detalle,
                'accion': 0
            },
            success: function(data) {
                if (data.status == "success") {
                    document.getElementById("fila_" + id_detalle).remove();
                    return;
                }
                alertas("error", "Error...", data.msj.toUpperCase());
            },
            error: function(data) {

            }
        })
    }

    const editFila = (e, id_detalle) => {

    }

    // const editFila = (id_detalle, id_serie) => {
    //     let precio = document.getElementById(`precio_${id_detalle}_${id_serie}`).value;
    //     let descuento = document.getElementById(`descuento_${id_detalle}_${id_serie}`).value;



    //     let especificacion_tecnica = document.getElementById(`espec_tecnica_${id_detalle}_${id_serie}`);
    //     let especificacion_tecnica_ofertada = document.getElementById(`espec_tecnica_ofertada_${id_detalle}_${id_serie}`);

    //     especificacion_tecnica = especificacion_tecnica ? especificacion_tecnica.value : "";
    //     especificacion_tecnica_ofertada = especificacion_tecnica_ofertada ? especificacion_tecnica_ofertada.value : "";

    //     let detalles = $('#detalles')

    //     precio = precio == '' ? 0 : precio;
    //     descuento = descuento == '' ? 0 : descuento;

    //     $.ajax({
    //         url: `{{ route('insumos.proformaInsumos.editarFila') }}`,
    //         type: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': `{{ csrf_token() }}`
    //         },
    //         data: {
    //             id_detalle,
    //             precio,
    //             descuento,
    //             "espec_tecnica": especificacion_tecnica,
    //             "espec_tecnica_ofertada": especificacion_tecnica_ofertada
    //         },
    //         success: function(data) {
    //             if (data.status == 'success') {
    //                 detalles.empty()
    //                 detalles.append(data.table)
    //                 popUpAlert(data.msj, '✔')
    //                 return;
    //             }

    //             let htmlCssError = `
    //         <div style="text-align: center;color: #e64942;">
    //            <h4 style="font-weight: 600;"><i class="fa fa-times-circle" aria-hidden="true"></i> ${data.msj}</h4>
    //         </div>`;
    //             detalles.empty().append(htmlCssError);
    //             popUpAlert(data.msj, '❌')
    //             return;
    //         },
    //         error: function(data) {

    //         }
    //     })
    // }

    const preaprobarProforma = () => {
        $.ajax({
            type: "post",
            url: `{{ route('insumos.proformaInsumos.preaprobar') }}`,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                id_proforma: `{{ $proforma->id }}`
            },
            success: function(data) {
                if (data.status == 'error') {
                    return alertas('error', 'Error...', data.msj)
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito',
                        text: data.msj,
                    }).then(() => {
                        window.location.reload();
                    })
                }
            },
            error: function(data) {
                console.log(data)
            }
        })
    }
</script>

<script>
    window.onload = function() {
        setTimeout(() => {
            $(".select2_cliente").trigger("changue");
            $(".select2_responsable").trigger("changue");
        }, 1500);
    }

    $('.select2_cliente').select2({
        tags: false
    });
    $('.select2_cliente').select2({
        placeholder: `Seleccione un cliente...`,
        allowClear: true,
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: `{{ route('insumos.proformaInsumos.buscadorClientes') }}`,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    $('.select2_cliente').on('select2:select', function(e) {
        let selectedElement = e.params.data;
        document.getElementById("correo_cliente").value = selectedElement.correo;
        guardarCabecera(5);
    });


    $('.select2_responsable').select2({
        tags: false
    });
    $('.select2_responsable').select2({
        placeholder: `Seleccione un responsable...`,
        allowClear: true,
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: `{{ route('insumos.proformaInsumos.buscadorResponsable') }}`,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });



    document.getElementById('mostrar').addEventListener('change', (event) => {
        if (event.currentTarget.checked) {
            $('.ocultar').hide();
            location.reload(true);
        } else {
            $('.ocultar').show();
            location.reload(true);
        }
    });
</script>

@endsection