@extends('laboratorio.orden.base')

@section('action-content')


<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <style type="text/css">
  .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td{
        padding: 3px !important;

    }
    div.formgroup.col-md-4{
        margin-bottom: 0px !important;
    }
</style>
<style type="text/css">
    .ro{
        color: black;
        font-weight: bolder;
    }
    .ro_2{
        color: black;
    }
    .radio_origen_2.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .radio_origen_3.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .box-title,.form-group,.box{
        margin: 0;
    }
    h3{
        margin: 0;
    }


</style>
<section class="content" >

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-6">
                        <h3 class="box-title">Cotización para Examen Seguro Privado</h3>
                    </div>
                </div>
                <div class="box-body">
                    <div class="alert-danger col-md-4 oculto" id="err">

                    </div>
                    <div class="col-md-12">
                        <div class="col-md-8 alert-success oculto" id="mensaje_membresia">
                        </div>
                        <span class="oculto" id="mensaje_membresia_oculto"></span>
                    </div>
                    <form class="form-vertical" id="formulario" role="form">
                        <!--cedula-->
                        <div class="alert-danger"><span id="cant_ord"></span></div>
                        <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="control-label">Cédula</label>
                            <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();">
                        </div>
                        <!--primer nombre-->
                        <div class="form-group col-md-4{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="control-label">Primer Nombre</label>
                            <input id="nombre1" class="form-control input-sm" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
                        </div>
                        <!--//segundo nombre-->
                        <div class="form-group col-md-4 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="control-label">Segundo Nombre</label>
                            <div class="input-group dropdown">
                                <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="">
                                <ul class="dropdown-menu usuario1">
                                    <li><a data-value="N/A">N/A</a></li>
                                </ul>
                                <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                            </div>
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-md-4{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="control-label">Primer Apellido</label>
                            <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
                        </div>
                        <!--Segundo apellido-->
                        <div class="form-group col-md-4 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="control-label">Segundo Apellido</label>
                            <div class="input-group dropdown">
                                <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="">
                                <ul class="dropdown-menu usuario2">
                                    <li><a data-value="N/A">N/A</a></li>
                                </ul>
                                <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                            </div>
                        </div>
                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-4{{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="control-label">Sexo</label>
                            <select id="sexo" name="sexo" class="form-control input-sm" required onchange="">
                                <option value="">Seleccionar ..</option>
                                <option value="1">MASCULINO</option>
                                <option value="2">FEMENINO</option>
                            </select>
                        </div>
                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-4 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                            <label class="control-label">Fecha Nacimiento</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{old('fecha_nacimiento')}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required onchange="">
                            </div>
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_doctor_ieced') ? ' has-error' : '' }}">
                            <label for="id_doctor_ieced" class="control-label">Médico</label>
                            <select id="id_doctor_ieced" name="id_doctor_ieced" class="form-control input-sm" required onchange="">
                                <option value="">Seleccione ...</option>
                            @foreach ($usuarios as $usuario)
                                <option @if(old('id_doctor_ieced') == $usuario->id) selected @endif value="{{$usuario->id}}">{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}} {{$usuario->nombre2}} </option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="control-label">Seguro</label>
                            <select id="id_seguro" name="id_seguro" class="form-control input-sm" required onchange="cargar_nivel();">
                                <option value="">Seleccione ...</option>
                                @foreach ($seguros1 as $seguro)
                                    <option @if(old('id_seguro') == $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                                @foreach ($seguros2 as $seguro)
                                    <option @if(old('id_seguro') == $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="div_nivel" class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }} oculto">
                        </div>
                        <div id="div_ticket" class="form-group col-md-4{{ $errors->has('ticket') ? ' has-error' : '' }} oculto">
                            <label for="ticket" class="control-label">No. Ticket</label>
                            <input id="ticket" class="form-control input-sm" type="text" name="ticket" value="{{ old('ticket') }}" required autofocus maxlength="100" autocomplete="off">
                        </div>
                        <div class="form-group col-md-4{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="control-label">Tipo</label>
                            <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required onchange="">
                                <option @if(old('est_amb_hos')== '0') selected @endif value="0">Ambulatorio</option>
                                <option @if(old('est_amb_hos')== '1') selected @endif value="1">Hospitalizado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                            <label for="id_empresa" class="control-label">Empresa</label>
                            <select id="id_empresa" name="id_empresa" class="form-control input-sm" onchange="">
                                <!--option value="">Seleccionar ...</option-->
                            @foreach ($empresas as $value)
                                @if($value->id=='0993075000001')
                                <option @if(old('id_empresa') == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                        <!--Telefono-->
                        <div class="form-group col-md-4{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="control-label">Telefono</label>
                            <input id="telefono1" class="form-control input-sm" type="text" name="telefono1" value="{{ old('telefono1') }}" required autofocus maxlength="50" autocomplete="off">
                            @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!--direccion-->
                        <div class="form-group col-md-4{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="control-label">Dirección</label>
                            <input id="direccion" class="form-control input-sm" type="text" name="direccion" value="{{ old('direccion') }}" required autofocus maxlength="100" autocomplete="off">
                        </div>
                        <!--email-->
                        <div class="form-group col-md-4{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Email</label>
                            <input id="email" class="form-control input-sm" type="text" name="email" value="{{ old('email') }}" required autofocus maxlength="100" onchange="busca_mail()">
                        </div>
                        <div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('pres_dom') ? ' has-error' : '' }}">
                            <label for="pres_dom" class="control-label">Presencial/Domicilio</label>
                            <select id="pres_dom" name="pres_dom" class="form-control input-sm" required>
                                <option value="">Seleccione...</option>
                                <option value="0">Presencial</option>
                                <option value="1">A Domicilio</option>
                            </select>
                        </div>

                        <!--origen-->
                        <div class="form-group col-md-12"  id="referidos">
                            <input type="hidden" name="tipo_referidos" id="tipo_referidos" value="0">
                            <label for="origen" class="col-md-12 control-label">Origen</label>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <input class="radio_origen" type="radio" name="origen" required id="medio_impreso" value="MEDIO IMPRESO" @if(old('origen')=='MEDIO IMPRESO') checked @endif>
                                    <span class="ro"> Medio impreso</span><br>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_2" type="radio" name="origen_impreso" value="REVISTA" @if(old('origen_impreso')=='REVISTA') checked @endif>
                                    <span class="ro_2"> Revista</span>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_2" type="radio" name="origen_impreso" value="FLYERS" @if(old('origen_impreso')=='FLYERS') checked @endif>
                                    <span class="ro_2"> Flyers</span>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_2" type="radio" name="origen_impreso" value="PERIODICO" @if(old('origen_impreso')=='PERIODICO') checked @endif>
                                    <span class="ro_2"> Periodico</span><br>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <div class="col-md-3">
                                        <input class="radio_origen_2" type="radio" name="origen_impreso" id="origen_otros" value="OTROS" @if(old('origen_impreso')=='OTROS') checked @endif>
                                        <span class="ro_2"> Otros</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="input-sm form-control" type="text" name="impreso_otros" id="impreso_otros" maxlength="100" value="{{old('impreso_otros')}}"><br>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input class="radio_origen" type="radio" name="origen" id="medio_digital" required value="MEDIO DIGITAL" @if(old('origen')=='MEDIO DIGITAL') checked @endif>
                                    <span class="ro"> Medio Digital</span><br>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_3" type="radio" name="origen_digital" value="FACEBOOK" @if(old('origen_digital')=='FACEBOOK') checked @endif>
                                    <span class="ro_2"> Facebook</span>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_3" type="radio" name="origen_digital" value="INSTAGRAM" @if(old('origen_digital')=='INSTAGRAM') checked @endif>
                                    <span class="ro_2"> Instagram</span>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_3" type="radio" name="origen_digital" value="EMAIL" @if(old('origen_digital')=='EMAIL') checked @endif>
                                    <span class="ro_2"> Email</span><br>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input class="radio_origen_3" type="radio" name="origen_digital" value="GOOGLE" @if(old('origen_digital')=='GOOGLE') checked @endif>
                                    <span class="ro_2"> Google</span> <br>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <div class="col-md-3">
                                        <input class="radio_origen_3" type="radio" name="origen_digital" value="OTROS" id="origen_otros2" @if(old('origen_digital')=='OTROS') checked @endif>
                                        <span class="ro_2"> Otros</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="input-sm form-control" name="digital_otros" id="digital_otros" maxlength="100" value="{{old('digital_otros')}}"><br>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input class="radio_origen" type="radio" name="origen" required value="REFERIDO" id="ireferido" @if(old('origen')=='REFERIDO') checked @endif>
                                    <span class="ro"> Referido (a)</span>
                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                    <input type="text" class="input-sm form-control" name="referido" id="referido" value="{{old('referido')}}"><br>
                                </div>
                             </div>
                        </div>

                        @php $cont=count(old('examen')) @endphp
                    </form>
                    <div class="col-md-12"> &nbsp;</div>
                    <div class="col-md-8 alert-success oculto" id="mensaje">

                    </div>
                    <div class="col-md-8 alert-warning oculto" id="orden_doctor">

                    </div>
                    <div class="col-md-12"> &nbsp;</div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-sm" type="button" onclick="crear_cabecera_post();" style="width: 100%">Ingresar Nueva Cotización</button>
                    </div>
                    @php
                        $plantillas_convenios = Sis_medico\Labs_Plantillas_Convenios::where('estado','1')->get();
                        $rolUsuario = Auth::user()->id_tipo_usuario;$i=0;
                    @endphp
                    
                    @foreach($plantillas_convenios as $plantilla)
                        @php $i ++; @endphp
                        <div class="col-md-2">
                            <button class="btn btn-success btn-sm" type="button" onclick="crear_cabecera_plantilla('{{$plantilla->id}}');" style="width: 100%">{{$plantilla->nombre}}</button>
                        </div>
                        @if($i=='5')
                        <div class="col-md-12"> &nbsp;</div>
                        @endif
                    @endforeach
                    
                </div>

            </div>
        </div>
    </div>

</section>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>


<script type="text/javascript">

    var js_examenes = [];
    $(document).ready(function() {
        //crear_cabecera();
        cargar_nivel();



        $('#fecha_nacimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
        });

        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'
        });

        $(".breadcrumb").append('<li><a href="{{asset('orden/')}}"> Inicio</a></li>');
        $(".breadcrumb").append('<li class="active">Solicitar</li>');

        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
        });
    });

    function busca_mail(){
        var email = document.getElementById('email').value;
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/mail/principal')}}/"+email, //orden.recupera_mail

            success: function(data){
                if(data=='no'){
                    /*$('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                    $('#sexo').val('');
                    $('#fecha_nacimiento').val('1980/01/01');*/
                }else{
                    //console.log(data);
                    $('#mensaje').removeClass('oculto');
                    $('#mensaje').empty().html('El correo pertenece a '+data.apellido1+' '+data.apellido2+' '+data.nombre1+' '+data.nombre2+' con CI: '+data.id+', si ingresa el paciente quedará anexado como grupo familiar');
                }
            }
        });
    }

    var buscapaciente = function ()
    {
        $('#email').removeAttr("readonly");
        var js_paciente = document.getElementById('id').value;
        $.ajax({
            type: 'get',
            url: "{{ url('hospitalizados/buscapaciente')}}/"+js_paciente, //hospitalizados.buscapaciente

            success: function(data){
                if(data=='no'){
                    /*$('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                    $('#sexo').val('');
                    $('#fecha_nacimiento').val('1980/01/01');*/
                    $('#referidos').removeClass('oculto');
                    $('#tipo_referidos').val(0);
                }else{
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    $('#fecha_nacimiento').val(data.fecha_nacimiento);
                    $('#telefono1').val(data.telefono1);
                    $('#direccion').val(data.direccion);
                    $('#referidos').addClass('oculto');
                    $('#tipo_referidos').val(1);
                    busca_usuario_mail();
                    busca_membresia();
                }
            }
        });
        busca_ordenes_hoy();
        busca_ordenes_hoy_doctor();
    }
    function busca_membresia(){
        var js_paciente = document.getElementById('id').value;
        $.ajax({
            type: 'get',
            url: "{{ url('labs_membresias')}}/"+js_paciente,
            success: function(data){
                $('#mensaje_membresia').empty().html('');
                if(data.estado == 'ok'){
                    $('#mensaje_membresia').empty().html('PACIENTE CON MEMBRESIA :'+data.nombre);
                    $('#mensaje_membresia').removeClass('oculto');
                }else{
                    $('#mensaje_membresia').addClass('oculto');
                    $('#mensaje_membresia_oculto').text(data.mensaje);
                }
            }
        });
    }
    var busca_usuario_mail = function ()
    {

        var js_paciente = document.getElementById('id').value;
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/mail/principal/recupera')}}/"+js_paciente,
            success: function(data){
                if(data=='no'){

                }else{
                    $('#email').val(data);
                    $('#email').attr("readonly", "yes");

                }
            }
        });

    }

    function busca_ordenes_hoy(){
        var js_paciente = document.getElementById('id').value;
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/orden/buscar')}}/"+js_paciente, //orden_lab.buscar_orden
            success: function(data){
                if(data != '0'){
                    $('#cant_ord').text('Paciente tiene: '+data+' orden(es) para el día de hoy, confirme si desea ingresar una nueva');
                }
                //alert(data);
            }
        });

    }

    function busca_ordenes_hoy_doctor(){
        var js_paciente = document.getElementById('id').value;
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/orden/buscar/doctor')}}/"+js_paciente, //orden_lab.buscar_orden_doctor
            success: function(data){
                console.log(data);
                //alert(data);
                if(data > 0){
                    //$('#cant_ord').text('Paciente tiene: '+data+' orden(es) para el día de hoy, confirme');
                    $('#orden_doctor').removeClass('oculto');
                    $('#orden_doctor').empty().html('<a href="{{url("cotizador/editar")}}/'+data+'" style="color:white">El paciente tiene una orden generada por un doctor, si desea verla Click aquí </a>');
                    <?php /*location.href = "{{url('cotizador/editar')}}/"+data;  */?>
                }
                //alert(data);
            }
        });

    }

    function crear_cabecera_post(){
        var id_seguro = $('#id_seguro').val();
        var ticket    = $('#ticket').val();
        var error     = false;
        $('#err').removeClass('has-error');
        $('#err').addClass('oculto');
        //alert(id_seguro)
        if(id_seguro == '41'){
            $('#div_ticket').removeClass('oculto');
        }else{
            $('#div_ticket').addClass('oculto');
        }
        if(id_seguro == '41'){
            if(ticket==''){
                error = true;
                alert("No puede Guardar Orden sin el número de ticket");
            }
        }
        if($('#tipo_referidos').val() == "0"){
            if(!($("#formulario input[name='origen']:radio").is(':checked'))) {
                var msn = '<ul>';
                msn = msn + '<li>Ingrese el Origen del paciente</li>' ;
                $('#error1').parent().addClass('has-error');
                msn = msn + '</ul>';
                $('#err').empty().html(msn);
                $('#err').removeClass('oculto');
                return 0;
            }else{
                var contenido = $('input:radio[name=origen]:checked').val();
                if(contenido == "MEDIO IMPRESO"){
                    if(!($("#formulario input[name='origen_impreso']:radio").is(':checked'))) {
                        var msn = '<ul>';
                        msn = msn + '<li>Ingrese el Origen Impreso del paciente</li>' ;
                        $('#error1').parent().addClass('has-error');
                        msn = msn + '</ul>';
                        $('#err').empty().html(msn);
                        $('#err').removeClass('oculto');
                        return 0;
                    }else{
                        var contenido2 = $('input:radio[name=origen_impreso]:checked').val();
                        if(contenido2 == "OTROS"){
                            contenido3 = $('#impreso_otros').val();
                            if(contenido3 == ""){
                               var msn = '<ul>';
                                msn = msn + '<li>Ingrese el Otro tipo de Origen Impreso del paciente</li>' ;
                                $('#error1').parent().addClass('has-error');
                                msn = msn + '</ul>';
                                $('#err').empty().html(msn);
                                $('#err').removeClass('oculto');
                                return 0;
                            }
                        }

                    }
                }else if(contenido == "MEDIO DIGITAL"){
                    if(!($("#formulario input[name='origen_digital']:radio").is(':checked'))) {
                        var msn = '<ul>';
                        msn = msn + '<li>Ingrese el Origen Digital del paciente</li>' ;
                        $('#error1').parent().addClass('has-error');
                        msn = msn + '</ul>';
                        $('#err').empty().html(msn);
                        $('#err').removeClass('oculto');
                        return 0;
                    }else{
                        var contenido2 = $('input:radio[name=origen_digital]:checked').val();
                        if(contenido2 == "OTROS"){
                            contenido3 = $('#digital_otros').val();
                            if(contenido3 == ""){
                               var msn = '<ul>';
                                msn = msn + '<li>Ingrese el Otro tipo de Origen Digital del paciente</li>' ;
                                $('#error1').parent().addClass('has-error');
                                msn = msn + '</ul>';
                                $('#err').empty().html(msn);
                                $('#err').removeClass('oculto');
                                return 0;
                            }
                        }

                    }
                }else{
                    contenido3 = $('#referido').val();
                    if(contenido3 == ""){
                       var msn = '<ul>';
                        msn = msn + '<li>Ingrese el Referido</li>' ;
                        $('#error1').parent().addClass('has-error');
                        msn = msn + '</ul>';
                        $('#err').empty().html(msn);
                        $('#err').removeClass('oculto');
                        return 0;
                    }

                }
            }
        }
        if(!error){

            $.ajax({
                type: 'post',
                url:"{{ route('cotizador.crear_cabecera') }}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#formulario").serialize(),
                success: function(data){
                    console.log(data);
                    location.href= "{{url('cotizador/editar')}}/" + data;
                },
                error: function(data){
                    var arr_name = data.responseJSON;
                    console.log(data);
                    $('#err').removeClass('oculto');
                    var msn = '<ul>';
                    $.each(arr_name, function(index, value) {
                        msn = msn + '<li>' + value + '</li>' ;
                        $('#'+ index).parent().addClass('has-error');
                    });
                    msn = msn + '</ul>';
                    $('#err').empty().html(msn);
                    console.log(msn);
                }
            })
        }
    }

    function cargar_nivel(){
        //alert("cargar nivel");
        var id_seguro = $('#id_seguro').val();
        //alert(id_seguro)
        if(id_seguro == '41'){
            $('#div_ticket').removeClass('oculto');
        }else{
            $('#div_ticket').addClass('oculto');
        }


        $.ajax({
            type: 'post',
            url:"{{route('agrupador_labs.nivel')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                if(data!='no'){
                    $('#div_nivel').removeClass('oculto');
                    $('#div_nivel').empty().html(data);

                }else{
                    $('#div_nivel').addClass('oculto');
                    $('#div_nivel').empty().html('');
                }


            },
            error: function(data){

                }
        })



    }

    function cotizador_crear(){
        var id_seguro = $('#id_seguro').val();
        var ticket    = $('#ticket').val();
        var error     = false;
        //alert(id_seguro)
        if(id_seguro == '41'){
            $('#div_ticket').removeClass('oculto');
        }else{
            $('#div_ticket').addClass('oculto');
        }
        if(id_seguro == '41'){
            if(ticket==''){
                error = true;
                alert("No puede Guardar Orden sin el número de ticket");
            }
        }
        if(!error){

            $.ajax({
                type: 'post',
                url:"{{route('cotizador.store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#formulario").serialize(),
                success: function(data){
                    //alert("creo");
                    $('#div_buscador').empty().html(data);



                },
                error: function(data){

                    }
            })
        }
    }

    
    function crear_cabecera_plantilla(id_plantilla){
        var id_seguro = $('#id_seguro').val();
        var ticket    = $('#ticket').val();
        var error     = false;
        $('#err').removeClass('has-error');
        $('#err').addClass('oculto');
        //alert(id_seguro)
        if(id_seguro == '41'){
            $('#div_ticket').removeClass('oculto');
        }else{
            $('#div_ticket').addClass('oculto');
        }
        if(id_seguro == '41'){
            if(ticket==''){
                error = true;
                alert("No puede Guardar Orden sin el número de ticket");
            }
        }
        if($('#tipo_referidos').val() == "0"){
            if(!($("#formulario input[name='origen']:radio").is(':checked'))) {
                var msn = '<ul>';
                msn = msn + '<li>Ingrese el Origen del paciente</li>' ;
                $('#error1').parent().addClass('has-error');
                msn = msn + '</ul>';
                $('#err').empty().html(msn);
                $('#err').removeClass('oculto');
                return 0;
            }else{
                var contenido = $('input:radio[name=origen]:checked').val();
                if(contenido == "MEDIO IMPRESO"){
                    if(!($("#formulario input[name='origen_impreso']:radio").is(':checked'))) {
                        var msn = '<ul>';
                        msn = msn + '<li>Ingrese el Origen Impreso del paciente</li>' ;
                        $('#error1').parent().addClass('has-error');
                        msn = msn + '</ul>';
                        $('#err').empty().html(msn);
                        $('#err').removeClass('oculto');
                        return 0;
                    }else{
                        var contenido2 = $('input:radio[name=origen_impreso]:checked').val();
                        if(contenido2 == "OTROS"){
                            contenido3 = $('#impreso_otros').val();
                            if(contenido3 == ""){
                               var msn = '<ul>';
                                msn = msn + '<li>Ingrese el Otro tipo de Origen Impreso del paciente</li>' ;
                                $('#error1').parent().addClass('has-error');
                                msn = msn + '</ul>';
                                $('#err').empty().html(msn);
                                $('#err').removeClass('oculto');
                                return 0;
                            }
                        }

                    }
                }else if(contenido == "MEDIO DIGITAL"){
                    if(!($("#formulario input[name='origen_digital']:radio").is(':checked'))) {
                        var msn = '<ul>';
                        msn = msn + '<li>Ingrese el Origen Digital del paciente</li>' ;
                        $('#error1').parent().addClass('has-error');
                        msn = msn + '</ul>';
                        $('#err').empty().html(msn);
                        $('#err').removeClass('oculto');
                        return 0;
                    }else{
                        var contenido2 = $('input:radio[name=origen_digital]:checked').val();
                        if(contenido2 == "OTROS"){
                            contenido3 = $('#digital_otros').val();
                            if(contenido3 == ""){
                               var msn = '<ul>';
                                msn = msn + '<li>Ingrese el Otro tipo de Origen Digital del paciente</li>' ;
                                $('#error1').parent().addClass('has-error');
                                msn = msn + '</ul>';
                                $('#err').empty().html(msn);
                                $('#err').removeClass('oculto');
                                return 0;
                            }
                        }

                    }
                }else{
                    contenido3 = $('#referido').val();
                    if(contenido3 == ""){
                       var msn = '<ul>';
                        msn = msn + '<li>Ingrese el Referido</li>' ;
                        $('#error1').parent().addClass('has-error');
                        msn = msn + '</ul>';
                        $('#err').empty().html(msn);
                        $('#err').removeClass('oculto');
                        return 0;
                    }

                }
            }
        }
        if(!error){

            $.ajax({
                type: 'post',
                url:"{{ url('labs_plantillas_convenios/crear_cabecera') }}/"+ id_plantilla,
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#formulario").serialize(),
                success: function(data){
                    //console.log(data);
                    location.href= "{{url('cotizador/editar')}}/" + data;
                },
                error: function(data){
                    var arr_name = data.responseJSON;
                    console.log(data);
                    $('#err').removeClass('oculto');
                    var msn = '<ul>';
                    $.each(arr_name, function(index, value) {
                        msn = msn + '<li>' + value + '</li>' ;
                        $('#'+ index).parent().addClass('has-error');
                    });
                    msn = msn + '</ul>';
                    $('#err').empty().html(msn);
                    console.log(msn);
                }
            })
        }
    }








</script>
@endsection
