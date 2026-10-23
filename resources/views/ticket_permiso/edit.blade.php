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

@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-7">
                    <h3 class="box-title">{{trans('tecnicof.editleave')}}. {{$registro->id}}</h3>
                </div>
            
                <div class="col-md-3">
                    @if($registro->ruta_archivo != null || $registro->ruta_archivo != '')
                    <a target="_blank" class="btn btn-primary" href="{{route('ticketpermisos.ver_pdf',['id'=>$registro->id])}}">{{trans('tecnicof.documentdowland')}}</a>
                    @endif
                </div>
                <div class="col-md-2">
                    <input type="button" class="btn btn-success" value="Regresar" onclick="goBack()">
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-1">
                    <label for="">{{trans('tecnicof.user')}}</label>
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="text" id="vh_usuario" name="vh_usuario" readonly value="{{$registro->nombre->nombre1}} {{$registro->nombre->nombre2}} {{$registro->nombre->apellido1}} {{$registro->nombre->apellido2}}">
                </div>
                <div class="col-md-1">

                </div>
                <div class="col-md-1">
                    <label for="">{{trans('tecnicof.id')}}</label>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="text" readonly id="cedula" value="{{$registro->cedula}}" class="form-control">
                </div>

                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.company')}}</label>
                    <div class="col-md-12">
                        @if(!is_null($nomina))
                        <span>{{$nomina->empresa->nombrecomercial}}</span>
                        @else
                        <input class="form-control" type="text" id="servicios" value="{{$registro->nombre->servicios}}" style="text-transform:uppercase;" class="form-control">
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.position')}}</label>
                    <div class="col-md-12">
                        <input class="form-control" type="text" readonly id="cargo" class="form-control" value="{{$registro->cargo}}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.department')}}</label>
                    <div class="col-md-12">
                        <input type="text" readonly id="area" class="form-control" value="{{$registro->departamento}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.permits')}}</label>
                    <div class="col-md-12">
                        <input class="form-control" name="permisos" id="permisos" readonly value="{{$registro->tipo_permiso}}">
                    </div>
                </div>
                <div>&nbsp;</div>

                <div class="col-md-12">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.leaveinday')}}</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.from')}}</label>
                    <div class="col-md-12">
                        <input type="date" readonly id="desde" name="desde" value="{{$registro->fecha_desde}}" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.to')}}</label>
                    <div class="col-md-12">
                        <input type="date" readonly id="hasta" name="hasta" value="{{$registro->fecha_hasta}}" class="form-control">
                    </div>
                </div>
                <div>&nbsp;
                </div>
                <div class="col-md-12">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.leaveinhours')}}</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.leave')}}</label>
                    <div class="col-md-12">
                        <input type="time" readonly id="sale" class="form-control" value="{{$registro->ora_salida}}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.enter')}}</label>
                    <div class="col-md-12">
                        <input type="time" readonly id="ingresa" value="{{$registro->ora_ingresa}}" class="form-control">
                    </div>
                </div>

                <div class="col-md-12">
                    &nbsp;
                </div>
                @if($registro->tipo_permiso=='OLVIDO DE MARCACION' || $registro->tipo_permiso=='ATRASOS'|| $registro->tipo_permiso=='TELETRABAJO')
                <div class="col-md-12">
                    <div style="text-align: center;">
                        <label for="">{{trans('tecnicof.forgotten')}}</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.leave')}}</label>
                    <div class="col-md-12">
                        <input type="time" id="ingreso" name="ingreso" value="{{$registro->hora_ingresomar}}" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.enter')}}</label>
                    <div class="col-md-12">
                        <input type="time" id="salida" name="salida" value="{{$registro->hora_salidamar}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                @endif
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
                <div class="col-md-2">
                    <label for="">{{trans('tecnicof.state')}}</label>
                    <div class="col-md-12">
                        <select name="estado" id="estado" class="form-control">
                            <option value="">{{trans('agenda.seleccione')}}</option>
                            <option {{$registro->estado_solicitud == 0  ? 'selected' : ''  }} value="0">{{trans('tecnicof.notapproved')}}</option>
                            <option {{$registro->estado_solicitud == 1  ? 'selected' : ''  }} value="1">{{trans('tecnicof.approved')}}</option>
                            <option {{$registro->estado_solicitud == 2  ? 'selected' : ''  }} value="2">{{trans('tecnicof.devolver')}}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="">{{trans('tecnicof.justification')}}</label>
                    @if($rolUsuario == 20 || $rolUsuario == 1)
                    <textarea name="obs_acepta" id="obs_acepta" placeholder="....." id="obs" style="width: 200%;">@if(!is_null($registro->justificacion_final)){{$registro->justificacion_final}} @endif</textarea>
                    @endif
                </div>
            </div>
            @if(!is_null($registro->path_devoluvion) and !is_null($registro->observacion_devolucion))
            <div class="row">
                <div class="text-center" style="background-color:yellowgreen;margin-bottom:5px">
                    <label style="color: white">Documentación de Soporte</label>
                </div>
                <div class="col-md-2">
                    <label for="">Justificación</label>
                </div>
                <div class="col-md-2">
                    <textarea name="justificacion" id="justificacion" cols="30" rows="3">{{$registro->observacion_devolucion}}</textarea>
                </div>
                @if($registro->path_devoluvion !== "")
                <div class="col-md-2">
                    <label for="">Documento</label>
                </div>
                <div class="col-md-2">
                    <a target="_blank" class="btn btn-warning" href="{{route('ticketpermisos.ver_prueba',['id'=>$registro->id])}}">Documentación Respaldo</a>
                </div>
                @endif
            </div>
            @endif
            <div class="col-md-12">
                &nbsp;
            </div>
            <div class="row" style="margin-left: 20px;">
                <p>* {{trans('tecnicof.hourly')}}</p>
                <p>* {{trans('tecnicof.attached')}}</p>
            </div>
            <div class="form-group col-md-12 col-xs-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                <label for="observaciones" id="titulo" class="col-md-2 control-label">{{trans('tecnicof.remarks')}}</label>
                <div class="col-md-10" style="padding-left: 50px;padding-right: 50%;">
                    <textarea id="observaciones" type="text" class="form-control" name="observaciones"  style="width: 200%;" readonly> {{$registro->observaciones }}</textarea>
                   
                </div>
            </div>
            <div class="row">
                <div style="text-align: center;margin-bottom:10px;">
                    <button class="btn btn-primary" id="editar">
                        {{trans('tecnicof.attend')}}
                    </button>
                </div>
            </div>
        </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">


    function veri(dato) {
        var datoEvitar = dato.id;
        var array = document.getElementsByClassName("check");
        for (let index = 0; index < array.length; index++) {
          
            if (array[index].id != datoEvitar) {
                document.getElementById(array[index].id).checked = false;
            }
        }
    }

    function guardar_archivo(id){

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
                // window.location = url;

            },
            error: function(xhr, status) {
                alert('Existió un problema al subir el archivo');
            },
        });        

    }

    /*document.getElementById("usuario").addEventListener('keydown', function() {
        let usuario = document.getElementById("usuario").value;
        //console.log(usuario);

        $.ajax({
            url: "{{route('ticket_soporte_tecnico.autocompletar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'data': usuario,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                // console.log(data.length); 
                var resultado = document.getElementById("result");
                var list = '';
                if (data.length > 0) {
                    document.getElementById("result").style.visibility = "visible";
                    for (let i = 0; i < data.length; i++) {
                        var nombreCompleto = data[i]['nombre1'] + ' ' + data[i]['nombre2'] + ' ' + data[i]['apellido1'] + ' ' + data[i]['apellido2'];
                        list += '<li  onclick="oprimir(this)" name="' + nombreCompleto + '" id=' + data[i]['id'] + '>' + data[i]['nombre1'] + ' ' + data[i]['nombre2'] + ' ' + data[i]['apellido1'] + ' ' + data[i]['apellido2'] + '</li>';
                    }
                    resultado.innerHTML = '<ul class="hola" style="margin-top:30px;">' + list + '</ul>';
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });

    });*/

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

    /*window.addEventListener('click', function(e) {
        document.getElementById("result").style.visibility = "hidden";
    });*/



    document.getElementById("editar").addEventListener('click', function() {
        var res = '';
        var todo = document.getElementsByClassName('check');
        for (let index = 0; index < todo.length; index++) {

            if (todo[index].checked == true) {

                res = todo[index].id;
            }
        }
        var arrayCompleto = [

            {
                'fecha_desde': document.getElementById('desde').value
            }, {
                'fecha_hasta': document.getElementById('hasta').value
            },
            {
                'sala': document.getElementById('sale').value
            }, {
                'ingresa': document.getElementById('ingresa').value
            },
            {
                'permiso': res
            },
            {
                'observaciones': document.getElementById("observaciones").value
            },
            {
                'id': <?php echo json_encode($registro->id); ?>
            },
            /*{
                'nombre': document.getElementById("usuario").value
            },*/
           /* {
                'fecha_registro': document.getElementById("fecha").value
            },*/
            {
                'cargo': document.getElementById("cargo").value
            },
            {
                'departamento': document.getElementById("area").value
            },
            {
                'estado': document.getElementById("estado").value
            },
            {
                'cedula': document.getElementById("cedula").value
            },
            {
                'obs_acepta': document.getElementById("obs_acepta").value
            },
           /*{
                'servicios': document.getElementById("servicios").value
            }*/
        ];;
        enviar(arrayCompleto);
    });



    function enviar(arrayCompleto) {

        var estado = $('#estado').val();
        var justificacion = $('#obs_acepta').val();
        var mensaje = '';
        if(estado==''){
            mensaje = "Ingrese el Estado de Aprobación \n";
        }
        if(estado == '0'){
            if(justificacion == ''){
                mensaje = mensaje + "Ingrese la Justificación \n";
            }
        }
        if(mensaje == ''){
            $.ajax({
                url: "{{route('ticketpermisos.editar_datos')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    estado: $('#estado').val(),
                    obs_acepta: $('#obs_acepta').val(),
                    id: '{{$registro->id}}',
                    servicios: $('#servicios').val(),
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('ticketpermisos.index')}}"
                    if (data.status) {
                        //enviar_mail('{{$registro->id}}}');
                        var userfile = $('#userfile').val();
                        if(userfile != undefined){
                            guardar_archivo('{{$registro->id}}');   
                        }
                        swal("Editado!", data.msj, "success");
                        window.location = url;
                    }else{
                        console.log(data);
                        alert("Error en el proceso de guardado")
                    }
                },
                error: function(xhr, status) {
                     alert('Existió un problema');
                    // console.log("ERROR", xhr, status);
                },
            });
        }else{
            alert(mensaje);
        }

            
    }

    function enviar_mail(id){
        $.ajax({
            url: "{{url('vh_ticket_permisos/mail_permisos')}}/" + id,
            type: 'get',
            dataType: 'json',
            success: function(data) {
                
               
            },
            error: function(xhr, status) {
                console.log("ERROR MAIL", xhr, status);
                // alert('Existió un problema al enviar el correo');

            },
        });
    }



    function goBack() {
        window.history.back()
    }
</script>


@endsection