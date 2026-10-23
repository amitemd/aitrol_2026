@extends('hc_admision.visita.base')
@section('action-content')

<style type="text/css"> 
  .parent{
      overflow-y:scroll;
      height: 600px;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #3c8dbc;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #3c8dbc;
      box-shadow: inset 0px 0px 0px 3px #3c8dbc;
  }
  .contenedor2{
        padding-left: 15px; 
        padding-right: 60px; 
        padding-top: 20px;
        padding-bottom: 0px;
        background-color: #FFFFFF;
        margin-left: 0px;
    }
</style>
<br>
<div class="container-fluid" >
  <div class="row ">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">

        <div class="row"> 
          <div class="col-md-8">
            <h1 style="font-size: 15px; margin:0; color: white;">
              <img style="width: 35px; margin-left: 15px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/receta.png"> 
              <b>Recetas del Paciente : {{$paciente->nombre1}} {{$paciente->nombre2}}
              {{$paciente->apellido1}} {{$paciente->apellido2}}</b>
            </h1>
          </div>
          <div class="col-md-4" style="top: 5px">
            <a class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;height:30px;" href='{{route('historiaclinica_paciente_nueva.receta',['id' =>$paciente->id])}}'>
              <img style="color: black" width="15px" src="{{asset('/')}}hc4/img/iconos/receta.png"> 
              <i aria-hidden="true"></i>Nueva Receta
            </a>
          </div>
        </div>
        <div>
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
            <div class="parent" style="background-color: #ffffff">
              <div style=" margin-right: 30px;">
                @if($hist_recetas != null)
                  @foreach($hist_recetas as $re_hist)
                      @php
                        $xfecha = (substr($re_hist->fecha_atencion,0,10));
                      @endphp 
                    <input type="hidden" name="fe_atencion"  id="fe_atencion" value="{{$xfecha}}">                         
                    <div class="box" style="border: 2px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                      <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                        @if(!is_null($re_hist->fecha_atencion))
                          @php 
                          $dia =  Date('N',strtotime($re_hist->fecha_atencion)); 
                          $mes =  Date('n',strtotime($re_hist->fecha_atencion)); 
                          @endphp
                        <b>
                        <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title" > 
                        @if($dia == '1') Lunes 
                           @elseif($dia == '2') Martes
                           @elseif($dia == '3') Miércoles 
                           @elseif($dia == '4') Jueves 
                           @elseif($dia == '5') Viernes 
                           @elseif($dia == '6') Sábado 
                           @elseif($dia == '7') Domingo 
                        @endif 
                          {{substr($re_hist->fecha_atencion,8,2)}} de 
                        @if($mes == '1') Enero 
                           @elseif($mes == '2') Febrero 
                           @elseif($mes == '3') Marzo 
                           @elseif($mes == '4') Abril 
                           @elseif($mes == '5') Mayo 
                           @elseif($mes == '6') Junio 
                           @elseif($mes == '7') Julio 
                           @elseif($mes == '8') Agosto 
                           @elseif($mes == '9') Septiembre 
                           @elseif($mes == '10') Octubre 
                           @elseif($mes == '11') Noviembre 
                           @elseif($mes == '12') Diciembre 
                        @endif 
                          del {{substr($re_hist->fecha_atencion,0,4)}}</span></b>
                        @endif
                        <div style="color: white"> 
                          @if(!is_null($re_hist->id)) 
                            {{$re_hist->id}} 
                            {{$xfecha}}
                          @endif
                        </div>
                        <div class="pull-right box-tools ">
                          <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <!--box-body-->
                      <div class="box-body">
                          <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                            <div class="row">
                              <div class="col-md-12">
                                  <div class="col-md-6">
                                    <label style="font-family: 'Helvetica general';">Doctor(a):</label>
                                      <span>
                                        @if($re_hist->id_doctor1!='9666666666')
                                            {{$re_hist->dnombre1}} {{$re_hist->dapellido1}}
                                        @endif
                                      </span>
                                  </div>
                                  <div class="col-md-6">
                                    <label style="font-family: 'Helvetica general';">Seguro:</label>
                                      <span>
                                        @if(!is_null($re_hist->nombre))
                                          {{$re_hist->nombre}} 
                                        @endif
                                      </span>
                                  </div>
                                </div>

                            <br><br>
                            <div class="col-md-9 col-sm-11 col-11" style="border: 2px solid #3c8dbc;margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;background-color:#ffffff;">
                                <!--Contenedor Historial de Recetas-->
                                <div  style="background-color: #3c8dbc; color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                                  <div class="box-title" style="background-color: #3c8dbc; margin-left: 10px">
                                    <div class="row">
                                      <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">
                                        <div class="btn" >
                                          <a style="color: white" class="fa fa-pencil-square-o" onclick="editarreceta({{$re_hist->id}},'{{$paciente->id}}');"><span style="font-size: 13px;color: white">&nbsp;Editar</span>
                                          </a>
                                        </div>
                                      </div> 
                                      <div class="col-md-8 col-sm-8 col-8" style="padding-top: 4px">
                                        <span>Historial de Recetas</span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="contenedor2" id="receta{{$re_hist->id}}" style="padding-bottom: 20px; padding-right: 15px">
                                  <div class="col-md-12" style="padding-bottom: 15px;">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                        <div id="trp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #3c8dbc; ">
                                        @php 
                                          $receta = Sis_medico\hc_receta::find($re_hist->id);
                                        @endphp
                                        @if(!is_null($receta))
                                          @php $rdetalles = $receta->detalles; @endphp
                                          @foreach($rdetalles as $detalle)
                                            @php $genericos = $detalle->medicina->genericos; @endphp
                                            <p>{{$detalle->nombre}} @foreach($genericos as $gen) ( {{$gen->generico->nombre}} ) @endforeach : {{$detalle->cantidad}} {{ $detalle->detalle_cantidad}}</p>
                                          @endforeach
                                          @if(!is_null($re_hist->rp))
                                          <p><?php echo $re_hist->rp?>
                                          </p>
                                          @endif
                                        @endif  
                                        </div>
                                      </div>
                                      <div class="col-md-6" >
                                        <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                                        <div id="tprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                                        @if(!is_null($receta))
                                          @php $rdetalles = $receta->detalles; @endphp
                                          @foreach($rdetalles as $detalle)
                                            @php $genericos = $detalle->medicina->genericos; @endphp
                                            <p>{{$detalle->nombre}}: {{$detalle->dosis}}</p>
                                          @endforeach
                                          @if(!is_null($re_hist->prescripcion))
                                            <p><?php echo $re_hist->prescripcion?></p>
                                          @endif
                                        @endif
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="row">
                                  <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">              <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime', ['id' => $re_hist->id, 'tipo' => '2']) }}"> 
                                    <div class="col-md-12" style="text-align: center; ">
                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                          <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        </div>
                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                          <label style="font-size: 14px;color: white">Imprimir Membretada</label>
                                        </div>  
                                      </div>
                                    </div>
                                    </a>
                                  </div>
                                  <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">
                                    <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime', ['id' => $re_hist->id, 'tipo' => '1']) }}"> 
                                    <div class="col-md-12" style="text-align: center;">
                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                          <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        </div>
                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px;">
                                          <label style="font-size: 14px;color: white">Imprimir</label>
                                        </div>  
                                      </div>
                                    </div>
                                    </a>
                                  </div>
                                  <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">
                                    <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('receta.nota_entrega', ['id' => $re_hist->id]) }}"> 
                                    <div class="col-md-12" style="text-align: center; ">
                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                          <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/receta.png">
                                        </div>
                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                          <label style="font-size: 14px;color: white">Nota de Entrega</label>
                                        </div>  
                                      </div>
                                    </div>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
                    <!--Fin box-body-->
                    </div>
                  @endforeach
                @endif
              </div>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
  function editarreceta(id,idpaciente){
           $.ajax({
           type: "GET",
           url: "{{route('historial.actualiza_receta')}}/"+id+'/'+idpaciente, 
           data: "",
           datatype: "html",
           success: function(datahtml){
           $("#receta"+id).html(datahtml);
           },
           error:  function(){
           alert('error al cargar');
           }
        });
  }
</script>
@endsection