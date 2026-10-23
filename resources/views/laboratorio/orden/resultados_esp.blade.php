@extends('laboratorio.orden.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .table-hover>tbody>tr:hover{
      background-color: #b3ffe6;
      cursor:pointer;
    }
    p{
      font-size:12px;
    }

</style>

@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp



<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

 
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="col-md-6"><h4>{{trans('laboratorio.paciente')}} : <span style="color: red;">{{$orden->id_paciente}}-{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif</span></div>
                            <div class="col-md-2"><h4>{{trans('laboratorio.edad')}} : <span id="edad" name="edad" style="color: red;"></span></h4></div>

                            
                            <button class="btn btn-primary btn-sm" id="btn_val">  {{trans('laboratorio.imprimir')}}</button>
                           
                         
                        </div>
                    </div> 

                   
                      @foreach($detalles as $detalle)
                        @php
                         
                        @endphp
                        @if(  $detalle->examen->resultado_especial == 'CUADRO_POS' )
                            
                            @php
                                
                                $parametros_nuevo = $parametros->where('id_examen', $detalle->id_examen);
                            @endphp
                            @if(count($parametros_nuevo) > 0)
                              <div class="col-md-6 col-sm-6" style="padding: 5px;">
                                <b>EXAMEN/PARAMETRO</b> 
                              </div>
                              <div class="col-md-3 col-sm-3" style="padding: 5px;">
                                <b>RESULTADO</b>
                              </div>
                              <div class="col-md-3 col-sm-3" style="padding: 5px;">
                                <b>CERTIFICADO</b>
                              </div>
                              <div class="col-md-6 col-sm-6" style="padding: 5px;">
                                <b>{{$detalle->examen->nombre}}</b> 
                              </div>
                              <div class="col-md-3 col-sm-3" style="padding: 5px;">&nbsp;
                                <!--input type="radio" id="exa_res_{{$detalle->id_examen}}_neg" name="exa_res_{{$detalle->id_examen}}" value="NEGATIVO" class="flat-red"  >
                                <label for="html">NEGATIVO</label>
                                <input type="radio" id="exa_res_{{$detalle->id_examen}}_pos" name="exa_res_{{$detalle->id_examen}}" value="POSITIVO" class="flat-red">
                                <label for="css">POSITIVO</label-->
                              </div>
                              <div class="col-md-3 col-sm-3" style="padding: 5px;">&nbsp;
                                <!--input id="exa_cer_{{$detalle->id_examen}}" name="exa_cer_{{$detalle->id_examen}}" type="checkbox" class="flat-orange"-->
                              </div>
                            @endif  
                            @foreach($parametros_nuevo as $ps)
                              @if($ps->id_parametro_ref == null)
                                @php $res = $resultados->where('id_parametro',$ps->id)->first(); @endphp
                                <div class="col-md-6 col-sm-6" style="padding: 5px;">
                                  {{$ps->nombre}} 
                                </div>
                                <div class="col-md-3 col-sm-3" style="padding: 5px;">
                                  <input type="radio" id="par_res_{{$ps->id}}_neg" name="par_res_{{$ps->id}}" value="NEGATIVO" class="flat-red" @if(!is_null($res)) @if($res->valor=='NEGATIVO') checked="checked" @endif @endif>
                                  <label for="html">NEGATIVO</label>
                                  <input type="radio" id="par_res_{{$ps->id}}_pos" name="par_res_{{$ps->id}}" value="POSITIVO" class="flat-red" @if(!is_null($res)) @if($res->valor=='POSITIVO') checked="checked" @endif @endif>
                                  <label for="css">POSITIVO</label>
                                </div>
                                <div class="col-md-3 col-sm-3" style="padding: 5px;">
                                  <input id="par_cer_{{$ps->id}}" name="par_cer_{{$ps->id}}" type="checkbox" class="flat-orange" @if(!is_null($res)) @if($res->certificado=='1') checked="checked" @endif @endif>
                                </div>
                              @endif  
                            @endforeach
                          
                        @endif    
                      @endforeach
               
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
$(document).ready(function(){
  $('input[type="radio"].flat-red').iCheck({
    checkboxClass: 'flat-red',
    radioClass: 'iradio_square-red',
  });
  $('#fecha_resultado').datetimepicker({
    format: 'YYYY/MM/DD HH:mm:ss',
    defaultDate: '@if($orden->fecha_convenios != null){{$orden->fecha_convenios}}@else{{date($orden->fecha_orden)}}@endif',
    
  });
  $("#fecha_resultado").on("dp.change", function(e) {
      cambiar_fecha_res();
  });
});
</script>
<script type="text/javascript">
  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  })

  $(".clickable-row").click(function() {
      //console.log(this);
      //$("#edit_crea").modal();
      //window.location = $(this).data("href");
      var url = $(this).data("href");
      //  alert(url);
      $.get(url,function(data) {
        $(".modal-content").html(data);
        $("#edit_crea").modal();
      });
  });




  $('#edit_crea').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#edit_crea_sub').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');


  });

  @php

    $usuario = Auth::user()->id;
    $usuario_aut = Sis_medico\Agenda_Permiso::where('id_usuario',$usuario)->first();

  @endphp

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){
    //console.log(this.name.substring(2));
    $.ajax({
        url: "{{route('orden.certificar_resultado_esp')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            'nombre': this.name, 'valor': this.value, 'id': '{{$orden->id}}',
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
        console.log(data);
          data.forEach(seleccionar_especial);
        },
        error: function(xhr, status) {
            alert('Existió un problema');
            //console.log(xhr);
        },
    });

     

  });
  $('input[type="radio"].flat-red').on('ifChecked', function(event){
    //console.log(this.name);
    //console.log(this.value);  
    $.ajax({
        url: "{{route('orden.guardar_resultado_esp')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            'nombre': this.name, 'valor': this.value, 'id': '{{$orden->id}}',
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
        console.log(data);
          data.forEach(seleccionar_especial);
        },
        error: function(xhr, status) {
            alert('Existió un problema');
            //console.log(xhr);
        },
    });
   

  });

  function seleccionar_especial(item, index, arr){
    
    console.log(item);
    $('#'+item).iCheck('check');

  }

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
    @if(!is_null($usuario_aut))
     
    @else
      alert(`{{trans('laboratorio.usuarioonautorizadoacertificar')}}`);
      location.reload();
    @endif
  });

  $(document).ready(function() {

    var edad = calcularEdad('<?php echo $orden->paciente->fecha_nacimiento; ?>');
    $('#edad').text( edad );

  });

  $( "#btn_val" ).click(function() {

    $.ajax({
      type: 'get',
      url:"{{ route('resultados.puede_imprimir',['id' => $orden->id]) }}",

      success: function(data){

        if(data.certificados>0){

          window.open('{{ route('resultados.imprimir',['id' => $orden->id]) }}');

        }else{
          alert(`{{trans('laboratorio.notieneexamcertificados')}}`);
        }

      },


      error: function(data){


      }
    });

  });

  function elimina_sub(id){
    var confirmar = confirm(`{{trans('laboratorio.deseaeliminarresultado')}}`);
    if(confirmar){
      $.ajax({
        type: 'get',
        url:"{{ url('subresultado/eliminar') }}/"+id,

        success: function(data){
          //alert(data);
          console.log($('#sr'+id));
          $('#sr'+id).hide();


        },


        error: function(data){


        }
      });
    }
  }

  


  function cambiar_fecha_res(){
    $.ajax({
        url: "{{route('orden.cambio_resultado')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            'id_orden': '{{$orden->id}}', 'fecha_resultado':$('#fecha_resultado').val(),
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
        console.log(data);
           if(data == 'ok'){
            alert("Fecha Resultado Actualizada");  
           }else{

            alert('Ocurrio un Error');
           }
        },
        error: function(xhr, status) {
            alert('Existió un problema');
            //console.log(xhr);
        },
    });  
  }

  







</script>
@endsection
