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
@php
$meses = [1=>trans('laboratorio.enero'),2=>trans('laboratorio.febrero'),3=>trans('laboratorio.marzo'),4=>trans('laboratorio.abril'),5=>trans('laboratorio.mayo'),6=>trans('laboratorio.junio'),7=>trans('laboratorio.julio'),8=>trans('laboratorio.agosto'),9=>trans('laboratorio.septiembre'),10=>trans('laboratorio.octubre'),11=>trans('laboratorio.noviembre'),12=>trans('laboratorio.diciembre')];
@endphp

<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="verdepreciacion" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>


<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Depreciaciones</h3>

        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-end md:w-auto w-full">
            </div>
        </div>
    </div>

    <div style="background-color: #D6DBDF; border-radius: 5px; margin-bottom: 15px;" class="col-md-12 mt-4">
        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Buscador</p>
    </div>

    <form action="{{ route('activosfijos.depreciacion.index_depreciaciones') }}" method="post">
        {{ csrf_field() }}
        <div class="col-md-12">
            <div class="form-group col-md-1 col-xs-2">
                <label class="texto" for="descripcion">Año: </label>
            </div>
            <div class="form-group col-md-2 col-12 container-4">
                <select class="form-control rounded" name="anio" value="{{$anio}}">
                    @php $anio_actual=date('Y'); @endphp
                    <option value="">{{trans('laboratorio.seleccione')}}</option>
                    @for($x=2022;$x<=$anio_actual;$x++) <option @if($x==$anio) selected @endif>{{$x}}</option>
                        @endfor
                </select>
            </div>

            <div class="form-group col-md-1 col-xs-2">
                <label class="texto" for="descripcion">Mes: </label>
            </div>
            <div class="form-group col-md-2 col-12 container-4">
                <select class="form-control rounded" name="mes" value="{{$mes}}">
                    <option value="">{{trans('laboratorio.seleccione')}}</option>
                    @foreach($meses as $i=>$val)
                    <option @if($mes==$i) selected @endif value="{{$i}}">{{$val}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-1 col-xs-2">
                <label class="texto" for="descripcion">Observación: </label>
            </div>
            <div class="form-group col-md-3 col-12 container-4">
                <input type="text" name="observacion" class="form-control rounded" value="{{$observacion}}">
            </div>

            <div class="form-group col-md-2 col-12 container-4">
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
                    <th width="5%">ID</th>
                    <th width="20%">AÑO</th>
                    <th width="10%">MES</th>
                    <th width="10%">FECHA</th>
                    <th width="20%">ID ASIENTO</th>
                    <th width="20%">OBSERVACIÓN</th>
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($depreciaciones as $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{$value->anio}}</td>
                    <td>{{$value->mes}}</td>
                    <td>{{substr($value->fecha,0,10)}}</td>
                    <td>
                        <a class="btn btn-success " data-remote="{{ route('compras.modal_estado',[$value->id_asiento])}}" data-toggle="modal" data-target="#visualizar_estado"> @if(!is_null($value->id_asiento)) {{$value->id_asiento}} @endif</a>
                    </td>
                    <td>{{$value->observacion}}</td>
                    <td>
                        <a class="btn btn-info " data-remote="{{ route('activofjo.depreciacion.ver_depreciacion',[$value->id])}}" data-toggle="modal" data-target="#verdepreciacion"> <i class="fa fa-eye"></i></a>

                        <a class="btn btn-danger" onclick="anular('{{$value->id}}')"><i class="fa fa-trash"></i></a>
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
    $('#visualizar_estado').on('hidden.bs.modal', function() {
        location.reload();
        $(this).removeData('bs.modal');
    });

    $('#verdepreciacion').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });


    function anular(id) {
        //alert("aqui");
        Swal.fire({
            title: `¿Desea Anular este comprobante?`,
            text: `No puedes revertir esta accion!!`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{ url('activofijo/anular_depreciacion')}}/" + id,
                    datatype: 'json',
                    success: function(data) {
                        console.log(data);
                        alertas(data.status, data.status.toUpperCase(), data.msj)
                        if (data.status == "success") {
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(data) {
                        alertas(data.status, data.status.toUpperCase(), data.msj)
                        console.log(data);
                    }
                });
            }
        })
    }
</script>


@endsection