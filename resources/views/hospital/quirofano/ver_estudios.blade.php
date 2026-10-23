
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

</style>

<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<style type="text/css">

  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    #mceu_61{
        display: none;

    }
    .nopad {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    /*image gallery*/



    @foreach($procedimientos2 as $value)
        .image-checkbox{{$value->id_protocolo}} {
            cursor: pointer;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            border: 4px solid transparent;
            margin-bottom: 0;
            outline: 0;
        }
        .image-checkbox{{$value->id_protocolo}} input[type="checkbox"] {
            display: none;
        }
        .image-checkbox{{$value->id_protocolo}} .fa {
          position: absolute;
          color: #004AC1;
          background-color: white;
          padding: 10px;
          top: 0;
          right: 0;
        }
    @endforeach



    @foreach($procedimientos1 as $value)

        .image-checkbox2{{$value->id_protocolo}} {
            cursor: pointer;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            border: 4px solid transparent;
            margin-bottom: 0;
            outline: 0;
        }
        .image-checkbox2{{$value->id_protocolo}} input[type="checkbox"] {
            display: none;
        }
        .image-checkbox2{{$value->id_protocolo}} .fa {
          position: absolute;
          color: #004AC1;
          background-color: white;
          padding: 10px;
          top: 0;
          right: 0;
        }


    @endforeach

    .image-checkbox-checked {
        border-color: red;
    }

    .image-checkbox-checked2 {
        border-color: red;
    }

    .image-checkbox-checked2 .fa {
      display: block !important;
    }

    .image-checkbox-checked .fa {
      display: block !important;
    }
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>
</div>

<div class="card">
	<div class="card-header bg bg-primary colorbasic">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <!--label class="colorbasic sradio" > 3</label--> 
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px" ><b>{{trans('transquirofano.Pre-VisualizarEstudios')}}</b>  </label> <br>
                    <label style="font-size: 16px;"><b>Paciente: {{$solicitud->paciente->apellido1}} {{$solicitud->paciente->apellido2}} {{$solicitud->paciente->nombre1}} {{$solicitud->paciente->nombre2}}</b></label>
                </div>
                <div class="col-md-2">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="col-12">
            <div class="row" >
                <span id="msn1" style="color: white;"></span>
                @php
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
                @endphp
                @foreach($procedimientos2 as $value)

                    <div class="col-12 {{$value->id_protocolo}}">
                        <br>
                        <div class="card" >
                            <div class="card-header bg bg-primary colorbasic">
                                            @if(!is_null($value->f_operacion))
                                                @php
                                                    $dia =  Date('N',strtotime($value->f_operacion));
                                                    $mes =  Date('n',strtotime($value->f_operacion)); 
                                                    $aux = intval($dia)-1;
                                                    $ms = intval($mes)-1;
                                                @endphp
                                                <b>{{$dias[$aux]}} {{substr($value->f_operacion,8,2)}} de {{$meses[$ms]}} del {{substr($value->f_operacion,0,4)}}</b>
                                            @else
                                                @php
                                                $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                                $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); 
                                                $aux = intval($dia)-1;
                                                $ms = intval($mes)-1;
                                                @endphp

                                                
                                                <b> {{$dias[$aux]}} {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de {{$meses[$ms]}} del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                                            @endif
                                                                
                            </div>
                            <br>
                            <div class="card-header bg bg-primary colorbasic">
                                <div class="col-12" >
                                    @if(!is_null($value))
                                        @php
                                            $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();
                                                $mas = true;
                                                $texto = "";
                                                foreach($adicionales as $value2)
                                                {
                                                    if($mas == true){
                                                     $texto = $texto.$value2->procedimiento->nombre  ;
                                                     $mas = false;
                                                     }
                                                    else{
                                                     $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                                                     }
                                                }
                                        @endphp
                                    @endif
                                    {{$texto}}
                                </div>
                            </div>
                            <div class="card-body" >
                                <div class="row">

                                    <div class="col-md-9 col-sm-12 col-12" style="padding-left: 15px;">
                                        @php
                                            $id_protocolo = $value->id_protocolo;
                                            $imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->where('seleccionado','1')->count();

                                            //dd($imagenes);
                                            //echo $imagenes->last()->id;
                                        @endphp
                                        <br>
                                        <embed style="max-width: 900px; height: 500px" id="miIFrame" src="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '7' ])}}" width="100%" type='application/pdf'>
                                    </div>
                                    <div class="col-md-3 col-sm-12 col-12" style="padding-left: 10px; padding-top: 10px">

                                        <div class="col-12">
                                            <div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
                                                <a  type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $value->id_protocolo])}}" data-toggle="modal" data-target="#foto" style="width: 100%; height: 100%; font-size: 12px;" >
                                                    <div class="col-12" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;">
                                                        <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                        <label style="color: white; " >{{trans('transquirofano.DescargarenotroFormato')}}</label>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @php
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
                @endphp

                @foreach($procedimientos1 as $value)

                    <div class="col-12 {{$value->id_protocolo}}">
                        <div class="card" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                          <div class="card-header with-border" style="background-color: white; color: black; text-align: center; border-bottom: #004AC1;">
                                @if(!is_null($value->f_operacion))
                                                @php
                                                    $dia =  Date('N',strtotime($value->f_operacion));
                                                    $mes =  Date('n',strtotime($value->f_operacion)); 
                                                    $aux = intval($dia)-1;
                                                    $ms = intval($mes)-1;
                                                @endphp
                                                <b>{{$dias[$aux]}} {{substr($value->f_operacion,8,2)}} de {{$meses[$ms]}} del {{substr($value->f_operacion,0,4)}}</b>
                                            @else
                                                @php
                                                    $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                                    $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); 
                                                    $aux = intval($dia)-1;
                                                    $ms = intval($mes)-1;
                                                @endphp
                                                <b> {{$dias[$aux]}} {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de {{$meses[$ms]}} del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                                            @endif
                            <div class="pull-right card-tools">
                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                    <i class="fa fa-minus"></i></button>
                            </div>
                                <div class="col-12" style="margin-top: 20px; background-color: #004AC1; color: white">
                                    @php
                                        $procedimiento =  \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
                                        $procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                                    @endphp
                                    <span>{{$procedimiento_completo->nombre_general}}</span>
                                </div>

                          </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-9 col-sm-12 col-12" style="padding-left: 15px;">
                                        @php
                                            $id_protocolo = $value->id_protocolo;
                                            $imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->where('seleccionado','1')->count();

                                            //dd($imagenes);
                                            //echo $imagenes->last()->id;
                                        @endphp
                                        <embed style="max-width: 900px; height: 500px" id="miIFrame" src="{{route('hc_reporte.descargar', ['id'=> $id_protocolo, 'tipo' => '7' ])}}" width="100%" type='application/pdf'>
                                    </div>
                                    <div class="col-md-3 col-sm-12 col-12" style="padding-left: 10px; padding-top: 10px">

                                        <div class="col-12">
                                            <div class="col-md-12 col-6" style="padding-left: 5px; padding-right: 5px; margin-bottom: 10px">
                                                <a  type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $value->id_protocolo])}}" data-toggle="modal" data-target="#foto" style="width: 100%; height: 100%; font-size: 12px;" >
                                                    <div class="col-12" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;">
                                                        <img width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                        <label style="color: white; " >{{trans('transquirofano.DescargarenotroFormato')}}</label>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

    </div>
    </div>
	
</div>