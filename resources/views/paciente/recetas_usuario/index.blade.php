@extends('paciente.recetas_usuario.base')
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
    p {
      font-size: 14px;
      margin-left: 5px;
      margin-right: 5px;

    }

</style>

<div class="container-fluid">
    <div class="row ">
        <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
            <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
                <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                        <h4 style="color: white;">
                            <b>BIENVENIDO AL SISTEMA DE DETALLES DE RECETAS</b>
                        </h4> 
                    </div>
                    @if(!is_null($paciente))
                        <div class="row"> 
                            <div class="col-md-12" style="text-align: center;">
                                <h4 style="color: white;">
                                    <b>USUARIO: {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
                                    {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                                    </b>
                                </h4>
                            </div>
                        </div>  
                    @endif    
                    <br>
                </div>
            <div>
            <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
                <div class="parent" style="background-color: #ffffff">
                    <div style=" margin-right: 30px;">
                        @foreach($hist_recetas as $re_hist)
                                @php
                                    $fecha_orden = $re_hist->fechaini;
                                    if(!is_null($re_hist->fechaini)){
                                        $fecha_r =  Date('Y-m-d',strtotime($re_hist->fechaini));
                                    }
                                    $xedad = Carbon\Carbon::createFromDate(substr($re_hist->fecha_nacimiento, 0, 4), substr($re_hist->fecha_nacimiento, 5, 2), substr($re_hist->fecha_nacimiento, 8, 2))->age; 
                                @endphp
                            <div class="box @if($fecha_r != date('Y-m-d')) collapsed-box @endif" style="border: 2px solid #4682B4; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 1px;margin-top: 0px;">
                                <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                    <div class="col-md-5">
                                        <span> <b style="font-family: 'Helvetica';padding-top: 7px" class="box-title">
                                        PACIENTE: {{$re_hist->papellido1}} @if($re_hist->papellido2!='(N/A)') {{$re_hist->papellido2}} @endif {{$re_hist->pnombre1}} @if($re_hist->pnombre2!='(N/A)') {{$re_hist->pnombre2}} @endif
                                        </b></span>
                                    </div>
                                    <div class="col-md-5">  
                                        @if(!is_null($re_hist))
                                            @php
                                                $dia =  Date('N',strtotime($re_hist->fechaini)); 
                                                $mes =  Date('n',strtotime($re_hist->fechaini)); 
                                            @endphp
                                            <span> <b style="font-family: 'Helvetica';" class="box-title">
                                                @if($dia == '1') Lunes 
                                                @elseif($dia == '2') Martes
                                                @elseif($dia == '3') Miércoles 
                                                @elseif($dia == '4') Jueves 
                                                @elseif($dia == '5') Viernes 
                                                @elseif($dia == '6') Sábado 
                                                @elseif($dia == '7') Domingo 
                                                @endif 
                                                {{substr($re_hist->fechaini,8,2)}} de 
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
                                                del {{substr($re_hist->fechaini,0,4)}}</b></span>
                                            
                                        @endif
                                    </div>  
                                    <div class="pull-right box-tools ">
                                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                                            <i class="fa @if($fecha_r != date('Y-m-d')) fa-plus @else  fa-minus @endif"></i>
                                        </button>
                                    </div>
                                    </div>
                                    <div class="box-body">
                                    <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                                        <div class="col-12">
                                            <div class="row">
                                            <div class="col-md-8" style="border: 2px solid #4682B4;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white; height: 30%; margin-bottom: 10px">
                                                <div class="col-md-12"  style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; text-align: center ">
                                                    <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                                        Detalle de la Orden de Receta
                                                    </label>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-3" ><b>Cédula:</b></div>
                                                    <div class="col-md-3" >{{$re_hist->id_paciente}}</div>
                                                    <div class="col-md-3"><b>Parentesco</b></div>
                                                    @if($re_hist->id_paciente != $paciente->id)
                                                    <div class="col-md-3">Familiar</div>
                                                    @else
                                                    <div class="col-md-3">Titular</div>
                                                    @endif
                                                    <div class="col-md-3" ><b>Edad:</b></div>
                                                    <div class="col-md-3" >{{$xedad}} años</div>
                                                    <div class="col-md-3" ><b>Seguro:</b></div>
                                                    <div class="col-md-3" >{{$re_hist->snombre}}</div>
                                                    <div class="col-md-3" ><b>Doctor:</b></div>
                                                    <div class="col-md-3" > @if($re_hist->id_doctor1!='9666666666'){{$re_hist->dnombre1}} {{$re_hist->dapellido1}}@endif</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <center>
                                                    <div class="col-md-12 col-8">
                                                    <a href="{{ route('receta_imprime', ['id' => $re_hist->id, 'tipo' => '2']) }}" style="width: 100%; color: white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;" class="btn btn-info boton-lab">
                                                        <span style="color: white" class="glyphicon glyphicon-print"></span> Imprimir Receta
                                                    </a>
                                                    </div>
                                                </center>
                                            </div>
                                        </div>
                                        
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="border-radius: 0px;margin-left: 0px;padding-left: 0px;">
                                        <div style="border: 2px solid #4682B4;background-color:#FFFFFF; border-radius: 10px;" id="{{$re_hist->id}}">
                                            <div class="col-md-12" class="box-header " style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; height: 30px; text-align: center;">
                                                <label class="box-title" style="background-color: #4682B4;  font-size: 16px">
                                                    &nbsp;Receta y Prescripción
                                                </label>
                                            </div>
                                            <div class="col-12" style="padding: 7px;">
                                                <div class="row">
                                                    <div class="col-12" style="width: 99%;padding-left: 12px">
                                                        <div class="contenedor2" id="receta{{$re_hist->id}}">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Rp</label>
                                                                    <div id="trp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #004AC1; ">
                                                                        @if(!is_null($re_hist->rp))
                                                                        <p><?php echo $re_hist->rp?>
                                                                        </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Prescripcion</label>
                                                                    <div id="tprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                                                                        @if(!is_null($re_hist->prescripcion))
                                                                        <p><?php echo $re_hist->prescripcion?></p>
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
                            </div>
                            <br>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection