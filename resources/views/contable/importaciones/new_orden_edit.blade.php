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
</style>

<div style="background-color: white; border-radius: 15px; padding: 20px;">
    <div style="display: flex; justify-content: space-between;">

        <a href="{{ route('importaciones.new_orden_index') }}" class="bg-red-500 hover:bg-red-700 text-white hover:text-white font-semibold py-2 px-4 rounded duration-300 transition-all ">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar
        </a>
    </div>
    <br>
    <hr>
    <hr>
    <div class="row mt-4">
        <div class="col-md-8">
            <input type="hidden" id="id_orden" name="id_orden" value="{{$orden->id}}">
            <label for="descripcion" class="block mb-2 text-md font-bold text-gray-900">Descripcion</label>
            <input type="text" id="descripcion" value="{{ $orden->observacion }}" onchange="guardarCabecera()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    <div style="width: 100%">
        <div style="margin-top: 10px;">
            <div style="text-align: center;">
                <p style="font-weight: bold;">Productos</p>
            </div>
        </div>
        <div class="buscador_proforma">
            <div class="ancho">
                <!-- <input placeholder="Escriba por lo menos 3 caracteres..." type="text" id="buscador" class="form-control borde" onkeyup="buscador()"> -->
                <select style="width: 100%;" name="id_producto" id="id_producto" class="form-control select2_productos">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div style="width: 20%;">
                <button class="btn btn-success " onclick="agregar_det()"><i class="fa fa-plus"></i> Agregar</button>
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
                    <tr class="bg-blue-500 rounded text-white">
                        <th colspan='10' class="text-lg uppercase font-bold" style='text-align: center;'>Detalles</th>
                    </tr>
                    <tr>
                        <th width="20%">Cod. Producto</th>
                        <th width="40%">Nombre</th>
                        <th width="20%">Reg. Sanitario</th>
                        <th width="10%">Cantidad</th>
                        <th width="10%">Accion</th>
                    </tr>
                </thead>
                <tbody class="font-semibold" id="detalles">
                    @php echo $detalle["table"]; @endphp
                </tbody>
            </table>
        </div>
    </div>

</div>


<script>
    const popUpAlert = (msj, icon) => {
        let notificacion = $('body');
        notificacion.append(`
            <div class="notification">
               <p>${msj} ${icon}</p>
               <span class="notification__progress"></span>
            </div>`)
    }

    const guardarCabecera = () => {
        observacion = document.getElementById("descripcion").value
        $.ajax({
            url: `{{ route('importaciones.actualizar_cabecera') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id_orden': `{{ $orden->id }}`,
                'observacion': observacion
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

    const agregar_det = () => {
        let id_producto = document.getElementById('id_producto').value
        if (id_producto == '') {
            return alertas("error", "Error...", "Seleccione un producto")
        }
        let detalles = $('#detalles')
        $.ajax({
            url: `{{ route('importaciones.guardar_detalles') }}`,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            beforeSend: function() {
                detalles.empty()
                detalles.append(`<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>`)
            },
            data: {
                'id_orden': `{{$orden->id}}`,
                'id_producto': id_producto
            },
            success: function(data) {
                if (data.status == 'success') {
                    detalles.empty()
                    detalles.append(data.table)
                    return;
                }
                let htmlCssError = `
                    <div style="text-align: center;color: #e64942;">
                    <h4 style="font-weight: 600;"><i class="fa fa-times-circle" aria-hidden="true"></i> ${data.msj}</h4>
                    </div>`;
                detalles.empty().append(htmlCssError);
            },
            error: function(data) {
                console.log(data)
            }
        })
    }

    const eliminarDetalle = (id_detalle) => {
        $.ajax({
            url: `{{ route('importaciones.eliminar_detalle') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id_detalle': id_detalle,
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


    const editar_fila = (id_detalle) => {
        let cantidad = document.getElementById(`cantidad_${id_detalle}`).value;
        let detalles = $('#detalles')
        cantidad = cantidad == '' ? 0 : cantidad;

        $.ajax({
            url: `{{ route('importaciones.editar_detalle') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            },
            data: {
                'id_orden': `{{ $orden->id }}`,
                'id_detalle': id_detalle,
                'cantidad': cantidad,
            },
            success: function(data) {
                if (data.status == 'success') {
                    detalles.empty()
                    detalles.append(data.table)
                    return;
                }

                let htmlCssError = `
                    <div style="text-align: center;color: #e64942;">
                    <h4 style="font-weight: 600;"><i class="fa fa-times-circle" aria-hidden="true"></i> ${data.msj}</h4>
                    </div>`;
                detalles.empty().append(htmlCssError);
            },
            error: function(data) {

            }
        })
    }
</script>

<script>
    window.onload = function() {
        setTimeout(() => {
            $(".select2_cliente").trigger("changue");
        }, 1500);
    }

    $('.select2_productos').select2({
        tags: false
    });
    $('.select2_productos').select2({
        placeholder: "{{trans('contableM.selecproducto')}}...",
        allowClear: true,
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: '{{route("importaciones.productos")}}',
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
</script>

@endsection