@extends('contable.importaciones.base')
@section('action-content')
<style>
    .form-control {
        border-radius: 5PX;
    }

    .dropdown-menu li a {
        font-size: 15px;
        color: white;
    }

    .btn-disabled {
        background: #e1e3e9;
        font-weight: bold;
    }

    .btn-disabled:hover {
        cursor: no-drop;
    }

    .label-success {
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    /* CSS CARGANDO */
    .swal2-container {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        -ms-flex-direction: row;
        flex-direction: row;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 10px;
        background-color: transparent;
        z-index: 1060;
    }

    .loader-wrapper {
        width: 220px;
        height: 220px;
    }

    .loader {
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        border: 34px solid #162534;
        border-top-color: #4bc8eb;
        border-bottom-color: #f13a8f;
        border-radius: 50%;
        animation: rotate 5s linear infinite;
    }

    .loader-inner {
        border-top-color: #36f372;
        border-bottom-color: #fff;
        animation-duration: 2.5s;
    }

    @keyframes rotate {
        0% {
            transform: scale(1) rotate(360deg);
        }

        50% {
            transform: scale(.8) rotate(-360deg);
        }

        100% {
            transform: scale(1) rotate(360deg);
        }
    }

    .cargando {
        text-align: center;
        color: white;
        font-size: 38px;
        text-transform: uppercase;
        font-weight: 700;
        font-family: inherit;
    }

    .swal2-icon-content {
        font-size: 100px !important;
    }

    .swal2-icon.swal2-info {
        width: 160px !important;
        height: 160px !important;
        margin: 0px !important;
    }

    /* FIN CSS CARGANDO */
</style>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Importaciones')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.Preorden')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-6">
                <h3 class="box-title">{{trans('contableM.PreordenImportacion')}}</h3>
            </div>
            <div class="col-md-2 text-center">
                <a href="{{ route('contable.importaciones.pre_orden') }}" class="btn btn-success">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> {{trans('contableM.preOrden')}}
                </a>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="POST" id="form_busqueda" action="">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="row">

                    </div>

                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="concepto">{{trans('contableM.concepto')}}: </label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="concepto" name="concepto" placeholder="Ingrese Concepto..." />
                    </div>
                    <div class="col-md-4 col-xs-2 col-xs-10 container-4">
                        <button type="submit" id="buscar" class="btn btn-primary btn-gray">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                        </button>
                    </div>
                </div>

            </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="col-md-12">
                    <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>

                                <th class="">{{trans('contableM.proveedor')}}</th>
                                <th class="">{{trans('contableM.concepto')}}</th>
                                <th class="">{{trans('contableM.fechapedido')}}</th>
                                <th class="">{{trans('contableM.total')}}</th>
                                <th class="">{{trans('contableM.creadopor')}}</th>
                                <th class="">{{trans('contableM.accion')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ordenes as $orden)
                            <tr>

                                <td>{{$orden->proveedor_da->nombrecomercial}}</td>
                                <td>{{$orden->observacion}}</td>
                                <td>{{$orden->fecha}}</td>
                                <td>{{$orden->total}}</td>
                                <td>{{$orden->usuario->apellido1}} {{$orden->usuario->nombre1}}</td>
                                <td>
                                    @if($orden->proceso == 0)
                                    <a type="button" class="btn btn-xs btn-info" onclick="aprobar('{{$orden->id}}');"><i class="fa fa-check"></i></a>
                                    <a type="button" class="btn btn-xs btn-warning" href="{{route('importaciones.edit_preorden',['id'=>$orden->id])}}"><i class="fa fa-edit"></i></a>
                                    <a type="button" class="btn btn-xs btn-danger" onclick="eliminar('{{$orden->id}}');"><i class="fa fa-trash"></i></a>
                                    @else
                                    <label class="label label-danger">Orden Aprobada</label>
                                    @endif

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function aprobar(id) {
        Swal.fire({
            title: `Está seguro que desea aporbar esta orden`,
            text: "",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `{{trans('contableM.sideseoconti')}}`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{url('contable/importaciones/aprobar/preorden')}}/" + id,
                    datatype: 'json',
                    success: function(data) {
                        alertas(data.respuesta, data.respuesta, data.msj)
                        setTimeout(function() {
                            location.reload();
                        }, 1500)
                    },
                    error: function(data) {

                    }
                })
            }
        })
    }

    function eliminar(id) {
        Swal.fire({
            title: `Está seguro que desea eliminar esta orden`,
            text: "",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `{{trans('contableM.sideseoconti')}}`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{url('contable/importaciones/eliminar/preorden')}}/" + id,
                    datatype: 'json',
                    success: function(data) {
                        alertas(data.respuesta, data.respuesta, data.msj)
                        setTimeout(function() {
                            location.reload();
                        }, 1500)
                    },
                    error: function(data) {

                    }
                })
            }
        })
    }
</script>
@endsection