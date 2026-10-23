@extends('enfermeria.baseinsumo2')
@section('action-content')
<style type="text/css">
  .alerta_correcto{
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }
  .btn{
        font-size: 15px;
        font-weight: bold;
    }
</style>
<!-- Main content -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">

  <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    Observacion Guardada Correctamente
  </div>

  <div class="box">
    <div class="box-header">

      <div class="row">
        <div class="col-md-12" style="text-align: right;">
          <a type="button" href="{{route('enfermeria.index_insumos')}}" class="btn btn-primary btn-sm">
          <span class="glyphicon glyphicon-arrow-left">Regresar</span>
          </a>
        </div>
        <div class="col-md-12">
            <div class="col-md-10"><h4 class="box-title"><b>Paciente: </b><span style="color: red;">{{$agenda->id_paciente}}-{{$agenda->paciente->apellido1}} @if($agenda->paciente->apellido2!='(N/A)'){{$agenda->paciente->apellido2}}@endif {{$agenda->paciente->nombre1}} @if($agenda->paciente->nombre2!='(N/A)'){{$agenda->paciente->nombre2}}@endif </span></h4></div>
            <div class="col-md-5"><h4 class="box-title"><b>Seguro: </b> {{$agenda->seguro->nombre}}</h4></div><div class="col-md-5"><h4 class="box-title"><b>Fecha: </b> {{$agenda->fechaini}}</h4></div>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

      <div class="box box-warning">
        <h3><b>Procedimientos: </b></h3>
        <div class="row">
          @foreach($procedimientos as $value)
            @if($value->nombre_general==null)
              @php
                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                $eliminados = \Sis_medico\hc_procedimientos::where('id', $value->id)->first();
                $mas = true;
                $texto = "";

                foreach($adicionales as $value2)
                {
                  if($mas == true){
                   $texto = $texto.$value2->procedimiento->nombre;
                   $mas = false;
                  }
                  else{
                    $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                  }
                }
              @endphp
            @endif
            @php
              $productos = \Sis_medico\Movimiento_Paciente::where('id_hc_procedimientos', $value->id)->get();
            @endphp<br>
            <div class="col-md-10" align="center" >
              @php
                $record = \Sis_medico\Hc_Anestesiologia::where('id_hc_procedimientos', $value->id)->whereNotNull('url_imagen')->first();
                $id_usuario = Auth::user()->id;
                if($id_usuario == "0922729587" ){
                  //dd($value->id);

                }
              @endphp
              
                @if($eliminados->estado == '0') 
                  <button style="width:70%; height: 60px;  font-size: 12px; text-align: center; background-color: #919090" > <span style="color: black;"> <b> !! ELIMINADO!! <br> @if($value->nombre_general==null) @if(!is_null($texto)) {{$texto}} @else NO INGRESADO @endif @else{{$value->nombre_general}}@endif </b> </span> </button>
                @else
                  <a style="width:70%; height: 60px;  font-size: 12px; text-align: center" href="{{route('enfermeria.selec_prod',['id'=>$value->id])}}" class="btn @if(is_null($record)) btn-danger @else btn-primary @endif">@if(is_null($record)) FALTA RECORD ANESTESICO <br> @endif @if($value->nombre_general==null) @if(!is_null($texto)) {{$texto}} @else NO INGRESADO @endif @else{{$value->nombre_general}}@endif</a>
                @endif
              

            </div>
            <br>

          @endforeach
         </div>
         <br>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</section>
    <!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  function enviar(nombre) {

    $.ajax({
        type: 'post',
        url:"{{route('transito.serie_enfermero')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#codigo_enviar"+nombre).serialize(),
        success: function(data){
            console.log(data);
           if(data == 'No se encontraron resultados'){
              $('#agregar_respuesta'+nombre).html('No se encontraron resultados');
              $("#recibir"+nombre).focus();
            }else if(data == 'caducado'){
              alert('No se puede usar el medicamento, se encuentra caducado');
            }else{


              //console.log("{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir"+nombre);
              window.location.replace("{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir"+nombre);
              location.reload();

            }
            $('#codigo_enviar'+nombre)[0].reset();
        },
        error: function(data){
            console.log(data);
        }
    });
  }
  function enviar2(){

    $.ajax({
        type: 'post',
        url:"{{route('transito.serie_enfermero_equipo')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#codigo_enviar").serialize(),
        success: function(data){
            console.log(data);
            if(data == 'No se encontraron resultados'){
              $('#agregar_respuesta').html('No se encontraron resultados');
            }else if(data == 'caducado'){
              alert('No se puede usar el medicamento, se encuentra caducado');
            }else{
              window.location.reload();
            }
            $('#codigo_enviar')[0].reset();
        },
        error: function(data){
            console.log(data);
        }
    });
  }

  function eliminar_producto(id){
    var r = confirm("Esta seguro de eliminar el registro?");
    if (r == true) {
      $.ajax({
          type: 'get',
          url:"{{asset('enfermeria/uso/paciente_insumo/eliminar')}}/"+id,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: '',
          success: function(data){
            if(data == 'ok'){
              window.location.reload();
            }else{
              alert('No se puede eliminar el registro');
              console.log(data);
            }
          },
          error: function(data){
              console.log(data);
          }
      });
    }

  }

  function guardar_observacion(){

    $.ajax({
        type: 'post',
        url:"{{route('enfermeria.guardar_observacion')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#hc_observacion").serialize(),
        success: function(data){
            console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);

        },
        error: function(data){
            console.log(data);
        }
    });
  }

  function eliminar(id){
    var r = confirm("Esta seguro de eliminar el registro?");
    if (r == true) {
      $.ajax({
          type: 'get',
          url:"{{asset('producto/uso/paciente_equipo/eliminar')}}/"+id,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: '',
          success: function(data){
              console.log(data);
              window.location.reload();
          },
          error: function(data){
              console.log(data);
          }
      });
    }
  }
  function pulsar(e) {
    tecla = (document.all) ? e.keyCode :e.which;
    return (tecla!=13);
  }

  function guardar_msn(){
    $("#alerta_datos").fadeIn(1000);
    $("#alerta_datos").fadeOut(3000);
  }

</script>

@endsection
