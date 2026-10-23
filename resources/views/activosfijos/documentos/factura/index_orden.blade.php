@extends('activosfijos.documentos.factura.base')
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
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.activosfijos')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.preOrden')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-6">
                <h3 class="box-title">{{trans('contableM.preordenactivofijo')}}</h3>
            </div>
            <div class="col-md-2 text-center">
                <a href="{{route('documentofactura.pre_orden')}}" class="btn btn-success">
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
            <form method="GET" id="form_docordenes" action="">
                {{ csrf_field() }}
                <div class="col-md-12">

                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="id">{{trans('contableM.id')}}: </label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="id" name="id" placeholder="Ingrese Id..." />
                    </div>

                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="concepto">{{trans('contableM.concepto')}}: </label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="concepto" name="concepto" placeholder="Ingrese Concepto..." />
                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="concepto">{{trans('contableM.proveedor')}}: </label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="proveedor" name="proveedor" placeholder="Ingrese Proveedor..." />
                    </div>

                    <div class="col-md-4 col-xs-2 col-xs-10 container-4 pull-right" style="text-align: right;">
                        <button type="submit" id="" class="btn btn-primary btn-gray">
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
                            <tr class='well-dark'>
                                <th width="5%">{{trans('contableM.id')}}</th>
                                <th width="20%">{{trans('contableM.secuencia')}}</th>
                                <th width="10%">{{trans('contableM.fechacompra')}}</th>
                                <th width="10%">{{trans('contableM.ruc')}}</th>
                                <th width="20%">{{trans('contableM.proveedor')}}</th>
                                <th width="20%">{{trans('contableM.concepto')}}</th>
                                <th width="15%">{{trans('contableM.accion')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ordenes as $value)
                            <tr>
                                <td>{{$value->id}}</td>
                                <td>{{$value->serie}}-{{$value->secuencia}}</td>
                                <td>{{ date('d/m/Y',strtotime($value->fecha_asiento))}}</td>
                                <td>{{$value->proveedor}}</td>
                                <td>{{$value->nombrecomercial}}</td>
                                <td>{{$value->observacion}}</td>
                                <td>
                                    @if($value->aprobado == '0')
                                    <a href="{{ route('documentofactura.edit_preorden', ['id' => $value->id]) }}" class="btn btn-warning btn-xs">
                                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                                    </a>
                                    <a onclick="eliminar('{{$value->id}}');" class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                    <a onclick="aprobar('{{$value->id}}');" class="btn btn-success btn-xs">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </a>
                                    
                                    <a href="{{route('documentofactura.index_fotos',['id'=>$value->id])}}" class="btn btn-success btn-gray btn-xs">
                                        <i class="fa fa-image" aria-hidden="true"></i>
                                    </a>
                                    @else
                                    <label class="label label-danger">Orden Aprobada</label>
                                    @endif

                                    <a href="{{ route('documentofactura.subir_archivo', ['id' => $value->id]) }}" class="btn btn-info btn-xs">
                                        <i class="fa fa-upload" aria-hidden="true"></i>
                                    </a>
                                    

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

        $.ajax({
            type: 'get',
            url: "{{url('contable/activofijo/aprobar/preorden')}}/" + id,
            datatype: 'json',
            success: function(data) {
                alertas(data.respuesta, data.respuesta, data.msj)
                if (data.respuesta == 'success') {
                    setTimeout(function() {
                        location.reload();
                    }, 1500)
                }

            },
            error: function(data) {
                alertas(data.respuesta, data.respuesta, data.msj)
            }
        })
    }

    function eliminar(id) {
        $.ajax({
            type: 'get',
            url: "{{url('contable/activofijo/eliminar/preorden')}}/" + id,
            datatype: 'json',
            success: function(data) {
                alertas(data.respuesta, data.respuesta, data.msj)
                if (data.respuesta == 'success') {
                    setTimeout(function() {
                        location.reload();
                    }, 1500)
                }
            },
            error: function(data) {
                alertas(data.respuesta, data.respuesta, data.msj)
            }
        })
    }
</script>
@endsection