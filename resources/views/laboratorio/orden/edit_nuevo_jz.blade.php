@extends('laboratorio.orden.base')

@section('action-content')
@php
    $xuser = Auth::user();

@endphp 
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
</style>
<section class="content" >
    <div class="col-md-12" style="text-align: right;">
        <button id="boton" type="button" class="btn btn-danger" onclick="confirmar();" @if($orden->estado=='1') disabled @endif>
        @lang('laboratorio.generar')
        </button>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-4">
                        <h3 class="box-title">@lang('laboratorio.editarordenexamenpublico')</h3>
                    </div> 
                    <div class="col-md-8">
                        <h3 class="box-title"><b>@lang('laboratorio.paciente'):</b> {{$orden->id_paciente}}-{{$orden->pnombre1}} @if($orden->pnombre2=='N/A'||$orden->pnombre2=='(N/A)') @else{{ $orden->pnombre2 }} @endif {{$orden->papellido1}} @if($orden->papellido2=='N/A'||$orden->papellido2=='(N/A)') @else{{ $orden->papellido2 }} @endif   </h3>
                    </div>    
                </div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('orden.nuevo_editar_pub',['id' => $orden->id]) }}">
                        
                        {{ csrf_field() }}
                        <!--div class="col-md-12" style="text-align: right;">
                            <b>Valor:</b><span id="xvalor"></span>    
                        </div-->
                        <input type="hidden" name="dir" value="{{$dir}}">
                        <input type="hidden" id="id_orden" name="id_orden" value="{{$orden->id}}">

                        <div class="form-group col-md-4 {{ $errors->has('id_doctor_ieced') ? ' has-error' : '' }}">
                            <label for="id_doctor_ieced" class="col-md-12 control-label">@lang('laboratorio.medico')</label>
                            <div class="col-md-12"> 
                                <select id="id_doctor_ieced" name="id_doctor_ieced" class="form-control input-sm" required>
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                @foreach ($usuarios as $usuario)
                                    <option @if($orden->id_doctor_ieced==$usuario->id) selected @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @endif>{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('id_doctor_ieced'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_doctor_ieced') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_convenio') ? ' has-error' : '' }}">
                            <label for="id_convenio" class="col-md-12 control-label">@lang('laboratorio.convenio')</label>
                            <div class="col-md-12"> 
                                <select id="id_convenio" name="id_convenio" class="form-control input-sm" required>
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                @foreach ($convenios as $con)
                                    
                                    <option @if(old('id_con')!='') @if(old('id_con') == $con->id) selected @endif @else @if($convenio->id==$con->id) selected @endif @endif value="{{$con->id}}">{{$con->nombre}}-{{$con->nombrecomercial}}</option>
                                    
                                @endforeach
                                </select>
                                @if ($errors->has('id_convenio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_convenio') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>

                        <div class="form-group col-md-4 ">
                            <label for="id_doctor_ieced" class="col-md-12 control-label">@lang('laboratorio.valor')</label>
                            <div class="col-md-12">
                                <input class="form-control input-sm" type="text" name="valor" value="{{$orden->valor}}" readonly style="text-align: right;"> 
                                
                            </div>
                        </div>
                        <div class="form-group col-md-4{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-12 control-label">@lang('laboratorio.tipo')</label>
                            <div class="col-md-12">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required>
                                    <option @if(old('est_amb_hos')!=null) @if(old('est_amb_hos')== '0') selected @endif @else @if($orden->est_amb_hos=='0') selected @endif @endif value="0">@lang('laboratorio.ambulatorio')</option>
                                    <option @if(old('est_amb_hos')!=null) @if(old('est_amb_hos')== '1') selected @endif @else @if($orden->est_amb_hos=='1') selected @endif @endif value="1">@lang('laboratorio.hospitalizado')</option>
                                </select>  
                            
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        

                        

                        <!--fecha_orden-->
                        <div class="form-group col-md-4 {{ $errors->has('fecha_orden') ? ' has-error' : '' }} ">
                            <label class="col-md-12 control-label">@if($xuser->id_tipo_usuario=='5') @lang('laboratorio.fecha_orden') @else @lang('laboratorio.fecha_convenios') @endif</label>
                            <div class="col-md-12">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{old('fecha_orden')}}" name="fecha_orden" class="form-control pull-right input-sm" id="fecha_orden" @if($orden->estado=='1')  readonly @endif required >

                                </div>
                                @if ($errors->has('fecha_orden'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_orden') }}</strong>
                                </span>
                                @endif
                                
                            </div>
                        </div>

                        <!--div class="form-group col-md-4 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                            <label for="id_protocolo" class="col-md-12 control-label">@lang('laboratorio.protocolo')</label>
                            <div class="col-md-12"> 
                                <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="Protocolo();">
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                @foreach ($protocolos as $protocolo)
                                    <option @if(old('id_protocolo') == $protocolo->id) selected @endif @if($orden->id_protocolo==$protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('id_protocolo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_protocolo') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div-->

                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>
                </div>        


    <div id="content" name="content">

    </div>
</section>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">

    $(document).ready(function() {
        let id_orden = $('#id_orden').val();
        crear_orden_publica(id_orden);
    });

//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
    $('#fecha_orden').datetimepicker({
        format: 'YYYY/MM/DD',
        date: '{{$orden->fecha_orden}}'


    }); 
    var contador = {{$orden->cantidad}};

    function confirmar(){
        swal.fire({
            title: '`{{trans("laboratorio.emitirordenenviarlabfechaemision")}}` {{date("Y-m-d")}}',
            //text: "You won't be able to revert this!",
            icon: "warning",
            type: 'warning',
            buttons: true,
          
          }).then((result) => {
            if (result.value) {
             //aqui pon algo
             cierre_caja()
             location.href = "{{url('orden_iess/confirmar')}}"+"/{{$orden->id}}"
        }
        })

        
    }

    function cierre_caja(){
        $.ajax({
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        url:"{{route('c_caja.storeLabs')}}",
        data:{'id_orden': '{{$orden->id}}','id_paciente':'{{$orden->id_paciente}}','total':'{{$orden->total_valor}}'},
        datatype: 'json',
        success: function(data){
            console.log(data);
            //alert(data)
        },
        error: function(data){
            //console.log(data);
            //alert(data)
        }
        });  
    }
    $(document).ready(function() {

        @foreach($examenes as $examen)
            @if($examen->estado=='0')
                $('#ch{{$examen->id}}').iCheck('disable');
            @endif
        @endforeach

        if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }

        $('input[type="checkbox"].flat-green').on('ifChecked', function(event){
            contador = contador + 1;
            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }
            
        });

        $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
            //Protocolo();
            
            

            var protocolo = document.getElementById('id_protocolo').value;
            var textid = this.id; 
            //alert(this.id);

            if(protocolo!=''){

                $.ajax({
                    type: 'get',
                    url: "{{ url('protocolo/busca/examen')}}/"+protocolo+"/"+this.id, //protocolo.buscaexamenid
                       
                    success: function(data){
                        //alert("ahora");
                        //console.log(data);
                        //alert(data);
                        if(data=='ok'){
                            alert(`{{trans('laboratorio.nopuedequitarexamenprotocolo')}}`); 
                            $('#'+textid).prop('checked',true).iCheck('update');   
                        }else{
                            contador = contador - 1;    
                            //alert('resta');
                            //alert(contador);   
                        }
                        
                    }    
                });

            }else{

                contador = contador - 1;    
                //alert('resta');
                //alert(contador); 

            }

            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }



        });

        $("#id_tipo_usuario").change(function () {
            
            //var valor = 0;
            var estado = document.getElementById("id_tipo_usuario").value;
            
             
        });



        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'


            });
         $(".breadcrumb").append('<li><a href="{{asset('orden/')}}"> Inicio</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');

        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');    
        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

        $('#fechaini').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

        $('.select2').select2();

        $('#examen').on('select2:select', function (e) {
            var data = e.params.data;
            console.log(data);
        });
           

    });

    var buscapaciente = function ()
    {
    

        var js_paciente = document.getElementById('id').value;
        
        $.ajax({
            type: 'get',
            url: "{{ url('hospitalizados/buscapaciente')}}/"+js_paciente, //hospitalizados.buscapaciente
                       
            success: function(data){
                if(data=='no'){
                    $('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#procedencia').focus();
                }
            }    
        });  
    
    }
    function Protocolo(){

        contador = 0;
        $('input[type="checkbox"].flat-green').prop('checked',false).iCheck('update');
        

        var protocolo = document.getElementById('id_protocolo').value;
        //alert(protocolo);
 
        if(protocolo!=''){

            $.ajax({
                type: 'get',
                url: "{{ url('protocolo/busca/examen')}}/"+protocolo, //protocolo.buscaexamen
                       
                success: function(data){
                    if(data!='no'){
                    
                        data.forEach(function(element) {
                            //alert('ch'+element.id_examen);
                            $('#ch'+element.id_examen).prop('checked',true).iCheck('update');
                        
                            contador = contador + 1;
                            //alert(contador);
                            
                            if(contador>0){
                                $("#boton").prop('disabled', false);    
                            }else{
                                $("#boton").prop('disabled', true);
                            }
                        });
                 
                    }
                }    
            });

        }else{
            $("#boton").prop('disabled', true);    
        }

    } 

    function crear_orden_publica(id_orden){
        $.ajax({
            type: 'get',
            url:"{{url('vt_ptv_laboratorio_iess/edit/')}}/"+id_orden,
            datatype: 'json',
            success: function(data){

                $('#content').html(data);

            },
            error: function(data){

            }
        });

    }

</script>
@endsection
