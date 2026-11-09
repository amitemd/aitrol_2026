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
  .hc4-recipe-content,
  .hc4-recipe-content * {
      color: #000000 !important;
  }
  #area_trabajo .hc4-recipe-item-header {
      display: flex !important;
      position: relative;
      align-items: center;
      justify-content: center;
      min-height: 52px;
      padding: 8px 54px 8px 18px !important;
      border: 0 !important;
      background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
      text-align: center !important;
  }
  #area_trabajo .hc4-recipe-item-date {
      display: block;
      width: 100% !important;
      margin: 0 !important;
      color: #fff !important;
      font-family: 'Helvetica', Arial, sans-serif;
      font-size: 14px;
      font-weight: 700;
      line-height: 1.3;
      text-align: center;
  }
  #area_trabajo .hc4-recipe-item-header .box-tools {
      position: absolute !important;
      top: 50%;
      right: 12px !important;
      float: none !important;
      margin: 0 !important;
      transform: translateY(-50%);
  }
  #area_trabajo .hc4-recipe-record,
  #area_trabajo .hc4-recipe-record > .box-body,
  #area_trabajo .hc4-recipe-record > .box-body > .col-md-12 {
      box-sizing: border-box;
      width: 100% !important;
      max-width: 100% !important;
      margin-right: 0 !important;
      margin-left: 0 !important;
  }
  #area_trabajo .hc4-recipe-record {
      min-width: 0;
      overflow: hidden;
  }
  #area_trabajo .hc4-recipe-record > .box-body {
      padding: 12px !important;
      overflow-x: hidden;
  }
  #area_trabajo .hc4-recipe-record > .box-body > .col-md-12 {
      min-width: 0;
      padding-right: 0 !important;
      padding-left: 0 !important;
  }
  #area_trabajo .hc4-recipe-record .row {
      box-sizing: border-box;
      width: 100% !important;
      max-width: 100% !important;
      margin-right: 0 !important;
      margin-left: 0 !important;
  }
  #area_trabajo .hc4-recipe-print-actions {
      display: flex;
      align-items: stretch;
      justify-content: center;
      gap: 14px;
      width: 100% !important;
      max-width: none !important;
      margin: 0 !important;
      padding: 0 0 10px !important;
  }
  #area_trabajo .hc4-recipe-print-action {
      flex: 0 1 250px;
      width: auto !important;
      max-width: 250px;
      margin: 0 !important;
      padding: 0 !important;
  }
  #area_trabajo .hc4-recipe-print-action .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      width: 100% !important;
      min-height: 38px;
  }
  #area_trabajo .hc4-recipe-print-action .btn img {
      flex: 0 0 18px;
      width: 18px;
  }
  #area_trabajo .hc4-recipe-print-action .btn label {
      margin: 0 !important;
      cursor: pointer;
  }
  #area_trabajo .hc4-recipe-history {
      box-sizing: border-box;
      min-width: 0;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
  }
  #area_trabajo .hc4-recipe-history .contenedor2 {
      box-sizing: border-box;
      width: 100% !important;
      max-width: 100% !important;
      padding: 12px !important;
      overflow-x: hidden;
  }
  @media (max-width: 640px) {
      #area_trabajo .hc4-recipe-print-actions {
          flex-direction: column;
      }
      #area_trabajo .hc4-recipe-print-action {
          flex-basis: auto;
          width: 100% !important;
          max-width: none;
      }
  }
