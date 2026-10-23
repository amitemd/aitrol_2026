@extends('ticket_permiso.base')
@section('action-content')
@php
$fecha = date('Y-m-d',strtotime($registro->fecha_registro));
$fecha1 = date('Y-m-d',strtotime($registro->fecha_desde));
$fecha2 = date('Y-m-d',strtotime($registro->fecha_hasta));
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp
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
</style>
@php
$nomina = Sis_medico\Ct_Nomina::where('id_user',$registro->cedula)->where('estado','1')->first();
$nomina2 = [];
if(is_null($nomina)){
    $nomina2 = Sis_medico\Ct_Nomina::where('id_user',$registro->cedula)->where('estado','0')->get()->last();
}

@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-7">
                    <h3 class="box-title">{{trans('tecnicof.editleave')}} {{$registro->id}}</h3>
                </div>

                <div class="col-md-3">
                    @if($registro->ruta_archivo != null || $registro->ruta_archivo != '')
                            <a target="_blank" class="btn btn-primary"
                                href="{{ route('ticketpermisos.ver_pdf', ['id' => $registro->id]) }}">{{ trans('tecnicof.documentdowland') }}</a>
                        @elseif($registro->path_devoluvion != null)
                            <a target="_blank" class="btn btn-primary"
                                href="{{ route('ticketpermisos.ver_pdf', ['id' => $registro->id]) }}">{{ trans('tecnicof.documentdowland') }}</a>
                    @endif
                </div>
                <div class="col-md-2">
                    <input type="button" class="btn btn-success" value="Regresar" onclick="goBack()">
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <form id="form_edit" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="id_registro" value="{{$registro->id}}">
                <input type="hidden" id="fecha" name="fecha" class="form-control" value="{{date('Y-m-d')}}">
                <div class="row">
                    <div class="col-md-1">
                        <label for="">{{trans('tecnicof.user')}}</label>
                    </div>
                    <div class="col-md-6">
                        <input class="form-control" type="text" id="vh_usuario" name="vh_usuario" value="{{$registro->nombre->nombre1}} {{$registro->nombre->nombre2}} {{$registro->nombre->apellido1}} {{$registro->nombre->apellido2}}">
                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-1">
                        <label for="">{{trans('tecnicof.id')}}</label>
                    </div>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="cedula" value="{{$registro->cedula}}" class="form-control">
                    </div>

                    <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.company')}}</label>
                        <div class="col-md-12">
                            @if(!is_null($nomina))
                            <input class="form-control" type="text" id="servicios" value="{{$nomina->empresa->nombrecomercial}}" style="text-transform:uppercase;" class="form-control" readonly>
                            @else
                            <input class="form-control" type="text" id="servicios"  value="{{$nomina2->empresa->nombrecomercial}}" style="text-transform:uppercase;" class="form-control" readonly >
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.position')}}</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="cargo" id="cargo" class="form-control" value="{{$registro->cargo}}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.department')}}</label>
                        <div class="col-md-12">
                            <input type="text" id="area" name="departamento" class="form-control" value="{{$registro->departamento}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.permits')}}</label>
                        <div class="col-md-12">

                            <select class="form-control" name="permiso" id="permiso">
                                <option value="{{$registro->tipo_permiso}}">{{$registro->tipo_permiso}}</option>
                                <option value="PERMISO POR FALLECIMIENTO">1.{{trans('tecnicof.deathleave')}}</option>
                                <option value="LICENCIA POR MATERNIDAD">2.{{trans('tecnicof.maternityleave')}}</option>
                                <option value="LICENCIA POR PATERNIDAD">3.{{trans('tecnicof.paternityleave')}}</option>
                                <option value="CALAMIDAD DOMESTICA">4.{{trans('tecnicof.domesticcalamity')}}</option>
                                <option value="OLVIDO DE MARCACION">5.{{trans('tecnicof.forgetfulnessofmarking')}}</option>
                                <option value="PERMISO MEDICO">6.{{trans('tecnicof.medicalleave')}}</option>
                                <option value="PERMISO PERSONAL">7.{{trans('tecnicof.personalleave')}}</option>
                                <option value="COMISION DE SERVICIOS">8. {{trans('tecnicof.servicecommission')}}</option>
                                <option value="REPROGRAMAR VACACIONES">9.{{trans('tecnicof.vacationrescheduling')}}</option>
                                <option value="ATRASOS">10. {{trans('tecnicof.delays')}}</option>
                                <option value="TELETRABAJO">11. {{trans('tecnicof.teletrabajo')}}</option>

                            </select>
                        </div>
                    </div>
                    <div>&nbsp;</div>

                    <div class="col-md-12">
                        <div style="text-align: center;">
                            <label for="">{{trans('tecnicof.leaveindays')}}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.from')}}</label>
                        <div class="col-md-12">
                            <input type="date" id="desde" name="desde" value="{{$registro->fecha_desde}}" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.to')}}</label>
                        <div class="col-md-12">
                            <input type="date" id="hasta" name="hasta" value="{{$registro->fecha_hasta}}" class="form-control">
                        </div>
                    </div>
                    <div>&nbsp;
                    </div>
                    <div class="col-md-12">
                        <div style="text-align: center;">
                            <label for="">{{trans('tecnicof.leaveinhours')}}</label>
                        </div>
                    </div>

                    <!-- <div class="col-md-3">
                        <label for="">{{trans('tecnicof.leave')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="ora_salida" name="ora_salida" class="form-control" value="{{$registro->ora_salida}}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.enter')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="ora_ingresa" name="ora_ingresa" value="{{$registro->ora_ingresa}}" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-12">
                        &nbsp;
                    </div> 

                    <div class="col-md-12">
                        <div style="text-align: center;">
                            <label for="">{{trans('tecnicof.forgotten')}}</label>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.enterofici')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="ingreso" name="ingreso" value="{{$registro->hora_ingresomar}}" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">{{trans('tecnicof.exitofici')}}</label>
                        <div class="col-md-12">
                            <input type="time" id="salida" name="salida" value="{{$registro->hora_salidamar}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>
                   
                    @if($registro->ruta_archivo == null)
                    <div class="col-md-10">
                        <label class="col-md-3" for="">{{trans('tecnicof.uploaddocument')}}</label>
                        <div class="col-md-8">
                            <input class="form-control" type="file" name="userfile" id="userfile" />
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12">
                        &nbsp;
                    </div>

                </div>

                <div class="row" style="margin-left: 20px;">
                    <p>* {{trans('tecnicof.hourly')}}</p>
                    <p>* {{trans('tecnicof.attached')}}</p>
                </div>
                <div class="form-group col-md-12 col-xs-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" id="titulo" class="col-md-2 control-label">Observaciones</label>
                    <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                        <textarea id="observaciones" type="text" class="form-control" name="observaciones" value="{{$registro->observaciones }}"  style="width: 100%;" >{{$registro->observaciones }}</textarea>
                    
                    </div>
                </div>
                <div class="row">
                    <div style="text-align: center;margin-bottom:10px;">
                        <button type="button" class="btn btn-primary" onclick="enviar();">
                            {{trans('tecnicof.edit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
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

    function veri(dato) {
        var datoEvitar = dato.id;
        var array = document.getElementsByClassName("check");
        for (let index = 0; index < array.length; index++) {

            if (array[index].id != datoEvitar) {
                document.getElementById(array[index].id).checked = false;
            }
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
                var url = "{{route('ticketpermisos.index_usuario')}}";
                swal("Guardado!", "Correcto", "success");
                window.location = url;

            },
            error: function(xhr, status) {
                alert('Existió un problema al subir el archivo');
            },
        });

    }



    function veri(dato) {

        var datoEvitar = dato.id;

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
                alert('Existió un problema');
                //console.log(xhr);
            },
        });

    }


    function enviar() {
        //alert("ingreso");
        $.ajax({
            url: "{{route('ticketpermisos.editar_datos_usuarios')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },

            type: 'POST',
            dataType: 'html',
            data: $("#form_edit").serialize(),
            success: function(data) {
                var url = "{{route('ticketpermisos.index_usuario')}}"
                if (data == 'ok') {
                    var userfile = $('#userfile').val();
                    console.log("aqui" + userfile);
                    if (userfile != undefined) {
                        guardar_archivo('{{$registro->id}}');
                    }
                    swal("Editado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }

    function goBack() {
        window.history.back()
    }
</script>


@endsection