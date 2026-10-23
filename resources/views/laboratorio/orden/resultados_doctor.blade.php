@extends('laboratorio.orden.base')

@section('action-content')

<style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .table-hover>tbody>tr:hover{
      background-color: #b3ffe6;
      cursor:pointer;
    }
   
</style>

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
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">    
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                          
                            <div class="table-responsive">
                              <table id="example2" class="table table-bordered dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                <tbody>
                                  <tr role="row">
                                    <td><b>Paciente:</b></td>
                                    <td>{{$orden->id_paciente}}</td>
                                    <td>{{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</td>
                                    <td><b>@if($orden->est_amb_hos==0) AMBULATORIO @else HOSPITALIZADO @endif</b></td>         
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-9">
                        <a class="btn btn-primary" onclick="goBack()">Regresar</a>
                        <a class="btn btn-primary" href="{{ route('resultados.imprimir',['id' => $orden->id]) }}">Imprimir</a>
                    </div>
                    @foreach($agrupador as $value)
                    @php  $i = 0;@endphp
                        @foreach($detalle as $validador_2)
                            @if($validador_2->examen->id_agrupador == $value->id)
                                @if($i == 0)
                                <div class="col-md-12">
                                    <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;">    
                                      <h2 class="box-title"><b>{{$value->nombre}}</b></h2>
                                      @php $indicador=0; @endphp
                                    </div>  
                                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                      
                                      @foreach($detalle as $value_detalle)
                                        @if($value_detalle->examen->id_agrupador == $value->id)
                                        <div class="table-responsive">
                                          @if($value_detalle->examen->id_agrupador != 2 && $value_detalle->examen->id_agrupador != 8)
                                          <div style="border-bottom: 1px solid black;padding: 0px;">
                                            {{$value_detalle->examen->nombre}}
                                            
                                          </div>
                                          @endif
                                          
                                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
                                            
                                              @if($indicador==0)
                                            <thead>
                                                <tr>
                                                    <td width="30%"><b>NOMBRE</b></td>
                                                    <td width="20%"><b>RESULTADO</b></td>
                                                    <td width="10%"><b>UNIDADES</b></td>
                                                    <td width="40%"><b>REFERENCIA</b></td>
                                                </tr>
                                            </thead>
                                            @php $indicador=1; @endphp
                                            @endif
                                             
                                            <tbody>
                                              @php
                                                
                                               if($value_detalle->examen->sexo_n_s=='0'){
                                                  $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                                                }else{
                                                  $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                                }

                                              @endphp
                                              @foreach($parametro_nuevo as $value_agrupador)
                                                @php
                                                $resultado = $resultados->where('id_orden', $value_detalle->id_examen_orden)->where('id_parametro', $value_agrupador->id)->first();
                                                @endphp

                                                @if($orden->created_at > Date('Y-m-d'))
                                                  <tr role="row" >
                                                    <td width="30%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->nombre}}</td>
                                                    <td width="20%" style="padding-top: 2px;padding-bottom: 2px;"><span id="{{$value_agrupador->id}}">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</span>
                                                        <!--a href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</a-->
                                                    </td>
                                                    <td width="10%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->unidad1}}</td>
                                                    <td width="40%" style="padding-top: 2px;padding-bottom: 2px;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                                          
                                                  </tr>
                                                @else 
                                                  @if(!is_null($resultado))
                                                  <tr role="row" >
                                                    <td width="30%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->nombre}}</td>
                                                    <td width="20%" style="padding-top: 2px;padding-bottom: 2px;"><span id="{{$value_agrupador->id}}">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</span>
                                                        <!--a href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</a-->
                                                    </td>
                                                    <td width="10%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->unidad1}}</td>
                                                    <td width="40%" style="padding-top: 2px;padding-bottom: 2px;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                                          
                                                  </tr>
                                                  @endif
                                                @endif  

                                              @endforeach
                                            </tbody>
                                          </table>
                                        </div>
                                           
                                        @endif
                                    @endforeach
                                    </div>
                                </div>
                                     @php 
                                        $i = 1;
                                    @endphp
                            @endif
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
  
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

  function goBack() {
      window.history.back();
  }

</script>
@endsection