</style>
<div id="area_index" class="container-fluid hc4-recipes-view" style="padding: 0;">
  <div class="row">
    <div class="col-md-12" style="padding: 0; margin: 0; border-radius: 10px;">
      <div class="col-md-12" style="border: 2px solid #004AC1; margin: 0; padding: 0; background-color:#56ABE3;">
          @include('hc4.partials.module_header', [
              'moduleTitle' => __('hc4.recetas-'),
              'moduleIcon' => asset('/').'hc4/img/iconos/receta.png',
              'paciente' => $paciente
          ])
        <div>
          <div class="hc4-recipes-list-shell" style="padding: 12px 8px 0; background-color: #56ABE3; margin: 0;"  >
          <!--Hacemos el llamado al estilo parent-->
            <div class="parent" style="background-color: #56ABE3">
              <div style="width: 100%; margin: 0;">
              <!-- FOREACH DE LAS RECETAS --> 
              @if($hist_recetas != null)
                @foreach($hist_recetas as $re_hist)
                
                <!--Contenedor de la Fecha-->
                <div class="box collapsed-box hc4-recipe-record" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                  <div class="box-header with-border hc4-recipe-item-header" style="font-family: 'Helvetica general3'; border-bottom: #004AC1;">
                  
                   
                    @php
                      $fechaReceta = !is_null($re_hist->fecha_atencion)
                        ? $re_hist->fecha_atencion
                        : $re_hist->fechaini;
                    @endphp
                    @if(!is_null($fechaReceta))
                      @php
                      $dia = Date('N', strtotime($fechaReceta));
                      $mes = Date('n', strtotime($fechaReceta));
                      @endphp
                      <b>
                      <span class="box-title hc4-recipe-item-date">
                        @if($dia == '1') Lunes 
                           @elseif($dia == '2') Martes
                           @elseif($dia == '3') Miércoles 
                           @elseif($dia == '4') Jueves 
                           @elseif($dia == '5') Viernes 
                           @elseif($dia == '6') Sábado 
                           @elseif($dia == '7') Domingo 
                        @endif 
                          {{substr($fechaReceta,8,2)}} de
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
                          del {{substr($fechaReceta,0,4)}}</span></b>
                    @endif
                

                    <div class="pull-right box-tools ">
                      <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>

                  <div class="box-body">
                    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                      <div class="col-12">
                        <div class="row">
                          <div class="col-6" style="text-align: right;" >
									<label style="font-family: 'Helvetica general';" >@lang('hc4.seguro'):</label>
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
                        
                        <div class="col-md-12 hc4-recipe-print-actions">
                          <div class="hc4-recipe-print-action">
                            <div>
                              <a target="_blank" class="btn btn-info btn_accion" style="width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $re_hist->id, 'tipo' => '2']) }}">
                                <img src="{{asset('/')}}hc4/img/iconos/descargar.png" alt="">
								<label style="font-size: 14px; ">@lang('hc4.imprimirMembretada')</label>
                              </a>
                            </div>
                            </div>
                            <div class="hc4-recipe-print-action">
                              <div>
                              <a target="_blank" class="btn btn-info btn_accion" style="width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $re_hist->id, 'tipo' => '1']) }}">
                                  <img src="{{asset('/')}}hc4/img/iconos/descargar.png" alt="">
								  <label style="font-size: 14px">@lang('hc4.imprimir')</label>
                              </a>
                              </div>
                            </div>
                        </div>

                        <div class="col-md-12 hc4-recipe-history" style="border: 2px solid #004AC1; border-radius: 3px; background-color:#004AC1;">
                          <!--Contenedor Historial de Recetas-->
                          <div  style="background-color: #004AC1; color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                            <div class="box-title" style="background-color: #004AC1; margin-left: 10px">
                              <div class="row">
                                <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">
                                  <div class="btn" style="color: white">
						<a class="fa fa-pencil-square-o " onclick="editarreceta({{$re_hist->id}},'{{$paciente->id}}');"><span style="font-size: 13px">&nbsp;@lang('hc4.editar')</span>
                                    </a>
                                  </div>
                                </div> 
                                <div class="col-md-8 col-sm-8 col-8" style="padding-top: 4px">
								<span>@lang('hc4.historialRecetas')</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="contenedor2" id="receta{{$re_hist->id}}" style="padding-bottom: 20px; padding-right: 15px">
                            <div class="col-md-12">
                              <div class="row">
                                <div class="col-md-6">
                                  <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                  <div id="trp" class="hc4-recipe-content" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #004AC1; ">
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
                                  <div id="tprescripcion" class="hc4-recipe-content" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
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

