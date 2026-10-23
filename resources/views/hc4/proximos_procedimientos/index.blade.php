<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

@php
 $variable_timepicker = 'a'.rand(0,999);
@endphp
<style type="text/css">
    .parent{
        overflow-y:scroll;
        height: 600px;
    }

    .jscroll-inner{
        width: 100%;
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
    } /* the new scrollbar will have a flat appearance with the set background color */
    .parent::-webkit-scrollbar-track-piece{
        width: 2px;
        background-color: none;
    }

    .parent::-webkit-scrollbar-button {
          background-color: none;
    } /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

    .parent::-webkit-scrollbar-corner {
          background-color: none;
    } /* if both the vertical and the horizontal bars appear, then perhaps the right bottom corner also needs to be styled */

    .btn-block{
      background-color: #004AC1;
    }
     .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }

    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }

    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }

     .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .btn_agregar_diag{
        color: white;
        background-color: green;
    }
    .alerta_correcto{
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }
    .desabilitar{
        pointer-events: none;
        cursor:no-drop;
    }
    .fincita{
        background-color: #dc3545;
        /*background-color: #ef3838;*/
        padding: 10px 20px;
        margin-top: 15px;
        margin-left: -70%;
        text-align: center;
        font-weight: ;
        font-family: 'Helvetica general3';
        font-size: 15px;
        border-radius: 10px;
        color: white;
        display: block;
    }
    .inlines{
        display: initial;
        margin-right: 10px;
        padding: 10px 0px;
    }
</style>

<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>
<div class="modal fade" id="vademecum" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document" style="max-width: 80% !important;">
      <div class="modal-content">

      </div>
    </div>
</div>

<input type="hidden" id="id_paciente" name="" value="{{$paciente->id}}">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />

<div class="box " style="border: 2px solid #004AC1; background-color: white; ">
    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
        <div class="row">
            <div class="col-4">
                <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
                    <img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/pendo.png"> <b>PROXIMOS PROCEDIMIENTOS</b>
                </h1>
            </div>
        </div>
        @if(!is_null($paciente))
            <center>
                <div class="col-12" style="padding-top: 15px">
                    <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
                        <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                            {{$paciente->nombre1}} {{$paciente->nombre2}}
                        </b>
                    </h1>
                </div>
            </center>
        @endif
    </div>
    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >
        <div class="parent" style="margin-left: 0px; height: 450px " >
            <div style=" margin-right: 30px;" >
                @foreach($prox_procedimientos as $value)
                    @if($value->id_doctor1 != '4444444444')    
                    <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 20px; padding-left: 0px; padding-right: 0px" >
                        <div class="box-header collapsed-box" >
                            <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 0px; margin-right: 0px">
                                <div class="col-md-11 col-sm-11 col-10" style="text-align: center;">
                                    <span >
                                        @if(!is_null($value->fechaini))
                                            @php
                                            $dia =  Date('N',strtotime($value->fechaini));
                                            $mes =  Date('n',strtotime($value->fechaini)); @endphp
                                            <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";>
                                            @if($dia == '1') Lunes
                                                 @elseif($dia == '2') Martes
                                                 @elseif($dia == '3') Miércoles
                                                 @elseif($dia == '4') Jueves
                                                 @elseif($dia == '5') Viernes
                                                 @elseif($dia == '6') Sábado
                                                 @elseif($dia == '7') Domingo
                                            @endif
                                                {{substr($value->fechaini,8,2)}} de
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
                                                del {{substr($value->fechaini,0,4)}}
                                                </span>
                                                <span style="font-family: 'Helvetica'; font-size: 14px; color: #004AC1;">{{$value->id}}</span>
                                        @endif
                                    </span>
                                </div>
                                <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                  <i class="fa fa-minus"></i>
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-12" >
                                    <div class="row">
                                        <div class="col-3">
                                           <div>
                                                <span style="font-family: 'Helvetica general'; font-size: 14px">Estado Cita:</span>
                                                <span style="font-size: 13px">
                                                    @if(!is_null($value->estado_cita)) 
                                                        @if($value->estado_cita == '0') <span style="color: red;"> Por confirmar </span> @endif
                                                        @if($value->estado_cita == '1') <span style="color: red;"> Confirmada </span> @endif
                                                        @if($value->estado_cita == '2') <span style="color: red;"> Reagendado </span> @endif
                                                        @if($value->estado_cita == '3') <span style="color: red;"> Suspendido </span> @endif
                                                        @if($value->estado_cita == '4') <span style="color: red;"> Admisionado </span> @endif
                                                    @endif
                                                </span>
                                           </div>
                                        </div>
                                        @php
                                            $texto = "";
                                            if(!is_null($value->id_procedimiento)){
                                                $texto = $procedimientos->find($value->id_procedimiento)->nombre;
                                            }
                                            if(!is_null($value->id)){
                                                $proc_agenda = \Sis_medico\AgendaProcedimiento::where('id_agenda', $value->id)->get();
                                                foreach($proc_agenda as $value2)
                                                {
                                                    $texto = $texto.' + '. $value2->procedimiento->nombre  ;
                                                }
                                            }
                                        @endphp
                                        <div class="col-4">
                                            <div>
                                                <span style="font-family: 'Helvetica general'; font-size: 14px">Procedimiento: </span>
                                                <span style="font-size: 13px"> @if($texto != "") {{$texto}} @else CONSULTA @endif </span>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                           <div>
                                                <span style="font-family: 'Helvetica general'; font-size: 14px">Seguro:</span>
                                                <span style="font-size: 13px"> @if(!is_null($value->id_seguro)) {{$value->seguro->nombre}} @endif </span>
                                           </div>
                                        </div>
                                        <div class="col-3">
                                           <div>
                                                <span style="font-family: 'Helvetica general'; font-size: 14px">Dr (a):</span>
                                                <span style="font-size: 13px"> @if(!is_null($value->id_doctor1)) {{$value->doctor1->nombre1}} {{$value->doctor1->apellido1}} @endif </span>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-6">
                                   <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 14px"> @lang('hc4.tipo'):</span>
                                        <span style="font-size: 13px"> @if($texto != "") PROCEDIMIENTO @else CONSULTA @endif </span>
                                   </div>
                                </div>
                                <div class="col-6">
                                   <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 14px"> @lang('hc4.hora'):</span>
                                        <span style="font-size: 13px"> {{substr($value->fechaini,-8)}} - {{substr($value->fechafin,-8)}} </span>
                                   </div>
                                </div>
                                <div class="col-12">
                                   <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 14px"> @lang('hc4.observacion'):</span>
                                        <span style="font-size: 13px"> @if(!is_null($value->observaciones)) {{$value->observaciones}} @endif </span>
                                   </div>
                                </div>
                                <div class="col-6">
                                   <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 14px"> @lang('hc4.creada'):</span>
                                        <span style="font-size: 13px"> {{$value->created_at}} - {{substr($value->ucnombre,0,1)}}.{{$value->ucapellido}}</span>
                                   </div>
                                </div>
                                <div class="col-6">
                                   <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 14px"> @lang('hc4.modificada'):</span>
                                        <span style="font-size: 13px"> {{$value->updated_at}} - {{substr($value->umnombre,0,1)}}.{{$value->umapellido}} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
