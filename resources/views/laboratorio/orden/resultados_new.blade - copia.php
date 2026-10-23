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

  <!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea_sub" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                          <div class="col-md-6"><h4>Paciente : <span style="color: red;">{{$orden->id_paciente}}-{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif</span></div>
                          <div class="col-md-2"><h4>Edad : <span id="edad" name="edad" style="color: red;"></span></h4></div>

                          
                          <button class="btn btn-primary btn-sm" id="btn_val">Imprimir</button>
                          
                          <label onclick="todo()">Certificar Todo</label>
                          <input type="checkbox"  id="cbox2" > 
                         
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                      <label for="fecha" class="texto col-md-3 control-label">Fecha Resultado</label>
                      <div class="col-md-9">
                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control input-sm" name="fecha_resultado" id="fecha_resultado" autocomplete="off" value="@if($orden->fecha_convenios != null){{date('Y/m/d H:i:s',strtotime($orden->fecha_convenios))}}@else{{date('Y/m/d H:i:s',strtotime($orden->fecha_orden))}}@endif">
                          <div class="input-group-addon">
                            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_resultado').value = ''; buscar();"></i>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12" style="padding: 5px;">
                    <div style="width: 30%;float:left;" ><b>NOMBRE</b></div>
                    <div style="width: 15%;float:left;" ><b>RESULTADO</b></div>
                    <div style="width: 10%;float:left;" ><b>UNIDADES</b></div>
                    <div style="width: 35%;float:left;" ><b>REFERENCIA</b></div>
                    <div style="width: 10%;float:left;" ><b>CERTIFICAR</b></div>
                      @foreach($resultados as $resultado)
                          <div style="clear:both;"></div>
                          <div style="border-bottom: 1px solid #009a98;padding: 0px;clear:both;">
                            <b>{{$resultado->parametro}}</b>
                            @if($resultado->tiene_detalle=='1') 
                              <a href="{{route('subresultado.crear',['id_orden' => $orden->id, 'id_examen' => $value_detalle->id_examen])}}" data-toggle="modal" data-target="#edit_crea_sub" class="btn btn-success btn-xs" ><span class="glyphicon glyphicon-plus"></span> Detalle</a>
                            @endif 
                          </div>
                          @if($resultado->texto!=null)
                            <div style="border-bottom: 1px solid #009a98;padding: 0px;">
                              <?php echo $value_detalle->examen->texto; ?>
                            </div>
                          @endif
                          <div style="border-bottom: 1px solid white;padding: 0px;clear:both;"></div>
                          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                          <div class="table-responsive">
                            <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;;    table-layout: fixed;width: 100%;">
                              <tbody>                   
                                @php
                                  $fl_ref = 0;
                                  $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$resultado->id_parametro)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                  if(!is_null($ref_ant)){
                                    $fl_ref = 1;
                                  }
                                @endphp
                                <tr>
                                  <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $orden->id,'id_parametro' => $resultado->id_parametro]) }}" data-toggle="modal" data-target="#edit_crea" width="15%" style="padding-top: 2px;padding-bottom: 2px;"><span class="prueba" id="{{$resultado->id_agrupador}}">{{$resultado->valor}}</span>   
                                    @if($fl_ref)
                                      @if($ref_ant->texto_referencia == "")
                                        @php $xvar = str_replace('*', '', $resultado->valor); @endphp
                                        @if(is_numeric($ref_ant->valor1g))
                                          @if($xvar >$ref_ant->valor1g)
                                            <span id="rg2{{$resultado->id_agrupador}}" style="color: red">SUPERIOR AL RANGO</span>
                                          @endif
                                        @endif   
                                        @if(is_numeric($ref_ant->valor1))
                                          @if($xvar <$ref_ant->valor1)
                                            <span id="rg2{{$resultado->id_agrupador}}" style="color: red">INFERIOR AL RANGO</span>
                                          @endif  
                                        @endif
                                      @endif
                                    @else
                                      @if($resultado->texto_referencia == "")
                                        @php $xvar = str_replace('*', '', $resultado->valor); @endphp
                                        @if(is_numeric($resultado->valor1g))
                                          @if($xvar >$resultado->valor1g)
                                            <span id="rg2{{$resultado->id}}" style="color: red">SUPERIOR AL RANGO</span>
                                          @endif
                                        @endif   
                                        @if(is_numeric($resultado->valor1))
                                          @if($xvar <$resultado->valor1)
                                            <span id="rg2{{$resultado->id}}" style="color: red">INFERIOR AL RANGO</span>
                                          @endif  
                                        @endif
                                      @endif
                                    @endif
                                    <span id="rg{{$resultado->id_agrupador}}" style="color: red"></span>     
                                  </td>
                                  <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $orden->id,'id_parametro' => $resultado->id_parametro]) }}" data-toggle="modal" data-target="#edit_crea" width="10%" style="padding-top: 2px;padding-bottom: 2px;"> @if($fl_ref) {{$ref_ant->unidad1}} @else {{$resultado->unidad1}} @endif</td>
                                  <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $orden->id,'id_parametro' => $resultado->id_parametro]) }}" data-toggle="modal" data-target="#edit_crea" width="35%" style="padding-top: 2px;padding-bottom: 2px;"> 
                                    @if($fl_ref)
                                      @if($ref_ant->texto_referencia == "")
                                        {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}  
                                      @else
                                        <?php echo $ref_ant->texto_referencia; ?> 
                                      @endif 
                                    @else 
                                      @if($resultado->texto_referencia == "")
                                        {{$resultado->valor1}} - {{$resultado->valor1g}} 
                                      @else
                                        <?php echo $resultado->texto_referencia; ?> 
                                      @endif
                                    @endif
                                  </td>
                                  <td width="10%" style="padding-top: 2px;padding-bottom: 2px;"><input id="ch{{$resultado->id_agrupador}}" name="ch{{$resultado->id_agrupador}}" type="checkbox" class="flat-orange"@if(!is_null($resultado)) enabled @if($resultado->certificado=='1') checked @endif @else disabled @endif</td>
                                </tr>
                                @if($resultado->tiene_detalle=='1')
                                  @php
                                    $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$resultado->id_examen)->get();
                                  @endphp
                                  <tr >
                                    <td colspan="5" id="sub_tabla{{$value_detalle->id_examen}}" style="padding: 0px;" >
                                      <table class="table table-bordered table-hover dataTable" style="width: 100%;">
                                        <tbody>
                                          @foreach($sub_resultados as $sub)
                                            <tr id="sr{{$sub->id}}">

                                              <td style="width: 30% !important;padding: 0px;">{{$sub->campo1}}</td>
                                              <td style="width: 30% !important;padding: 0px;">{{$sub->campo2}}</td>
                                              <td style="width: 30% !important;padding: 0px;">{{$sub->campo3}}</td>
                                              <td style="width: 10% !important;padding: 0px;"><button class="btn btn-sm btn-danger" onclick="elimina_sub('{{$sub->id}}')" href=""><i class="glyphicon glyphicon-trash"></i></button></td>
                                            </tr>
                                          @endforeach
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                @endif
                                          
                              </tbody>
                            </table>
                          </div>
                      @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
