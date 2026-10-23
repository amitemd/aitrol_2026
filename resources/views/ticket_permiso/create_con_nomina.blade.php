@extends('ticket_permiso.base')
@section('action-content')

<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }

    th {

        text-align: center;
    }

    .hola {
        z-index: 999999 !important;
        z-index: 999999999 !important;
        z-index: 99999999999999 !important;
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        display: block;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
    }

    .check {
        height: 20px;
        width: 20px;
    }

    .marcacion {
        display: none;
    }
</style>
<style type="text/css">
    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        top: 200px;
        right: 100px;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
@php
$user = Auth::user();
$id_usuario = $user->id;

@endphp
<section class="content">

    <div class="box box-primary col-xs-24">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">{{trans('tecnicof.createapplication')}}</h3>
                </div>
                <div class="col-md-2">
                    <input type="button" class="btn btn-success" value="Regresar" onclick="goBack()">
                </div>
            </div>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-1">
                    <label for="">{{trans('tecnicof.searchuser')}}</label>
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="text" id="vh_usuario" name="vh_usuario" placeholder="{{trans('tecnicof.nameandsurname')}}">
                </div>
                <div class="col-md-1">
                    <label for="">{{trans('tecnicof.id')}}</label>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="text" id="vh_cedula" name="vh_cedula" readonly>
                </div>

                <div class="col-md-12"></div>

            </div>
            <br>
            <form id="form_guardar" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="cedula" id="cedula">
                <input type="hidden" id="fecha" name="fecha" class="form-control" value="{{date('Y-m-d')}}">
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-3">
                        <label for=""><span id="nombre_empresa">
                                {{trans('tecnicof.company')}}
                            </span></label>
                        <div class="col-md-12">

                            <select class="form-control" name="id_empresa" id="id_empresa">
                                <option value="">{{trans('tecnicof.select')}}</option>
                                @foreach($empresa_nombres as $value)
                                <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.position')}}</label>
                        <div class="col-md-12">
                            <input type="text" id="cargo" name="cargo" readonly class="form-control" style="text-transform:uppercase;">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.department')}}</label>
                        <div class="col-md-12">
                            <input type="text" id="area" name="area" class="form-control" style="text-transform:uppercase;">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.permits')}}</label>
                        <div class="col-md-12">
                            <select class="form-control" name="permiso" id="permiso" onchange="mostrar_marcacion()">
                                <option value="">{{trans('tecnicof.select')}}</option>
                                <option value="PERMISO POR FALLECIMIENTO">{{trans('tecnicof.deathleave')}}</option>
                                <option value="LICENCIA POR MATERNIDAD">{{trans('tecnicof.maternityleave')}}</option>
                                <option value="LICENCIA POR PATERNIDAD">{{trans('tecnicof.paternityleave')}}</option>
                                <option value="CALAMIDAD DOMESTICA">{{trans('tecnicof.domesticcalamity')}}</option>
                                <option value="OLVIDO DE MARCACION">{{trans('tecnicof.forgetfulnessofmarking')}}</option>
                                <option value="PERMISO MEDICO">{{trans('tecnicof.medicalleave')}}</option>
                                <option value="PERMISO PERSONAL">{{trans('tecnicof.personalleave')}}</option>
                                <option value="COMISION DE SERVICIOS">{{trans('tecnicof.servicecommission')}}</option>
                                <option value="REPROGRAMAR VACACIONES">{{trans('tecnicof.vacationrescheduling')}}</option>
                                <option value="ATRASOS">{{trans('tecnicof.delays')}}</option>
                                <option value="TELETRABAJO">{{trans('tecnicof.teletrabajo')}}</option>
                                <option value="CAPACITACION">{{trans('tecnicof.capacitacion')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div style="text-align: center;">
                            <label for="">{{trans('tecnicof.leaveinday')}}</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.from')}}</label>
                        <div class="col-md-12">
                            <input type="date" id="desde" name="desde" class="form-control validar" >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.to')}}</label>
                        <div class="col-md-12">
                            <input type="date" id="hasta" name="hasta" class="form-control validar">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div style="text-align: center;">
                            <label for="">{{trans('tecnicof.leaveinhours')}}</label>
                        </div>
                    </div>

                    <!-- <div class="col-md-3">
                        <label for="">{{trans('tecnicof.leave')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="sale" name="sale" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.enter')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="ingresa" name="ingresa" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-12 marcacion">
                        <div style="text-align: center;">
                            <label for="">{{trans('tecnicof.forgotten')}}</label>
                        </div>
                    </div> -->

                    <div class="col-md-3 ">
                        <label for="">{{trans('tecnicof.enterofici')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="ingreso" name="ingreso" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3 ">
                        <label for="">{{trans('tecnicof.exitofici')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="salida" name="salida" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>

                    <div class="col-md-10">
                        <label class="col-md-3" for="">{{trans('tecnicof.uploaddocument')}}</label>
                        <div class="col-md-8">
                            <input class="form-control" type="file" name="userfile" id="userfile">
                        </div>
                    </div>
                     <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="form-group col-md-12 col-xs-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" id="titulo" class="col-md-2 control-label">{{trans('tecnicof.comments')}}</label>
                        <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                            <textarea id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>
                       
                        </div>
                    </div>


                </div>
            </form>

            <div class="col-md-12">
                &nbsp;
            </div>

            <div class="row" style="margin-left: 20px;">
                <p>*{{trans('tecnicof.hourly')}}</p>
                <p>*{{trans('tecnicof.attached')}}</p>
            </div>


            <div class="row">
                <div style="text-align: center;margin-bottom:10px;">
                    <button class="btn btn-primary" id="vh_guardar" onclick="vh_guardar()">
                        {{trans('tecnicof.save')}}
                    </button>
                </div>
            </div>

        </div>

    </div>

</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function mostrar_marcacion() {
        $('.marcacion').hide();
        $('#ingreso').val('');
        $('#salida').val('');
        var permiso = $('#permiso').val();
        console.log(permiso);
        if (permiso == 'OLVIDO DE MARCACION') {
            $('.marcacion').show();
        }
        if (permiso == 'ATRASOS') {
            $('.marcacion').show();
        }
        if (permiso == 'TELETRABAJO') {
            $('.marcacion').show();
        }

    }

    $("#vh_usuario").autocomplete({
        source: function(request, response) {

            $.ajax({
                url: "{{route('ticketpermisos.vh_buscar_usuario')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    //console.log(data);
                    response(data);
                }
            })
        },
        minLength: 2,
        select: function(data, ui) {
            //console.log("++"+ui.item.codigo);
            $('#vh_cedula').val(ui.item.id);
            $('#cedula').val(ui.item.id);
            buscar_nomina(ui.item.id);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    });

    function buscar_nomina(id) {
        $('#servicios').attr("readonly", false);
        $.ajax({
            url: "{{url('vh_ticket_permisos/buscar_nomina')}}/" + id,
            type: 'get',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('#cargo').val(data.cargo);
                $('#area').val(data.departamento);
                if (data.tiene_nomina == 0) {
                    $('#nombre_empresa').text('Servicios');
                    $('#servicios').val(data.servicios);
                }
                if (data.tiene_nomina == 1) {
                    $('#nombre_empresa').text('Empresa');
                    $('#servicios').val(data.empresa);
                    $('#servicios').attr("readonly", true);
                }

            },
            error: function(xhr, status) {
                alert('Existió un problema');

            },
        });

    }

    function vh_guardar() {
        //obligatorias
        var cedula = $('#cedula').val();
        var fecha = $('#fecha').val();
        var cargo = $('#cargo').val();
        var area = $('#area').val();
        var permiso = $('#permiso').val();
        var servicios = $('#servicios').val();
        var id_empresa = $('#id_empresa').val();
        //opcionales
        var desde = $('#desde').val();
        var hasta = $('#hasta').val();
        //
        var sale = $('#sale').val();
        var ingresa = $('#ingresa').val();
        //
        var ingreso = $('#ingreso').val();
        var salida = $('#salida').val();
        var observaciones = $('#observaciones').val();

        var mensaje = '';
        if (cedula == '') {
            mensaje = 'Seleccione el usuario\n';
        }
        if (fecha == '') {
            mensaje = mensaje + 'Seleccione la fecha\n';
        }
        if (cargo == '') {
            mensaje = mensaje + 'Seleccione el cargo\n';
        }
        if (area == '') {
            mensaje = mensaje + 'Seleccione el area\n';
        }
        if (permiso == '') {
            mensaje = mensaje + 'Seleccione el permiso\n';
        }
        if (id_empresa == '') {
            mensaje = mensaje + 'Seleccione la empresa\n';
        }

        if (mensaje == '') {

            var confirmar = confirm("Desea Guardar Solicitud");

            if (confirmar) {
                    document.getElementById("vh_guardar").disabled = true;
                $.ajax({
                    url: "{{route('ticketpermisos.save_sin_dato')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: $("#form_guardar").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        //var url = "{{route('ticketpermisos.index')}}";
                        if (data.msn == 'ok') {
                            guardar_archivo(data.id);
                            //swal("Guardado!", "Correcto", "success");
                            //window.location = url;
                        }
                            document.getElementById("vh_guardar").disabled = false;
                    },
                    error: function(xhr, status) {
                        alert('Existió un problema');
                            document.getElementById("vh_guardar").disabled = false;
                    },
                });
            }
        } else {
            alert(mensaje);
        }

    }

    function guardar_archivo(id) {

        var fd = new FormData();
        fd.append('userfile', $('#userfile')[0].files[0]);
        fd.append('id', id);
        $.ajax({
            url: "{{route('ticketpermisos.subir_documento')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            processData: false,
            contentType: false,
            data: fd,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var url = "{{route('ticketpermisos.index')}}";
                swal("Guardado!", "Correcto", "success");
                window.location = url;

            },
            error: function(xhr, status) {
                alert('Existió un problema al subir el archivo');
            },
        });

    }

    /*
    document.getElementById("fecha").addEventListener('change', function() {
        let fecha = new Date(this.value);
        let fechaPrueba = new Date(fecha.getFullYear(), fecha.getMonth(), parseInt(fecha.getDate() + 1));
        let fechaActual = new Date();
        let fechaPrueba1 = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), parseInt(fechaActual.getDate()));
        if (fechaPrueba.getTime() < fechaPrueba1.getTime()) {
            swal("Fecha pasada!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
        }
    });
    document.getElementById("desde").addEventListener('change', function() {

        let fecha = new Date(this.value);
        let fechaPrueba = new Date(fecha.getFullYear(), fecha.getMonth(), parseInt(fecha.getDate() + 1));
        let fechaActual = new Date();
        let fechaPrueba1 = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), parseInt(fechaActual.getDate()));
        if (fechaPrueba.getTime() < fechaPrueba1.getTime()) {
            swal("Fecha pasada!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
        }
    });
    document.getElementById("hasta").addEventListener('change', function() {
        let fecha = new Date(this.value);
        let fechaPrueba = new Date(fecha.getFullYear(), fecha.getMonth(), parseInt(fecha.getDate() + 1));
        let fechaActual = new Date();
        let fechaPrueba1 = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), parseInt(fechaActual.getDate()));

        let desdeFecha = document.getElementById("desde").value;
        let fechaFormat = new Date(desdeFecha);
        var timeDiff = Math.abs(fechaFormat.getTime() - fecha.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        if (diffDays > 15) {

            swal("Solo son permitidos 15 dias!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
            document.getElementById("desde").value = "";
        }

        if (fechaPrueba.getTime() < fechaPrueba1.getTime()) {
            swal("Fecha pasada!", "Incorrecto", "error");
            document.getElementById("hasta").value = "";
        }
    });
*/
    function veri(dato) {

        var datoEvitar = dato.id;
        const fechaDesde = new Date();
        const fecha = fechaDesde.getMonth() + 1;

        if (datoEvitar == 'olvido' || datoEvitar == 'atraso' || datoEvitar == 'teletrabajo')  {
            document.getElementById("ocultar-si").style.visibility = "hidden";
        } else {
            document.getElementById("ocultar-si").style.visibility = "visible";
        }



        if (datoEvitar == 'vacaciones' && fecha == 1) {
            swal("Incorrecto!", "Solo se puede seleccionar vacaciones en Enero", "error");
            document.getElementById(datoEvitar).checked = false;
        }
        var array = document.getElementsByClassName("check");
        for (let index = 0; index < array.length; index++) {
            if (array[index].id != datoEvitar) {
                document.getElementById(array[index].id).checked = false;
            }
        }
    }

    function oprimir(valor) {
        var te = valor.getAttribute("name");
        document.getElementById("result").style.visibility = "hidden";
        document.getElementById("usuario").value = te;
        document.getElementById("cedula").value = valor.id;
        $.ajax({
            url: "{{route('ticketpermisos.verificar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': valor.id,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var area = '';
                if (data.area == 1) {
                    area = 'ADMINISTRATIVA';
                } else {
                    area = 'MEDICA';
                }
                if (data.cargo != undefined) {
                    document.getElementById("cargo").value = data.cargo;
                }
                if (area != undefined) {
                    document.getElementById("area").value = area;
                }

                var url = "{{route('ticket_soporte_tecnico.index')}}"
                if (data == 'ok') {

                    swal("Guardado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema ---');
                //console.log(xhr);
            },
        });

    }


    function oprimir1(valor) {
        var te = valor.getAttribute("name");
        document.getElementById("result1").style.visibility = "hidden";
        document.getElementById("apellido").value = te;
        document.getElementById("cedula").value = valor.id;
        $.ajax({
            url: "{{route('ticketpermisos.verificar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': valor.id,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var area = '';
                if (data.area == 1) {
                    area = 'ADMINISTRATIVA';
                } else {
                    area = 'MEDICA';
                }
                if (data.cargo != undefined) {
                    document.getElementById("cargo").value = data.cargo;
                }
                if (area != undefined) {
                    document.getElementById("area").value = area;
                }
                var url = "{{route('ticket_soporte_tecnico.index')}}"
                if (data == 'ok') {

                    swal("Guardado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema ...');
                //console.log(xhr);
            },
        });

    }

    function goBack() {
        window.history.back()
    }
</script>


@endsection