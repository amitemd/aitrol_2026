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

    .swal2-icon-content{
        font-size:100px!important;
    }

    .swal2-icon.swal2-info{
        width: 160px!important;
        height: 160px!important;
        margin: 0px!important;
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
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Compras')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.registrodeimportac')}}</li>
        </ol>
    </nav>
    <!-- ANIMACION DE CARGANDO-->
    <div id="cargando" style="overflow-y: auto;">

    </div>
    <!-- FIN DE ANIMACION DE CARGANDO-->
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-6">
                <!--<h8 class="box-title size_text">Empleados</h8>-->
                <!--<label class="size_text" for="title">EMPLEADOS</label>-->
                <h3 class="box-title">{{trans('contableM.importacompras-')}}</h3>
            </div>

            <div class="col-md-2 text-center">
                <a href="{{ route('importaciones.create') }}" class="btn btn-success">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> {{trans('contableM.newpedido')}}
                </a>
            </div>

            <div class="col-md-2 text-center">
                <a href="{{ route('importaciones.new_orden_index') }}" class="btn btn-success">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> {{trans('contableM.preOrden')}}
                </a>
            </div>

            <div class="col-md-2 text-center">
                <a href="{{route ('importaciones.crear_agrupada') }}" class="btn btn-warning">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> {{trans('contableM.agruparpedidos')}}
                </a>
            </div>
        </div>

        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('contableM.buscadeimportac-')}}</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" id="form_busqueda" action="{{route('importaciones.index')}}">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="row">

                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="id">{{trans('contableM.id')}}</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="id" name="id" value="@if(isset($busqueda['id']))@if(!is_null($busqueda['id'])){{$busqueda['id']}}@endif @endif" placeholder="Ingrese Id..." />

                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="concepto">{{trans('contableM.concepto')}}: </label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($busqueda['observacion']))@if(!is_null($busqueda['observacion'])){{$busqueda['observacion']}}@endif @endif" placeholder="Ingrese Concepto..." />
                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="concepto">{{trans('contableM.secimporta')}}: </label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="sec_importacion" name="sec_importacion" value="" placeholder="{{trans('contableM.secimporta')}}..." />
                    </div>
                    
                </div>
                <div class="col-md-4 col-xs-2 col-xs-10 container-4">
                    <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>
                </div>
            </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">{{trans('contableM.listdeimportaciones-')}}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="resultados">
                    </div>
                    <div id="contenedor">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well-dark'>
                                                <th width="3%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.id')}}</th>
                                                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: active to sort column asceding"> {{trans('contableM.secimporta')}}</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.concepto')}}</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fechapedido')}}</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Numerodefactura')}}</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.serie')}}</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                                                <th class="">Kardex</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach($importaciones as $value)
                                            <tr @if($value->agrupada == 1) style="background: #AAFBF6;" @endif>
                                                <td>{{$value->id}}</td>
                                                <td>@if($value->agrupada == 0){{$value->secuencia_importacion}} @else @if($value->secuencia_importacion != 'null') <b>{{$value->secuencia_importacion}}</b>  @else <label>@lang('contableM.secuenagrupado')</label> @endif @endif</td>
                                                <td>@if($value->agrupada == 0) @if(isset($value->proveedor_da)){{$value->proveedor_da->nombrecomercial}} @endif @else <label>@lang('contableM.proveedoragrup')</label> @endif</td>
                                                <td>@if($value->agrupada == 0){{$value->observacion}}@else <label>{{$value->observacion}}</label> @endif</td>
                                                <td>@if($value->agrupada == 0) {{$value->fecha}} @else <label>@lang('contableM.fechaagrupado')</label> @endif</td>
                                                <td>@if($value->agrupada == 0) {{ $value->serie }}-{{ $value->secuencia_factura }} @else <label>@lang('contableM.secuagrupada')</label> @endif</td>
                                                <td>@if($value->agrupada == 0) {{ $value->serie }} @else <label>@lang('contableM.serieagrup')</label> @endif</td>
                                                <td>@if(isset($value->usuario)) {{ $value->usuario->nombre1 }} {{ $value->usuario->apellido1 }} @endif </td>
                                                <td>@if($value->inventario == 1) Listo @else Pendiente @endif </td>
                                                <td>
                                                    <div class="btn-group" style="width: 100%;">
                                                        <button style="width:50%;" type="button" class="btn btn-success btn-xs"><span style="font-size: 12px;">{{trans('contableM.acciones')}}</span></button>
                                                        <button style="width:10%;" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 2px;padding-right: 2px">
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu cot" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 80px;">
                                                            <li><a @if($value->inventario == 0) href="{{route('gastosimportacion.ingreso_factura',['id'=>$value->id])}}" target="_blank" @else style="color:black!important" class="btn-disabled" @endif> <i class="fas fa-plus"></i> @lang('contableM.factura')</a></li>
                                                            <li><a @if($value->inventario == 0) href="{{ route('importaciones.create_recibo', ['id'=>$value->id]) }}" target="_blank"> @else style="color:black!important" class="btn-disabled" @endif <i class="fas fa-plus"></i> @lang('contableM.recibo')</a></li>
                                                            <li><a @if($value->inventario == 0) href="{{ route('importaciones.create_orden', ['id'=>$value->id]) }}" target="_blank"> @else style="color:black!important" class="btn-disabled" @endif <i class="fas fa-plus"></i> @lang('contableM.Orden')</a></li>
                                                            <li><a @if($value->inventario == 0) href="{{ route('importaciones.liquidacion', ['id'=>$value->id]) }}" target="_blank"> @else style="color:black!important" class="btn-disabled" @endif <i class="fas fa-plus"></i> LIQ/SENAE</a></li>
                                                            @php /*<li><a href="{{ route('importaciones.view',['id'=>$value->id]) }}" target="_blank"><i class="far fa-eye"></i> @lang('agenda.view')</a></li>*/ @endphp
                                                            <li><a href="{{ route('index_importaciones',['id'=>$value->id]) }}" target="_blank">@lang('agenda.resumen')</a></li>
                                                            <li><a href="{{ route('importaciones.info_fecha_lote',['id'=>$value->id]) }}" target="_blank">Lote / F. Venc.</a></li>
                                                            <li><a href="{{ route('importaciones.pdf_importaciones',['id'=>$value->id]) }}" target="_blank">Pdf</a></li>
                                                            <li><a href="{{ route('index_excel',['id'=>$value->id]) }}" target="_blank">{{trans('contableM.excel')}}</a></li>
                                                            <li><a href="{{ route('gastosimportacion.subir_archivo',['id'=>$value->id]) }}" target="_blank">{{trans('contableM.subarchivos')}}</a></li>
                                                            <li><button @if($value->inventario == 0) onclick="kardex({{$value->id}});" @else style="background: #e1e3e9;color: black;border: 0px;cursor: no-drop; width: 82%!important;" @endif style="width: 80%!important;" type="button" class="btn btn-warning"target="_blank">Kardex</button></li>
                                                            <li><a href="{{ route('importaciones.kardex_resumen',['id'=>$value->id])}}" target="_blank">Resumen Kardex</a></li>
                                                            <!-- <li><a class="btn btn-danger" href="" target="_blank"> {{trans('laboratorio.eliminar')}}</a></li> -->
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($importaciones->currentPage() - 1) * $importaciones->perPage())}} / {{count($importaciones) + (($importaciones->currentPage() - 1) * $importaciones->perPage())}} {{trans('laboratorio.de')}} {{$importaciones->total()}} {{trans('contableM.registros')}}</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $importaciones->appends(Request::only(['id','concepto','sec_importacion']))->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    const cargando = accion => {
        let divCargando = document.getElementById("cargando");
        if (accion == 1) {
            let content = `<div id="cargando_hijo" class="loader-wrapper">
                            <div class="loader">
                                <div class="loader loader-inner"></div>
                            </div>
                            <div class="cargando">Cargando...</div>
                        </div>`;
            divCargando.classList.add("swal2-container", "swal2-center", "swal2-backdrop-show")

            $('#cargando').append(content);
        } else {
            let hijo = document.getElementById("cargando_hijo");
            divCargando.removeChild(hijo)
            divCargando.classList.remove("swal2-container", "swal2-center", "swal2-backdrop-show")
        }


    }

    const kardex = (id) => {
        Swal.fire({
            title: `{{trans('contableM.estasegdeenviaralkardex')}}`,
            text: "",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `{{trans('contableM.sideseoconti')}}`
        }).then((result) => {
            if (result.isConfirmed) {
                // cargando(1);
                $.ajax({
                    type: 'post',
                    url: "{{route('importaciones.store_compras_importacion')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    beforeSend: function() {
                    Swal.fire({
                            text: "Cargando...",
                            imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                            imageWidth: 200,
                            imageHeight: 200,
                            imageAlt: 'Custom image',
                        })
                    },
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        // cargando(0)
                        Swal.close();
                        console.log(data);
                        if (data.status == 'success') {
                            let ref = `<a style="color:red; font-weight:bold;" target="_blank" href="{{url('contable/contabilidad/libro/edit/${data.id_asiento}')}}">ASIENTO : ${data.id_asiento}</a>`
                            alertas(data.status, `{{trans('contableM.exito')}}...`, `${data.msj} <br> ${ref} Creado`)
                        } else {
                            alertas(data.status, 'Error...', data.msj)
                        }


                    },
                    error: function(data) {
                        // cargando(0)
                        console.log(data);
                    }
                });
            }
        })
    }

    const prueba = ({nombre, apellido}) => {
        console.log("🚀 ~ file: index.blade.php ~ line 366 ~ prueba ~ {}", nombre)  
    }



    const alertas = (icon, title, msj) => {

        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            html: `${msj}`,
        })
    }
</script>


@endsection