$(document).ready(function(){
  $('input[type="radio"].flat-orange2').iCheck({
    checkboxClass: 'flat-orange2',
    radioClass: 'iradio_square-grey',
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
    console.log(this.name.substring(2));

      $.ajax({
        type: 'get',
        url:"{{ url('certificar/resultado') }}/{{$orden->id}}/"+this.name.substring(2)+"/1/0",

        success: function(data){

          //alert(data);
          //console.log(data);

        },


        error: function(data){


        }
      });

  });
  $('input[type="radio"].flat-orange2').on('ifChecked', function(event){

    //console.log(this);
    $.ajax({
      type: 'get',
      url: "{{url('guardar/testdealimentos')}}/"+this.name.substring(2)+"/"+this.value+"/{{$orden->id}}",

      success: function(data){

        $('#ch'+data).iCheck('enable');

      },


      error: function(data){


      }
    });

  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
    @if(!is_null($usuario_aut))
      $.ajax({
        type: 'get',
        url:"{{ url('certificar/resultado') }}/{{$orden->id}}/"+this.name.substring(2)+"/0/{{$maq}}",

        success: function(data){

          //alert(data);

        },


        error: function(data){


        }
      });
    @else
      alert("Usuario No autorizado a Certificar");
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
          alert("No tiene exámenes certificados");
        }

      },


      error: function(data){


      }
    });

  });

  function elimina_sub(id){
    var confirmar = confirm("Desea Eliminar Resultado");
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

  document.getElementById("cbox2").addEventListener('click',function(){
    @if(!is_null($usuario_aut))
      var array = [];
      if(this.value == 'on'){
        var campo = document.getElementsByClassName("prueba");
        if (campo.length > 0) {
          for (let i = 0; i < campo.length; i++) {
            if(campo[i].innerText != '0'){
            var campitos = campo[i].parentNode.nextElementSibling.nextElementSibling.nextElementSibling.children;
              for (let index = 0; index < campitos.length; index++) {
                for (let j = 0; j < campitos[index].children.length; j++) {       
                  if(campitos[index].children[j].name != null && campitos[index].children[j].name != ''){ 
                    var campos = {};
                    campos.dato = 1;
                    campos.id_orden = {{$orden->id}};
                    campos.id = campitos[index].children[j].name.substring(2);
                    array.push(campos);
                  }
                }
              }
            }
          }
        }
        if(array.length > 0){
          $.ajax({
              url: "{{route('ordenes.check_nuevo')}}",
              headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
              },
              data: {
                  'datos': array, 'maq':'{{$maq}}',
              },
              type: 'POST',
              dataType: 'json',
              success: function(data) {
              console.log(data);
                 if(data == 'ok'){

                   location.reload();
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
      }
    @else
      alert("Usuario No autorizado a Certificar");
    @endif  
  });


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
