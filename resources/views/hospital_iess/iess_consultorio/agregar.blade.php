@extends('agenda.base')

@section('action-content')

<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>

<style type="text/css">
  .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }
       
        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222; 
        }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />



<!-- Ventana modal editar -->
<div class="modal fade" id="bucar_nombre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">    
                    <h4 class="modal-title" id="myModalLabel">AGREGAR IESS-CONSULTORIO PARA EL DR(A). {{ $doctor->apellido1}}</h4>
                </div>
                <div class="box-body">
                <form role="form" method="POST" action="{{route('iess_consultorio.crear')}}" id="form" >    
                    {{ csrf_field() }}
                    <!--nombre1-->
                    <span style="color: blue;" class="col-md-12" id="mensaje"></span>
                    <!--Primer nombre-->
                    <div class="form-group col-md-12 cl_nombre1 {{ $errors->has('nombre1') ? ' has-error' : '' }}" id="cambio5">
                        <label for="nombre1" class="col-md-2 control-label">Nombre Paciente</label>
                        <div class="col-md-8">
                            <input id="nombre1" type="text" class="form-control input-sm"  name="nombre1" value="{{old('nombre1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Nombres y Apellidos">
                            <span class="help-block">
                                <strong id="str_nombre1"></strong>
                            </span>
                        </div>
                    </div>
                   
                    <input type="hidden" class="form-control" name="id_doctor1" value="{{ $doctor->id}}" >
                    <input type="hidden" class="form-control" id="id_paciente" name="id_paciente" value="" >
                    <input type="hidden" class="form-control" id="proc_consul" name="proc_consul" value="0" >
                    <input type="hidden" class="form-control" id="unix" name="unix" value="{{$unix}}" >
                                    
                    <div class="form-group col-md-6 cl_inicio {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                        <label class="col-md-4 control-label">Inicio</label>
                        <div class="col-md-7">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{$hora}}" name="inicio" class="form-control input-sm" id="inicio" required onchange="incremento(event)">
                            </div>
                            <span class="help-block">
                                <strong id="str_inicio"></strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-md-6 cl_fin {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                        <label class="col-md-4 control-label">Fin</label>
                        <div class="col-md-7">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{ old('fin') }}" name="fin" class="form-control input-sm" id="fin" required>
                            </div>
                            <span class="help-block">
                                <strong id="str_fin"></strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                        <label for="id_sala" class="col-md-4 control-label">Ubicación</label>
                        <div class="col-md-7">
                                <select id="id_sala" name="id_sala" class="form-control input-sm"  required>
                                    <option value="">Seleccione..</option>
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}}@endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div> 

                    <div class="form-group col-md-6 {{ $errors->has('est_amb_hos') ? ' has-error' : '' }}" id="cambio6">
                        <label for="est_amb_hos" class="col-md-4 control-label">Tipo de Ingreso</label>
                        <div class="col-md-7">
                            <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" >
                                <option @if(old('est_amb_hos')=="0"){{"selected"}}@endif value="0">Ambulatorio</option> 
                                <option @if(old('est_amb_hos')=="1"){{"selected"}}@endif value="1" >Hospitalizado</option>
                            </select>  
                            @if ($errors->has('est_amb_hos'))
                            <span class="help-block">
                                <strong>{{ $errors->first('est_amb_hos') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div id="cambio11" class="form-group col-md-6 {{ $errors->has('tipo_cita') ? ' has-error' : '' }}" >
                        <label for="tipo_cita" class="col-md-4 control-label">Consec/1ra vez</label>
                        <div class="col-md-7">
                            <select id="cortesia" name="tipo_cita" class="form-control input-sm" >
                                <option @if(old('tipo_cita')=="1"){{"selected"}}@endif value="1">Consecutivo</option> 
                                <option @if(old('tipo_cita')=="0"){{"selected"}}@endif value="0" >Primera vez</option>
                            </select>  
                            @if ($errors->has('tipo_cita'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo_cita') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

            
  
                    <div class="form-group">
                            <div class="col-md-6 col-md-offset-5">
                                <button type="submit" id="enviar_dato" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Agendar
                                </button>
                            </div>
                        </div>   
                </form>
                </div>
            </div>    
        </div>
    </div>
</section>    

<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

 $(document).ready(function(){ 

    incremento();

    $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
    $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda', ['id' => $doctor->id, 'i' => $doctor->id]) }}"></i> Doctor</a></li>');
    $(".breadcrumb").append('<li class="active">Agregar</li>');

    $('.usuario1 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;

        if (relacion == "Principal") {
        $("#nombre22").val('(' + $(this).attr('data-value') + ')');

        } 

        busca_usuario_nombre();
    });

    $('.usuario2 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;
 
        if (relacion == "Principal") {
        $("#apellido22").val('(' + $(this).attr('data-value') + ')');} 

        busca_usuario_nombre();
    });

    $('.usuario3 a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode').val('(' + $(this).attr('data-value') + ')');
        busca_usuario_nombre1(); 

    });

    $("#nombre1").autocomplete({
        source: function( request, response ) {
                
            $.ajax({
                url:"{{route('paciente.nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data)
                }
            })
        },
        minLength: 3,
    } );

    $("#nombre1").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('paciente.nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#nombre1"),
            success: function(data){
                $('#id_paciente').val(data);
                    //console.log(data);
                    js_paciente();

                },
            error: function(data){
                    //console.log(data);
                }
        })
    });

          
        
        $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',



            });
        $('#fin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD HH:mm',
            minDate: '{{$hora}}',
             //Important! See issue #1075
            
        });

        var inicio = document.getElementById("inicio").value;

        @if($doctor->id=='1314490929')
            var fin = moment(inicio).add(20, 'm').format('YYYY/MM/DD HH:mm');
        @else
            var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
            
        @endif

        $("#fin").val(fin);

        $("#inicio").on("dp.change", function (e) {
            $('#fin').data("DateTimePicker").minDate(e.date);
            incremento(e);
        });


        $('.select2').select2();
        
        


    
});  

function incremento (e){
        var fjs_inicio = document.getElementById("inicio").value;
        var fjs_valor = document.getElementById("proc_consul").value;
         

         if(fjs_valor == 0 || fjs_valor == ''){

            
            @if($doctor->id=='1314490929')
            var fjs_fin = moment(fjs_inicio).add(20, 'm').format('YYYY/MM/DD HH:mm');
            @else
             var fjs_fin = moment(fjs_inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
            @endif 
            

            $("#fin").val(fjs_fin);
         }
         if(fjs_valor == 1){

            var fjs_fin = moment(fjs_inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');
            
            $("#fin").val(fjs_fin);
         }
    } 
     

function js_paciente (){
    var js_ced = document.getElementById('id_paciente').value;
    //alert(js_ced);
    if(js_ced!='0'){
        $(".cl_telefono1").addClass("oculto");
        $('#mensaje').empty().html("Paciente se encuentra registrado en el sistema con CI: "+ js_ced);   
    }else{
        $(".cl_telefono1").removeClass("oculto");
        $('#mensaje').empty().html("");   
    }
}


</script>

@endsection

 


