@extends('insumos.proforma.base')
@section('action-content')

<style>
    :root {
        --color: transparent linear-gradient(90deg, #2A79C6 0%, #115596 100%) 0% 0% no-repeat padding-box !important;
    }

    .emitido {
        background-color: #00a65a;
        font-weight: 600;
        color: #ffffff;
    }

    .pendiente {
        background-color: #f39c12;
        font-weight: 600;
        color: #ffffff;
    }

    .anulado {
        background-color: #dd4b39;
        font-weight: 600;
        color: #ffffff;
    }

    .swal2-icon.swal2-warning {
        font-size: 37px !important;
    }

    .swal2-icon-content {
        font-size: 102px;
    }

    .swal2-warning {
        margin: 16px !important;
    }

    .contenedor-valores {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 5px 20px;
        width: 100%;
        text-align: center;
    }

    .contenedor-valores>p {
        margin: 0px;
        font-weight: 600;
        color: white;
    }

    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(20em, 1fr));
        gap: 20px;
        margin-top: 0px;
        padding: 0px 10px;
    }

    .grid-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(5em, 1fr));
        gap: 5px;
        margin-top: 0px;
    }
</style>

<div class="modal fade bd-example-modal-lg rounded" id="modal_proforma" aria-labelledby="myModalLabelGuia" style="width: 100%;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content" id="cargar_modal" style="width: 100%;">

        </div>
    </div>
</div>

<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Ordenes Importaciones</h3>

        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-end md:w-auto w-full">
                <button type="button" onclick="crear_orden()" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-4 py-2 transition-all duration-300  hover:bg-green-700"> <i class="fa fa-plus" aria-hidden="true"></i> Nueva Orden</button>
            </div>
        </div>
    </div>

    <div style="background-color: #D6DBDF; border-radius: 5px; margin-bottom: 15px;" class="col-md-12 mt-4">
        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Buscador</p>
    </div>

    <form action="{{ route('importaciones.new_orden_index') }}" method="post">
        {{ csrf_field() }}
        <div class="col-md-12">
            <div class="form-group col-md-1 col-xs-2">
                <label class="texto" for="descripcion">Descripcion: </label>
            </div>
            <div class="form-group col-md-3 col-12 container-4">
                <input class="form-control rounded" type="text" id="observacion" name="observacion" value="" placeholder="Ingrese descripcion...">
            </div>

            <div class="form-group col-md-3 col-12 container-4">
                <button class="bg-blue-400 py-[6px] px-3 transition-all duration-300  hover:bg-blue-600 text-white rounded w-full md:w-auto" type="submit" id="btnBuscar">
                    <i class="fa fa-search" aria-hidden="true"></i> Buscar
                </button>
            </div>
        </div>
    </form>

    @php
    $fecha_desde = date('Y')."-01-01";
    $fecha_hasta = date('Y-m-d');
    @endphp

    <input type="hidden" id="fecha_desde" value="{{$fecha_desde}}">
    <input type="hidden" id="fecha_hasta" value="{{$fecha_hasta}}">



    <div class="col-md-12 bg-gray-200 p-2 rounded">
        <p style="margin: 0; font-weight:bold;" class="text-gray-500">Listado Ordenes</p>
    </div>


    <div class="col-md-12 mt-5" style="padding: 0px;">
        <table class='table table-bordered table-striped table-hover'>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Fecha</th>
                    <th>Descripcion</th>
                    <th>Usuario Crea</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordenes as $orden)
                <tr>
                    <td>{{$orden->id}}</td>
                    <td>{{$orden->created_at}}</td>
                    <td>{{$orden->observacion}}</td>
                    <td>@if(isset($orden->usuario_crea)) {{$orden->usuario_crea->nombre1}} {{$orden->usuario_crea->apellido1}} @endif</td>
                    <td>@if($orden->estado == 1) Pendiente Aprobar @elseif($orden->estado == 2) Emitido @elseif($orden->estado == 0) Eliminado @endif</td>
                    <td>
                        <a href="{{route('importaciones.new_orden_edit',['id' => $orden->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                        @if($orden->estado == 1)
                        @if(!is_null($permiso_aprobado) or Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
                        <a onclick="aprobar('{{$orden->id}}')" class="btn btn-success"><i class="fa fa-check"></i></a>
                        @endif
                        <a onclick="eliminar('{{$orden->id}}')" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
    <div class="row">

    </div>
</div>
<script>
    const crear_orden = () => {
        $.ajax({
            url: `{{ route('importaciones.new_orden_store') }}`,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            success: function(data) {
                window.location.href = `{{url('contable/importaciones/new_orden_edit/${data.id_orden}')}}`;
            },
            error: function(data) {

            }
        })
    }

    function aprobar(id) {
        $.ajax({
            url: `{{ url('contable/importaciones/AprobarOrden/${id}') }}`,
            type: "GET",
            datatype: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    alertas(data.status, data.status, data.msj)
                    setTimeout(function() {
                        location.reload();
                    },1500)
                }
            },
            error: function(data) {
                alertas(data.status, data.status, data.msj)

            }
        })

    }

    function eliminar(id) {
        $.ajax({
            url: `{{ url('contable/importaciones/EliminarOrden/${id}') }}`,
            type: "GET",
            datatype: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    alertas(data.status, data.status, data.msj)
                    setTimeout(function() {
                        location.reload();
                    },1500)
                }
            },
            error: function(data) {
                alertas(data.status, data.status, data.msj)

            }
        })

    }
</script>


@endsection