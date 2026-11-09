@extends('hc_admision.orden_proc.base_biopsia')
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


<div class="container-fluid">
  <div class="row ">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
        <div class="row">
          <div class="col-md-9" style="padding-left:460px">
              <h1 style="font-size: 15px; margin:0; color: white;padding-top: 12px;">
                <b>HISTORIAL ORDENES DE BIOPSIAS</b>
              </h1> 
          </div>
          <br>
          @if(!is_null($paciente))
            @php
                $xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age; 
            @endphp 
            <div class="row"> 
              <div class="col-md-9" style="padding-top: 15px">
                <h1 style="font-size: 14px; margin:0;color: white;padding-left:20px" >
                  <b>PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
                    {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                    </b>
                </h1>
              </div>
              <div class="col-md-3" style="padding-top: 15px">
                <h1 style="font-size: 14px; margin:0;color: white;padding-left: 10px" >
                  <b>
                    EDAD: {{$xedad}} AÑOS
                  </b>
                </h1>
              </div> 
            </div>  
          @endif    
          <br>
        </div>
        <div>
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
            <div class="parent" style="background-color: #ffffff">
              <div style=" margin-right: 30px;">

                @if($biopsias != null)

                @foreach($biopsias as $bp)
                  @php
                    $biop_detalle = Sis_medico\Hc4_Biopsias::where('hc_id_procedimiento',$bp->hc_id_procedimiento)->get();
                    $biop_hcid = Sis_medico\Hc4_Biopsias::where('hc_id_procedimiento',$bp->hc_id_procedimiento)->first();
                    $cuadclinico_diagnostico = Sis_medico\hc_procedimientos::where('id',$bp->hc_id_procedimiento)->first();
                  @endphp
                <div class="box collapsed-box" style="border: 2px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                  <div class="box-header with-border" style="font-family: 'Helvetica general3';border-bottom: #004AC1;">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          @if(!is_null($biop_hcid->created_at))
                              @php 
                                $dia =  Date('N',strtotime($biop_hcid->created_at)); 
                                $mes =  Date('n',strtotime($biop_hcid->created_at)); 
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
                              {{substr($biop_hcid->created_at,8,2)}} de
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
                              del {{substr($biop_hcid->created_at,0,4)}}</span></b>  
                           @endif
                        </div>
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-2">
                          <a target="_blank" class="btn btn-danger" style="color:white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;" href="{{ route('imprimir.orden_biopsias_recepcion', ['id' => $bp->hc_id_procedimiento,'id_hcid' => $bp->hcid,'id_doct' => $bp->id_doctor]) }}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Imprimir Orden Biopsia</a>
                        </div>
                        <div class="pull-right box-tools ">
                          <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="box-body" style="background: white;">
                      <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
                        <div class="box" style="border: 2px solid #4682B4; background-color: white; border-radius: 3px; margin-bottom: 0;">
                          <div class="box-header with-border" style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4;padding: 2px;">
                            <div class="col-md-12">
                              <div class="row">
                                  <div class="col-3" style="text-align: center;"> 
                                      <span >Informaciòn Biopsia</span>
                                  </div>
                              </div>
                            </div>
                          </div>
                          <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general';" id="d">
                            <br>
                              <div class="col-12">
                                <table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;border-color: #4682B4;">
                                  <tr>
                                    <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;background-color: #4682B4;color: white;border-color: currentColor;text-align: center;"><b>&nbsp;Detalle Frascos</b></td>
                                  </tr>
                                  @foreach($biop_detalle as $value)
                                  <tr>
                                   <td colspan="2" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px;"><p style="width: 100%; border: none; margin:0">Fco {{$value->numero_frasco}}: {{$value->descripcion_frasco}}</p></td>
                                   <td colspan="3" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px,"><p style="width: 100%; border: none; margin:0">Obs: {{$value->observacion}}</p></td>
                                  </tr>
                                  @endforeach
                                </table>
                              </div>
                            <div class="col-md-12" style="padding-top: 10px"></div>
                            <div class="col-md-12" style="padding: 1px;">
                              <div class="row">
                                <div class="col-md-12">
                                  <label style="font-family: 'Helvetica general';">CUADRO CLINICO:</label> 
                                </div>
                                <div class="col-12" style="padding: 15px;">
                                  <?php echo $cuadclinico_diagnostico->cuadro_clinico_bp?>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12" style="padding: 1px;">
                              <div class="row">
                                <div class="col-md-12">
                                  <label style="font-family: 'Helvetica general';">DIAGNOSTICO:</label> 
                                </div>
                                <div class="col-12" style="padding: 15px;">
                                  <?php echo $cuadclinico_diagnostico->diagnosticos_bp?>
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



              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


<script type="text/javascript">

</script>