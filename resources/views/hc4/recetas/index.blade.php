<style type="text/css"> 
  .parent{
      overflow-y:scroll;
      height: 600px;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #004AC1;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #004AC1;
      box-shadow: inset 0px 0px 0px 3px #56ABE3;
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
<div id="area_index"  class="container-fluid" style="padding-left: 8px;">
  <div class="row">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#56ABE3;">
          <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;">
            <img style="width: 35px; margin-left: 15px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/receta.png"> 
            <b>RECETAS</b>
            <br>
            @if(!is_null($paciente)) 
              <center> 
                <div class="col-12" style="padding-bottom: 20px;">
                  <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
                      <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                            {{$paciente->nombre1}} {{$paciente->nombre2}}
                      </b>
                  </h1>
                </div> 
              </center>
            @endif  
          </h1>
        <div>
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >
          <!--Hacemos el llamado al estilo parent-->
            <div class="parent" style="background-color: #56ABE3">
              <div style=" margin-right: 30px;" > 
              <!-- FOREACH DE LAS RECETAS --> 
              @if($hist_recetas != null)
                @foreach($hist_recetas as $re_hist)
                
                <!--Contenedor de la Fecha-->
                <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
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
                      @endif
                    </div>
                    <div class="pull-right box-tools ">
                      <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa fa-minus"></i>
                      </button>
                    </div>
                  </div>

                  <div class="box-body">
                    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                      <div class="col-12">
                        <div class="row">
                          <div class="col-6" style="text-align: right;" >
                              <label style="font-family: 'Helvetica general';" >Seguro:</label>
                          </div>
                          <div class="col-6">
                            @if(!is_null($re_hist->nombre))
                              {{$re_hist->nombre}} 
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <!--Contenedor Historial de Recetas Rp y Prescripcion-->
                        
                        <div class="col-md-12" style="padding-left: 110px" >
                          <div class="row">
                            <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">                      
                              <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $re_hist->id, 'tipo' => '2']) }}"> 
                              <div class="col-md-12" style="text-align: center; ">
                                <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                  <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                    <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                  </div>
                                  <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                    <label style="font-size: 14px; ">Imprimir Membretada</label>
                                  </div>  
                                </div>
                              </div>
                              </a>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
                              <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $re_hist->id, 'tipo' => '1']) }}"> 
                                <div class="col-md-12" style="text-align: center">
                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                      <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                    </div>
                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                      <label style="font-size: 14px">Imprimir</label>
                                    </div>  
                                  </div>
                                </div>
                              </a>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-11 col-sm-11 col-11" style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;background-color:#004AC1;">
                          <!--Contenedor Historial de Recetas-->
                          <div  style="background-color: #004AC1; color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                            <div class="box-title" style="background-color: #004AC1; margin-left: 10px">
                              <div class="row">
                                <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">
                                  <div class="btn" style="color: white">
                                    <a class="fa fa-pencil-square-o " onclick="editarreceta({{$re_hist->id}},'{{$paciente->id}}');"><span style="font-size: 13px">&nbsp;Editar</span>
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
                            <div class="col-md-12">
                              <div class="row">
                                <div class="col-md-6">
                                  <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                  <div id="trp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #004AC1; ">
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
                                  <span><b style="font-family: 'Helvetica general';" class="box-title">@lang('hc4.prescripcion')</b></span>
                                  <div id="tprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
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
                      </div>   
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
          <!--FIN DE FOREACH-->
          </div>
        </div> 
      </div>  
    </div>
  </div>
  </div>
</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function editarreceta(id,idpaciente){
           $.ajax({
           type: "GET",
           url: "{{route('paciente.actualiza.receta')}}/"+id+'/'+idpaciente, 
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

