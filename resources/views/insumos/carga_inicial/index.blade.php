@extends('insumos.carga_inicial.base')
@section('action-content')
<style>
    input[type=file]::file-selector-button {
        margin-right: 20px;
        border: none;
        background: #084cdf;
        padding: 5px 10px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
        transition: background .2s ease-in-out;
    }



    input[type=file]::file-selector-button:hover {
        background: #0d45a5;
    }

    .files {
        margin: 0px;
        border: 1px solid #C6C9CB;
        width: 393px;
        border-radius: 10px;
    }

    .container-subida {
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 3px solid #DDE2E4;
        border-radius: 9px;
        padding: 20px;
    }

    .swal2-warning{
        height: 100px!important;
        display: flex!important;
        width: 100px!important;
        margin-top: 20px!important;
    }

    .swal2-icon-content{
        font-size: 123px!important;
    }
</style>
<div style="display:flex; padding: 15px;">
    <h3>Carga Inicial</h3>
</div>

<div style="display:flex; justify-content: end; padding: 15px;">
    <!-- <a href="" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i> Crear</a> -->
    &nbsp;
    <a class="btn btn-primary" href="{{route('insumos.nuevo_reporte_plantilla')}}"><i class="fa fa-download" aria-hidden="true"></i> Descargar plantilla</a>
    &nbsp;
    <a class="btn btn-danger"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar</a>
</div>
<form id="form" method="post" action="{{ route('insumos.storeExcel') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div style="padding: 20px;">
        <div class="container-subida">
            <div class="col-md-3">
                <label>Subir Excel</label>
            </div>
            <div class="col-md-12" style="display: flex;justify-content: center;">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="observacion" placeholder="Observacion..." id="observacion">
                </div>
            </div>
            <div class="col-md-12" style="padding:15px;display: flex; justify-content: center;">
                <input type="file" accept=".xlsx, .csv" class="files" name="archivo" id="archivo"> &nbsp;&nbsp;&nbsp;
                <button type="button" onclick="subirExcel()" class="btn btn-primary" style="background:#084cdf;"><i class="fa fa-upload" aria-hidden="true"></i> Subir</button>
            </div>
        </div>
    </div>
</form>

<hr>
<div class="col-md-12">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre Excel</th>
                <th>Descripción</th>
                <th>Fecha Proceso</th>
                <th> Usuario </th>
                <th style="text-align: center;">Total Items</th>
                <th style="text-align: center;">Producto Problemas</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insumos as $value)
            <tr>
                <td>{{ $value->nombre_excel }}</td>
                <td>{{ $value->descripcion }}</td>
                <td>{{ $value->fecha_proceso }}</td>
                <td> {{ $value->user_crea->apellido1 }} {{ $value->user_crea->nombre1 }}  </td>
                <td style="text-align: center;"> <span class="label label-info"> {{ $value->total_item }} </span> </td>
                <td style="text-align: center;"> <span class="label label-danger">{{ $value->item_problema }}</span> </td>
                <td>
                    <a href="{{ route('insumos.edit', ['id' => $value->id]) }}" class="btn btn-warning"><i class="fa fa-eye" aria-hidden="true"></i></a>
                   
                    @if($value->procesado == 0)
                    <button onclick="procesar({{$value->id}})" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>
                    <button onclick="eliminar({{$value->id}})" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-sm-5">
        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('wfact_venta.mostrando')}} {{1 + (($insumos->currentPage() - 1) * $insumos->perPage())}} / {{count($insumos) + (($insumos->currentPage() - 1) * $insumos->perPage())}} {{trans('wfact_venta.de')}} {{$insumos->total()}} {{trans('wfact_venta.registros')}}</div>
    </div>
    <div class="col-sm-7">
        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $insumos->appends(Request::only(['id', 'numero','nombre_cliente','nombres_paciente','fecha','id_asiento','omni']))->links() }}
        </div>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    function subirExcel() {

        let observacion = document.getElementById('observacion');
        let archivo = document.getElementById('archivo');
        if (observacion.value.trim() == "") {
            observacion.style.border = "1px solid red";
            alertas('error', 'Error...', 'Agrege una descripción')
            return;
        } else {
            observacion.style.border = "1px solid #d2d6de"
        }

        if (archivo.value.trim() == "") {
            archivo.style.border = "1px solid red";
            alertas('error', 'Error...', 'Agrege una descripción')
            return;
        } else {
            archivo.style.border = "1px solid #d2d6de"
        }



        var form = $('#form')[0];
        var data = new FormData(form);
        $.ajax({
            enctype: 'multipart/form-data',
            type: "post",
            url: `{{ route('insumos.storeExcel') }}`,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            beforeSend: function() {
                Swal.fire({
                    //title: "{{trans('winsumos.procesando')}}",
                    text: "Cargando...",
                    imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
            },
            datatype: "html",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(datahtml) {
                console.log("🚀 ~ file: index.blade.php ~ line 175 ~ subirExcel ~ ", datahtml)
                Swal.close();
                let hola = "Como estas ${datahtml}"
                alertas(datahtml.status, `${datahtml.status.toUpperCase()}...`, datahtml.msj)
                if (datahtml.status == 'success') {
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500)
                }
                
            },
            error: function(datahtml) {
                alert("ocurrio un error");
            }
        });
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    const procesar = id => {
        Swal.fire({
            title: 'Esta seguro?',
            text: "Esta seguro que quiere hacer esta carga inicial, al hacer esta acción no se podra reversar",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, continuar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: `{{ route('insumos.procesar') }}`,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'id_cabecera': id
                    },
                    beforeSend: function() {
                        Swal.fire({
                            //title: "{{trans('winsumos.procesando')}}",
                            text: "Cargando...",
                            imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                            imageWidth: 200,
                            imageHeight: 200,
                            imageAlt: 'Custom image',
                            showConfirmButton: false,
                        })
                    },
                    success: function(data) {
                        console.log("🚀 ~ file: index.blade.php ~ line 172 ~ procesar ~ data", data)
                        alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)

                    },
                    error: function(data) {

                    }
                })
            }
        })

    }

    const eliminar = id => {
        Swal.fire({
            title: 'Esta seguro?',
            text: "Esta seguro que quiere eliminar",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, continuar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: `{{ url('insumos/carga/inicial/delete') }}/${id}`,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {'id': id},
                    beforeSend: function() {
                        Swal.fire({
                            //title: "{{trans('winsumos.procesando')}}",
                            text: "Cargando...",
                            imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                            imageWidth: 200,
                            imageHeight: 200,
                            imageAlt: 'Custom image',
                            showConfirmButton: false,
                        })
                    },
                    success: function(data) {
                        alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)
                        if(data.status == 'success'){
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                        
                    },
                    error: function(data) {

                    }
                })
            }
        })
    }


</script>

@endsection