@extends('hc_admision.nota_de_entrega.base_nota')
@section('action-content')

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">

    @media screen and (max-width: 1500px) {

        label#peri.control-label {
            font-size: 13px;
        }

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

        .mce-edit-focus,
        .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
        }

        .select2-selection--multiple{
            background-color: #ffe0cc !important;
        }

        /*Nuevos css historial de recetas*/
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
        /* Fin Nuevos css historial de recetas*/

</style>
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12" style="padding-right: 6px;">
            <div  class="box box-primary " style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b>Recetas del Paciente</b></a></h3>
                    </div>
                    @php $paciente = Sis_medico\User::find($agenda->id_paciente); @endphp
                    <div class="col-md-5">
                        <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b>@if(!is_null($paciente)) {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} @endif</b></a></h3>
                    </div>
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="receta">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="container-fluid">
                        <div class="row ">
                            <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
                                <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
                                     <div>
                                        <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
                                            <div class="parent" style="background-color: #ffffff">
                                                <div style=" margin-right: 30px;">
                                                    @if($hist_recetas != null)
                                                        @foreach($hist_recetas as $re_hist)
                                                        @php 
                                                            $receta = Sis_medico\hc_receta::find($re_hist->id);
                                                         @endphp
                                                        <div class="box" style="border: 2px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                                                            <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                                                @if(!is_null($re_hist->fecha_atencion))
                                                                    @php
                                                                      $dia =  Date('N',strtotime($re_hist->fecha_atencion));
                                                                      $mes =  Date('n',strtotime($re_hist->fecha_atencion));
                                                                    @endphp
                                                                    <b>
                                                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title" >
                                                                    @if($dia == '1') @lang('laboratorio.lunes') 
                                                                    @elseif($dia == '2') @lang('laboratorio.martes') 
                                                                    @elseif($dia == '3') @lang('laboratorio.miercoles')
                                                                    @elseif($dia == '4') @lang('laboratorio.jueves') 
                                                                    @elseif($dia == '5') @lang('laboratorio.viernes') 
                                                                    @elseif($dia == '6') @lang('laboratorio.sabado') 
                                                                    @elseif($dia == '7') @lang('laboratorio.domingo') 
                                                                    @endif {{substr($re_hist->fecha_atencion,8,2)}} 
                                                                    @lang('laboratorio.de') @if($mes == '1') @lang('laboratorio.enero') 
                                                                    @elseif($mes == '2') @lang('laboratorio.febrero') 
                                                                    @elseif($mes == '3') @lang('laboratorio.marzo') 
                                                                    @elseif($mes == '4') @lang('laboratorio.abril') 
                                                                    @elseif($mes == '5') @lang('laboratorio.mayo') 
                                                                    @elseif($mes == '6') @lang('laboratorio.junio') 
                                                                    @elseif($mes == '7') @lang('laboratorio.julio') 
                                                                    @elseif($mes == '8') @lang('laboratorio.agosto') 
                                                                    @elseif($mes == '9') @lang('laboratorio.septiembre')
                                                                    @elseif($mes == '10') @lang('laboratorio.octubre') 
                                                                    @elseif($mes == '11') @lang('laboratorio.noviembre') 
                                                                    @elseif($mes == '12') @lang('laboratorio.diciembre') 
                                                                    @endif @lang('laboratorio.del') {{substr($re_hist->fecha_atencion,0,4)}}</span></b>
                                                                @endif
                                                                    <div style="color: white">
                                                                      @if(!is_null($re_hist->id))
                                                                        {{$re_hist->id}}
                                                                      @endif
                                                                    </div>
                                                                    <div class="pull-right box-tools ">
                                                                      <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                                                                        <i class="fa fa-plus"></i>
                                                                      </button>
                                                                    </div>
                                                            </div>
                                                            <!--box-body-->
                                                            <div class="box-body">
                                                                <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                                                                    <div class="row">
                                                                        <div class="col-md-6" style="text-align: right;">
                                                                            <label style="font-family: 'Helvetica general';">@lang('laboratorio.seguro'):</label>
                                                                              <span>
                                                                                @if(!is_null($re_hist->nombre))
                                                                                  {{$re_hist->nombre}}
                                                                                @endif
                                                                              </span>
                                                                        </div>
                                                                        <br><br>
                                                                        <div class="col-md-9 col-sm-11 col-11" style="border: 2px solid #3c8dbc;margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;background-color:#ffffff;">
                                                                           <!--Contenedor Historial de Recetas-->
                                                                            <div  style="background-color: #3c8dbc; color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                                                                              <div class="box-title" style="background-color: #3c8dbc; margin-left: 10px">
                                                                                <div class="row">
                                                                                  <div class="col-md-8 col-sm-8 col-8" style="padding-top: 4px">
                                                                                    <span>&nbsp;</span>
                                                                                  </div>
                                                                                </div>
                                                                              </div>
                                                                            </div>
                                                                            <div class="contenedor2" id="receta{{$re_hist->id}}" style="padding-bottom: 20px; padding-right: 15px">
                                                                              <div class="col-md-12" style="padding-bottom: 15px;">
                                                                                <div class="row">
                                                                                  <div class="col-md-6">
                                                                                    <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                                                                    <div id="xtrp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #3c8dbc; ">
                                                                                    @if(!is_null($receta))
                                                                                        @php $rdetalles = $receta->detalles; @endphp
                                                                                        @foreach($rdetalles as $detalle)
                                                                                            @php $genericos = $detalle->medicina->genericos; @endphp                       
                                                                                            <p>{{$detalle->nombre}} @foreach($genericos as $gen) ( {{$gen->generico->nombre}} ) @endforeach : {{$detalle->cantidad}} {{ $detalle->detalle_cantidad}}</p>                                  
                                                                                        @endforeach  
                                                                                        @if(!is_null($re_hist->rp))
                                                                                            <p><?php echo $re_hist->rp ?>
                                                                                            </p>
                                                                                        @endif
                                                                                    @endif    
                                                                                    </div>
                                                                                  </div>
                                                                                  <div class="col-md-6" >
                                                                                    <span><b style="font-family: 'Helvetica general';" class="box-title">@lang('laboratorio.prescripcion')</b></span>
                                                                                    <div id="xtprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                                                                                    @if(!is_null($receta))
                                                                                        @php $rdetalles = $receta->detalles; @endphp
                                                                                        @foreach($rdetalles as $detalle)
                                                                                            @php $genericos = $detalle->medicina->genericos; @endphp                       
                                                                                            <p>{{$detalle->nombre}}: {{$detalle->dosis}}</p>                                 
                                                                                        @endforeach  
                                                                                        @if(!is_null($re_hist->prescripcion))
                                                                                            <p><?php echo $re_hist->prescripcion ?></p>
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
                                                                                      <label style="font-size: 14px;color: white">@lang('laboratorio.imprimirMembretada')</label>
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
                                                                                      <label style="font-size: 14px;color: white">@lang('laboratorio.imprimir')</label>
                                                                                    </div>
                                                                                  </div>
                                                                                </div>
                                                                                </a>
                                                                              </div>
                                                                              <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">
                                                                                <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('receta.nota_entrega', ['id' => $re_hist->id]) }}">
                                                                                <div class="col-md-12" style="text-align: center;">
                                                                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                                                      <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/receta.png">
                                                                                    </div>
                                                                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px;">
                                                                                      <label style="font-size: 14px;color: white">Nota de Entrega</label>
                                                                                    </div>
                                                                                  </div>
                                                                                </div>
                                                                                </a>
                                                                              </div>
                                                                             <!--  <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">
                                                                                <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('nota_entrega.editar', ['id' => $re_hist->id]) }}">
                                                                                <div class="col-md-12" style="text-align: center;">
                                                                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                                                      <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/receta.png">
                                                                                    </div>
                                                                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px;">
                                                                                      <label style="font-size: 14px;color: white">Nota de Entrega</label>
                                                                                    </div>
                                                                                  </div>
                                                                                </div>
                                                                                </a>
                                                                              </div> -->
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
                    </div>
                </div>
            </div>
           
    </div>
</div>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
</section>

@include('sweet::alert')
@endsection
