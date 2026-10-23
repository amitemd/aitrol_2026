@extends('laboratorio.orden.base')
@section('action-content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<style>
    .contenedor {
        max-width: 95%;
        margin: 0 auto;
    }
    * {
        font-family: 'Poppins', sans-serif;
    }
    .titulo {
        font-weight: bold;
        color: red;
    }
    .val {
        display: none;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="col-md-12">
                            <h3 class="titulo">Empresa: {{ $empresa->id }} - {{ $empresa->nombrecomercial }}</h3>
                        </div>
                        <div class="col-md-12">
                            <h3 class="box-title">Plantilla</h3>
                        </div>
                        <div class="col-md-12 d-flex ">
                            <div class="col-md-11">
                                <h5><span style="font-weight: bold;">Paciente:</span> {{$orden->id_paciente}} - {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }}</h5>
                            </div>
                            <div class="col-md-1">
                                <a href="{{route('orden.index_control')}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a>
                            </div>
                        </div>
                    </div><!--Aqui 21-02-23-->
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <label class="control-label" for="id_bodega">Seleccione Bodega: </label>
                            <select name="bodega" id="id_bodega" class="form-control" style="border-radius: 6px;" >
                                <option value="">Selecione...</option>
                                @foreach ($bodegas as $value)
                                    <option value="{{$value['id']}}">{{$value['nombre']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="col-md-12">
                <div class="table-responsive col-md-12">
                    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                        <div class="box-header estilo_box">
                            <div class="col-md-12" style="padding-left: 0px;">
                                <h3 class="box-title">
                                    <b>Detalle Examenes</b>
                                </h3>
                            </div>
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="cabecera">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body" style="padding: 5px;">
                            <table style="border: 1px solid;width:100%!important;">
                                <thead>
                                    <tr style="border: 1px solid;">
                                        <th>Examenes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($examen_det as $det)
                                    <tr>
                                        <td>{{$det->examen->nombre }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="box box-primary contenedor">
                    <table class="table">
                        <thead>
                            <th width="20%">Serie</th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Cantidad</th>
                            <th>Bodega</th>
                            <th>Usuario</th>
                            <th>Accion</th>
                        </thead>
                        <tbody id="table_insumos">
                           
                            @php echo $filas; @endphp
                        </tbody>
                        <tfoot>
                            <tr>
                                <!--<td>
                                  <input onchange="buscarProducto();" style="border-radius: 5px;" type="text" class="form-control" id="serie_prod"></td>
                                <td colspan="5"></td> -->
                                <td>
                                    <!-- <select onchange="buscarProducto(this);" id="buscador_producto" style="border-radius: 5px;width:100%" type="text" class="js-data-example-ajax">
                                    </select> -->
                                    <input type="text" class="form-control" onchange="buscarProducto(this);"  id="buscador_producto" placeholder="Buscar producto"  value="">
                                </td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Examen</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_derivado">
                            @if(isset($planilla_derivado->detalles))
                            @foreach($planilla_derivado->detalles as $det)
                            <tr id="tr_{{$det->id}}">
                                <td>@if(isset($det->derivado)) {{$det->derivado->examen->nombre}} @endif</td>
                                <td>@if(isset($det->derivado)) {{$det->derivado->tipo_derivado->nombre}} @endif</td>
                                <td>@if(isset($det->derivado)) {{$det->derivado->valor}} @endif</td>
                                <td>
                                    <button class="btn btn-danger" onclick="eliminar_det('{{$det->id}}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 box" style="text-align: center;padding-bottom: 15px;">
                <h3><b> Plantilla </b></h3>
                <div class="col-md-12" style="text-align: center;display: flex;justify-content: center;">
                    <div class="col-md-2 row">
                        <b>Seleccione: </b>
                    </div>
                    <div class="col-md-3 row">
                        <select class="form-control select2_plantilla" name="plantilla" id="plantila">
                            <option value="">Seleccione...</option>
                            @foreach($plantillas as $value)
                            <option value="{{$value->id}}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button onclick="cargarPlantilla()" class="btn btn-warning">Cargar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 box" style="text-align: center;padding-bottom: 15px;">
                <h3><b>Agregar Derivados </b></h3>
                <!-- <div class="col-md-12" style="text-align: center;display: flex;justify-content: center;">
                    <div class="col-md-2 row">
                        <b>Seleccione: </b>
                    </div>
                    <div class="col-md-3 row">
                        <select class="form-control select2_examenes" name="id_examen" id="id_examen" onchange="busca_examen();">
                            <option value="">Seleccione...</option>

                            @foreach($examen_det as $value)

                            <option value="{{$value->id_examen}}">{{ $value->examen->nombre }}</option>

                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 row">
                        <select class="form-control select2_examenes" name="val_derivado" id="val_derivado">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button onclick="guardar_derivado()" class="btn btn-warning">Agregar</button>
                    </div>
                </div> -->
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Examen</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($derivados as $der)
                            @php $style = ''; @endphp
                            @if(isset($planilla_derivado->detalles))
                            @php
                            $val_examen = $planilla_derivado->detalles->where('id_examen', $der->id_examen)->first();
                            if(!is_null($val_examen)){
                            $style = 'disabled checked';
                            }
                            @endphp
                            @endif
                            <tr>
                                <td>{{$der->nombre_examen}}</td>
                                <td>{{$der->nombre_tipo}}</td>
                                <td>{{$der->valor}}</td>
                                <td><input {{$style}} type="checkbox" name="ch_der" id="ch_der" onchange="guardar_derivado(`{{$der->id}}`,`{{$der->id_examen}}`);"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var studentSelect = $('.js-data-example-ajax');
        $('.js-data-example-ajax').select2({
            minimumInputLength: 2,
            ajax: {
                delay: 250,
                url: `{{ route('plantillacontrollabs.buscar_producto') }}`,
                datatype: 'json',
                data: function(params) {
                    return {
                        term: params.term,
                        id_orden: `{{$orden->id}}`
                    }
                },
                processResults: function(data, page) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.nombre,
                                id: item.id
                            }
                            var option = new Option(item.nombre, item.id, true, true);
                            studentSelect.append(option).trigger('change');
                        })
                    };
                },
                /*error: function(data) {
                    console.log(data);
                    alertas(`error`, `Error...`, data)
                }*/
            }
        });
    });
    window.onload = function() {
        $('.select2_plantilla').select2({
            tags: false
        });

        $('.select2_examenes').select2({
            tags: false
        });
    }
    function eliminar_det(id_det) {
        Swal.fire({
            title: '¿Desea eliminar este insumo?',
            text: "No puedes revertir esta acccion!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{ url('labs/plantilla_labs/eliminar_det') }}/" + id_det,
                    datatype: 'json',
                    success: function(data) {
                        alertas(data.status, (`${data.status}...`).toUpperCase(), data.msj)
                        if (data.status == 'success') {
                            document.getElementById("tr_" + id_det).remove();
                        }
                        // setTimeout(function() {
                        //     location.reload()
                        // }, 2000)
                    },
                    error: function(data) {
                    }
                })
            }
        })
    }
    function guardar_derivado(id_derivado, id_examen) {
        $.ajax({
            type: 'get',
            url: `{{ route('plantillacontrollabs.guardar_derivado') }}`,
            datatype: 'json',
            data: {
                'id_examen': id_examen,
                'id_orden': `{{$orden->id}}`,
                'id_derivado': id_derivado
            },
            success: function(data) {
                //location.reload();
                alertas(data.status, (`${data.title}...`).toUpperCase(), data.msj)
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(data) {
                alertas(`error`, `Error...`, `Ha ocurrido un error 2`)
            }
        })
    }
    function busca_examen() {
        let id_examen = document.getElementById('id_examen').value;
        $.ajax({
            type: 'get',
            url: `{{ route('plantillacontrollabs.busca_examen') }}`,
            datatype: 'json',
            data: {
                'id_examen': id_examen,
            },
            success: function(data) {
                $.each(data, function(key, registro) {
                    $("#val_derivado").append('<option value=' + registro.id + '>' + registro.nombre_tipo + '-' + registro.valor + '</option>');
                });
            },
            error: function(data) {
            }
        })
    }
    const buscarProducto = (val) => {
        if (val.value != '') {
            let bodega = document.getElementById('id_bodega').value;
            //Aqui 21-02-23
            if (bodega == "" || bodega == null) {
                // $('#buscador_producto').val("").trigger('change');
                val.value = "";
                alertas("error", "Error...", "Seleccione la bodega primero")

                return;
            }
            $.ajax({
                type: 'get',
                url: `{{ route('plantilla.buscarProducto') }}`,
                datatype: 'json',
                data: {
                    'id_producto': val.value,
                    'id_orden': `{{$orden->id}}`,
                    'id_bodega': bodega
                },
                success: function(data) {
                    if (data.status == 'success') {
                        $("#table_insumos").append(data.fila)
                        // document.getElementById('serie_prod').value = ''
                        $('#buscador_producto').val("").trigger('change');
                    } else {
                        alertas(data.status, (`${data.status}...`).toUpperCase(), data.msj)
                    }
                },
                /*error: function(data) {
                    alertas(`error`, `Error...`, data)
                }*/
            });
        }
    }
    const cargarPlantilla = () => {
        let planilla = document.getElementById('plantila').value;
        $.ajax({
            type: 'get',
            url: `{{ route('plantilla.cargarPanltilla') }}`,
            datatype: 'json',
            data: {
                'id_plantilla': planilla,
                'id_orden': `{{$orden->id}}`,
                'id_bodega': $('#id_bodega').val()
            },
            success: function(data) {
                if (data.status == "success") {
                    $("#table_insumos").append(data.fila)
                    /*if (data.falta != ' ') {
                        alertas('warning', 'Advertencia...', `Estos productos no tienen stock o no se encuentra en el inventario: <br>${data.falta}`);
                    }*/
                }
            },
            error: function(data) {
                alertas(`error`, `Error...`, `Ha ocurrido un error 4`)
            }
        })
    }
    function cambio_bodega(obj, id_modulo) {
        $.ajax({
            url: "{{route('modulobodega.store_bodega_usuario')}}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_bodega': obj.value,
                'id_modulo': id_modulo,
            },
            success: function(data) {
                console.log(data);
                document.getElementById('bodega_selecc').innerHTML = 'Bodega Seleccionada: ' + data.nombre_bodega
            },
            error: function(data) {

            }
        });
    }
</script>

<script>
    // const alertas = (icon, title, text) => {
    //     Swal.fire({
    //         icon: `${icon}`,
    //         title: `${title}`,
    //         html: `${text}`,
    //     })
    // }
</script>


@endsection