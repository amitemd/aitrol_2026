@extends('contable.facturacion.base')
@section('action-content')
<style type="text/css">
    td {
        padding-top: 2px !important;
        padding-bottom: 2px !important;
    }

    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .alerta_guardado {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .disableds {
        display: none;
    }

    .disableds2 {
        display: none;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .card-header {
        border-radius: 6px 6px 0 0;
        background-color: #3c8dbc;
        border-color: #b2b2b2;
        padding: 8px;
        font-family: 'Roboto', sans-serif;
    }

    .col-md-6 {
        margin-top: 7px;
    }
</style>
<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
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

    .disableds {
        display: none;
    }

    .dogde {
        width: 100%;
        height: 20px;
    }

    .disableds2 {
        display: none;
    }

    .wells {
        background-color: #E3F2FD;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .cabecera {
        background-color: #3c8dbc;
        border-radius: 8px;
        color: white;
    }

    .borde {
        border: 2px solid #3c8dbc;
    }

    .s {
        font-size: 17px !important;
    }

    .visible {
        display: none;
    }
</style>

<div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        </div>
    </div>
</div>

<div class="modal fade" id="log_rc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-content">
                <div style="text-align: left" class="modal-header">
                    <h3 class="box-title">LOG</h3>
                </div>  
                <div class="modal-body">
                    @php $logs = $orden->logs; @endphp
                    @foreach($logs as $log)
                        <p><b>{{$log->usercrea->apellido1}} {{$log->usercrea->nombre1}}: </b>{{ $log->descripcion }}</p>
                    @endforeach
                </div>   
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
                </div> 
            </div>    
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="datos_factura">
      </div>
    </div>
</div>

<!-- 
http://192.168.75.125/sis_medico_prb/public/contable/facturacion/agenda/21526 -->
<section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{trans('new_recibo.GuardadoCorrectamente')}}
    </div>
    <?php
    $ordenVenta=Sis_medico\Ct_ventas::where('orden_venta',$orden->id)->where('estado',1)->first(['id']);
    $conglo=Sis_medico\Ct_Ven_Orden::where('orden_venta',$orden->id)->first(['id']);
    ?>
    <div class="box box s">
        <div class="header box-header with-border">
            <div class="box-title col-md-9">
                <b style="font-size: 16px;">{{trans('new_recibo.ReciboCobro')}} #{{$orden->id}}</b>
            </div>
            @if($orden->estado == 1)<div class="col-md-3 alert-success" style="text-align: right;"> Orden Ya Emitida !!! </div>@endif
            @if($ordenVenta != '')<div style="padding-top: 20px;margin-top: 10px;"></div><div class="col-md-offset-9 col-md-3 alert-info" style="text-align: right;"> Orden Ya Facturada !!! </div>@endif
        </div>

        <div class="box-body">
            <a href="#" target="_blank" id="ride"></a>
            <form class="form-vertical" id="form_actualizar_cabecera" method="POST" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">
                <input type="hidden" name="empresa" id="id_empresa" value="{{$empresa->id}}">
                <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                <input type="hidden" name="id_doctor" id="id_doctor" value="{{$agenda->id_doctor1}}">
                <div class="col-md-12">
                    <div class="row dobra">
                        @php 
                            if($agenda->id_doctor1 == null){
                                $stringarm="preagenda/".$agenda->id."/edit";
                            }else{
                                $stringarm="agenda/".$agenda->id."/edit/".$agenda->id_doctor1;
                            }
                            $npro   = Sis_medico\Proforma_Cabecera::where('id_orden', $orden->id)->first();
                            if(!is_null($npro) && $npro->cobro_hc == 1){
                                $stringarm="comercial/proforma/index_proforma";
                            }
                                $subir_archivo = 'compr_cobros_hc/'. $agenda->id_paciente;
                        @endphp
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <label class="col-md-5">{{trans('new_recibo.DatosPrincipales')}}</label>
                                        <div class="col-md-5" style="text-align: center;">
                                            @if($orden->estado == -1)
                                            <button class="btn btn-success" type="button" onclick="emitir()">Emitir</button>
                                            @endif

                                            @if ($orden->estado === 1)
                                            <a href="{{ route('facturacion.imprimir_ride', ['id' => $orden->id]) }}" class="btn btn-primary btn-gray" id="printer" type="button" target="_blank"> <i class="fa fa-print"></i> </a>
                                                @if($ordenVenta=='')
                                                    <button class="btn btn-warning" type="button" onclick="devolver()">{{ trans('contableM.devolver') }}</button>
                                                @endif
                                            @endif
                                                <?php
                                                if($ordenVenta=='')
                                                {                                                
                                                    if($orden->total==0)
                                                    {
                                                        if($conglo=='')
                                                        {
                                                        ?>
                                                            <button class="btn btn-danger" type="button" id="btn_facturar" title="Facturar" onclick="facturar('{{$orden->id}}')"><i class="fa fa-file"></i> Enviar a conglomerada</button>
                                                        <?php        
                                                        }
                                                    }
                                                    else{
                                                    ?>  
                                                       <button class="btn btn-success" type="button" id="btn_facturar" title="Facturar" onclick="facturar('{{$orden->id}}')"><i class="fa fa-file"></i> Facturar</button>
                                                        <?php 
                                                    }
                                                }
                                                ?>
                                            <a class="btn btn-primary" data-toggle="modal" data-target="#log_rc">Logs</a>
                                        
                                                <a href="{{ url($subir_archivo) }}" target="_blank" type="button" class="btn btn-primary">
                                                    <i class="fa fa-upload"> Comprobante</i>
                                                </a>
                                        </div>
                                       
                                        <div class="col-md-2" style="text-align: center;">
                                           
                                            <button onclick="return location.href='{{url($stringarm)}}'" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>

                                        </div>      
                                        <!--div class="col-md-2" style="text-align: right;">
                                            <button onclick="return window.location.href = window.location.href" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </div-->
                                    </div>

                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:210px;height:70px">
                                        </div>
                                        <div class="col-md-9">
                                            <label>{{$empresa->nombrecomercial}}</label><br>
                                            <label>{{$empresa->direccion}}</label>
                                        </div>
                                        <div class="col-md-12">&nbsp;</div>
                                        <div class="row" style="padding: 0px 20px 0px 20px;">
                                            <div class="col-md-2">
                                                <label for="numero">{{trans('new_recibo.FechaAgenda')}}</label>
                                                <div class="form-group input-group-sm">

                                                    <input type="date" name="f_procedimiento" value="{{date('Y-m-d',strtotime($agenda->fechaini))}}" readonly class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" >
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="numero">{{trans('new_recibo.FechaDeEmision')}}</label>
                                                <div class="form-group input-group-sm">

                                                    <input type="datetime-local" name="f_emision" value="{{date('Y-m-d',strtotime($orden->nueva_fecha))}}T{{date('H:i',strtotime($orden->nueva_fecha))}}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" onchange="guardar_auto()" min="{{date('Y-m-d')}}T00:00">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="observacion">{{trans('new_recibo.Concepto')}}</label>
                                                <div class="form-group">
                                                    <textarea class="form-control" name="observacion" id="observacion" cols="3" rows="1" onchange="guardar_auto()">{{ $orden->observacion}}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <label for="observacion_admin">Observación Administrativa</label>
                                                <div class="form-group">
                                                    <textarea class="form-control" name="observacion_admin" id="observacion_admin" cols="3" rows="1" onchange="guardar_auto()">{{ $orden->observacion_admin}}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label for="buscador" class="control-label">Asesor de venta ( *valido solo para la compra de membresias)</label>
                                                <input id="buscadorasesor" class="form-control input-sm" onblur="guardar_cabecera()" type="text" name="buscadorasesor" value="@if($orden->asesor_venta != null) {{$orden->user_referido->apellido1}} {{$orden->user_referido->nombre1}} @endif">
                                                <input type="hidden" id="idasesor" name="idasesor" value="@if($orden->asesor_venta != null){{$orden->asesor_venta}}@endif">
                                            </div>
                                        </div>
                                        <div class="row" style="padding: 0px 20px 0px 20px;">
                                            @if($orden->valor_oda > 0)
                                                <div class="col-md-2">
                                                    <label >Fecha Emision ODA</label>
                                                    <div id="fechaEmisionOda" style="display:block;" class="form-group input-group-sm">
                                                        <input type="date" name="f_emision_oda"
                                                            value="{{ isset($orden->fecha_emision_oda) ? date('Y-m-d', strtotime($orden->fecha_emision_oda)) : date('Y-m-d') }}"
                                                            class="form-control" aria-label="Small"
                                                            aria-describedby="inputGroup-sizing-sm" onchange="guardar_auto()">
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-2">
                                                <label for="codigo_prestador">Codigo Prestador</label>
                                                <div class="form-group">
                                                    <input value="{{$orden->codigo_prestador}}" onchange="guardar_auto()" type="text" class="form-control" name="codigo_prestador" id="codigo_prestador">
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $obs_devolucion = Sis_medico\LogReciboDeCobro::where('id_ct_orden_venta',$orden->id)->where('descripcion', 'like', '%DEVUELTA%')->orderby('updated_at', 'desc')->first();
                                        @endphp
                                        @if (!is_null($obs_devolucion))
                                            <div class="form-group col-md-7">
                                                <label for="devolucion_modal" class="control-label">Observación de la Devolución</label>
                                                <input type="text" class="form-control" value="{{$obs_devolucion->observacion }}">
                                            </div>
                                        @endif
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label>{{trans('new_recibo.Datos')}}</label>
                                </div>
                                <div class="panel-body">
                                    <div class="row"><!--MEMBRESIAS -->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="nombres" class="col-md-3">{{trans('new_recibo.Paciente')}}</label>
                                            <div class="input-group col-md-9"><!--PACIENTE EN ROJO Y GRANDE -->
                                                <label style="font-style: 15px;color: red;">{{$agenda->id_paciente}}: {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
                                                    {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif </label>
                                            </div>
                                        </div>
                                        @if($res['estado']=='ok')
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="nombres" class="col-md-3">MEMBRESIA</label>
                                            <div class="input-group col-md-9"><!--PACIENTE EN ROJO Y GRANDE -->
                                                <label style="font-style: 20px;color: red;"><div class="alert-success" style="text-align: right;">{{ $res['nombre']}}</div></label>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-3 col-xs-12">
                                        <label for="id_tipo" class="col-md-12 control-label">{{trans('new_recibo.Tiposeguro')}}</label>
                                        <div class="input-group col-md-12">
                                            <select class="form-control input-sm" name="id_tipo" id="id_tipo" required onchange="cargar_seguro();guardar_auto();">
                                                <!--option value="">Seleccione ...</option-->
                                                @foreach($tipos_seguros as $ts)
                                                <option @if($orden->seguro != null ) @if($orden->seguro->id_seguro_tipos != null) @if($orden->seguro->id_seguro_tipos == $ts->id) selected @endif @endif @endif value="{{$ts->id}}">{{$ts->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-xs-12">
                                        <label for="id_seguro" class="col-md-12 control-label">{{trans('new_recibo.Seguro')}}</label>
                                        <div class="input-group col-md-12">
                                            <select class="form-control input-sm js-seguro" name="id_seguro" id="id_seguro" required onchange="cargar_nivel();guardar_auto();">
                                                <!--option value="">Seleccione ...</option-->
                                                @foreach($seguros as $seguro)
                                                <!--option @if($orden->id_seguro == $seguro->id) selected @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option-->
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div id="ident_nivel" class="col-md-3 col-xs-5">
                                        <label for="id_nivel" class="col-md-12 control-label">{{trans('new_recibo.Nivel')}}</label>
                                        <div class="input-group col-md-12">
                                            <select name="id_nivel" id="id_nivel" class="form-control input-sm" onchange="guardar_auto()">
                                                <option value="">Seleccione...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 col-xs-12">
                                        <div class="col-md-12">
                                            <label>{{trans('new_recibo.PoseeOda')}}</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="oda" class="form-control input-sm" placeholder="# ODA" id="oda" value="{{ $orden->numero_oda }}" onchange="guardar_auto()">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-xs-12">
                                        <label for="pago" class="col-md-12 control-label">{{trans('new_recibo.CajaCobro')}}</label>
                                        <div class="input-group col-md-12">
                                            <select id="caja" name="caja" class="form-control" required onchange="guardar_auto()">
                                                <option value="">Seleccione...</option>
                                                @foreach($cajas as $caja)
                                                <option @if($orden->caja == $caja->id) selected @endif value="{{$caja->id}}">{{$caja->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
				    <div class="form-group col-md-3 col-xs-12">
                                            <label for="doctor_referido"
                                                class="col-md-12 control-label">Doctor Referido</label>
                                            <div class="input-group col-md-12">
                                                <select class="form-control input-sm js-seguro" name="doctor_referido" id="doctor_referido"
                                                    required>
                                                    <option value="">Seleccione ...</option>
                                                    @foreach ($doctor_referido as $seguro)
                                                       <option @if ($orden->doctor_referido == $seguro->id) selected @endif value="{{ $seguro->id }}">{{ $seguro->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    <div class="form-group col-md-1 col-xs-12" style="text-align: center; margin-top: 10px;">
                                        <br>
                                        <button class="btn btn-success btn-success saves" type="button" onclick="guardar_cabecera()" >
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                    <div class="form-group col-md-12" style="background-color: #d4850e; color: white;">
                                        <label> {{trans('new_recibo.DatosDeCliente')}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-success btn-success saves" type="button" onclick="actualizar_cliente()" style="margin: 5px">
                                            <i class="fa fa-save"> Crear/Actualizar Cliente</i>
                                        </button>
                                    </div>
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="cedula" class="col-md-5 control-label">{{trans('new_recibo.Tipo')}}</label>
                                        <div class="input-group col-md-7">
                                            <select id="tipo_identificacion" name="tipo_identificacion" class="form-control input-sm" required autofocus="">
                                                <option @if($orden->tipo_identificacion=='4') selected='selected' @endif value="4">RUC</option>
                                                <option @if($orden->tipo_identificacion=='5') selected='selected' @endif value="5">CEDULA</option>
                                                <option @if($orden->tipo_identificacion=='6') selected='selected' @endif value="6">PASAPORTE</option>
                                                <option @if($orden->tipo_identificacion=='8') selected='selected' @endif value="8">CEDULA EXTRANJERA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--Ruc/Cedula-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="cedula" class="col-md-5 control-label">{{trans('new_recibo.RucCedula')}}</label>
                                        <div class="input-group col-md-7">
                                            <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula" value="{{$orden->identificacion}}" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Razon Social-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="razon_social" class="col-md-5 control-label">{{trans('new_recibo.RazonSocial')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="razon_social" id="razon_social" value="{{$orden->razon_social}}" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Ciudad-->

                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="ciudad" class="col-md-5 control-label">{{trans('new_recibo.Ciudad')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="{{$orden->ciudad}}" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Direccion-->
                                    <div class="form-group col-md-66 col-xs-6">
                                        <label for="direccion" class="col-md-5 control-label">{{trans('new_recibo.Direccion')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="direccion" id="direccion" value="{{$orden->direccion}}" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Telefono-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="telefono" class="col-md-5 control-label">{{trans('new_recibo.Telefono')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="{{$orden->telefono}}" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Email-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="email" class="col-md-5 control-label">{{trans('new_recibo.Mail')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="email" id="email" value="{{$orden->email}}" placeholder="Mail">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                   <!--Sucursal-->
                                   <div class="form-group col-md-6 col-xs-6">
                                        <label for="direccion" class="col-md-5 control-label">Sucursales</label>
                                        <div class="input-group col-md-7">
                                            <select id="sucursal" name="sucursal" class="form-control input-sm" required autofocus="">
                                                <option value="">--Seleccionar--</option>
                                                <?php
                                                if (isset($puntoEmision) && count($puntoEmision) > 0) {
                                                    foreach($puntoEmision as $punto)
                                                    {
                                                        $select='';
                                                        if($punto->codigo_sucursal=='001' and $punto->codigo_caja==100)
                                                        {
                                                            $select='selected';
                                                        }
                                                        echo '<option value="'.$punto->id_sucursal.'-'.$punto->id_caja.'" '.$select.'>'.$punto->nombre_sucursal.' | '.$punto->codigo_sucursal.'-'.$punto->codigo_caja.'</option>';
                                                    }
                                                }
                                                ?>
                                            </select> 
                                        </div>
                                    </div>
                                    <!--div class="form-group col-md-2 ">
                                        <div class="col-md-7">
                                            <a class="btn btn-primary" data-remote="{{route('comercial.proforma.proformaModal',['id_paciente' => $agenda->id_paciente])}}" data-toggle="modal" data-target="#tarifario_nivel">Crear</a>
                                        </div>
                                    </div-->
                                    <div class="form-group col-md-6 col-xs-12" style="text-align: center; margin-top: 10px;">
                                        <button class="btn btn-success btn-success saves" type="button" onclick="guardar_cabecera()">
                                            <i class="fa fa-save">Actualizar</i>
                                        </button>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                    <div class="col-md-12" style="padding: 0;">
                        <div id="detalles_orden">

                        </div>
                    </div>
                    <div class="col-md-2" style="text-align: center;">
                        @if($orden->estado == -1)
                        <button class="btn btn-success" type="button" onclick="emitir()">Emitir</button>
                        @endif
                    </div>

                    <div class="col-md-12" style="padding: 0;">
                        <div id="detalles_forma_pago">

                        </div>
                    </div>

                </div>

        </div>
        </form>
    </div>
    </div>
</section>
    <div class="modal" tabindex="-1" role="dialog" id="myModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="display:flex;align-items:center;">
                    <h5 class="modal-title"> <span style="font-weight:bold">OBSERVACIÓN</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <img style="width:10%"
                            src="https://upload.wikimedia.org/wikipedia/commons/8/88/Red_triangle_alert_icon.png"
                            alt="imagen_alerta" />
                    </div>
                    <div class="form-group">
                        <label for="observacion_modal">INGRESE LA OBSERVACIÓN</label>
                        <input type="text" class="form-control" id="observacion_modal"
                            placeholder="Ingrese la observación" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="confirmado_devolder();">SI, DEVOLVER!</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">CANCELAR</button>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>

<script type="text/javascript">

    document.getElementById("tipo_identificacion").addEventListener("change", function () {
        const selectValue = this.value; // Obtiene el valor del select
        const input = document.getElementById("cedula"); // Obtiene el input

        // Cambia el maxlength según el valor del select
        if (selectValue === "5" || selectValue === "8") {
            input.maxLength = 10;
        } else if (selectValue === "4") {
            input.maxLength = 13;
        } else if (selectValue === "6") {
            input.maxLength = 25;
        }
    });

        $(".js-seguro").select2();
    //////////////// NUEVAS FUNCIONES DE INGRESO DE PRODUCTOS
    lista_productos();
    lista_formas_pago();
    cargar_nivel();
    cargar_seguro();
function facturar(id)
{
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Se va a generar la factura correspondiente a esta orden",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Continuar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:'{{url("/")}}/ventas/generarFacturaRecibo',
                type:'post',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data:$('#form_actualizar_cabecera').serialize(),
                datatype:'json',
                success:function(data)
                {
                    Swal.fire({
                        title: "Aitrol le informa",
                        icon: data.status,
                        text: data.message
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if(data.status=='success')
                            {
                                $('#btn_facturar').hide();
                                window.open("{{route('reporte.index_cierre')}}",'_self');
                            }
                        }
                    });
                },
                error:function(data)
                {
                    Swal.fire({
                        title: "Aitrol le informa",
                        icon: "error",
                        text: 'Ocurrio un mensaje al comunicarse con el servidor...'
                    });
                }
            });
        } 
    });
}
    function actualizar_cliente() {
        //alert(id_cliente);
        $.ajax({
            type: 'get',
            url:"{{ route('nuevorecibocobro.modal_actualiza_cliente', ['id_cliente' => $orden->identificacion, 'id_orden' => $orden->id])}}",
            datatype: 'json',
            success: function(data) {
              $('#datos_factura').empty().html(data);
              $('#modal_datosfacturas').modal();
            },
            error: function(data) {
              //console.log(data);
            }
        });

    }

    function lista_formas_pago() {
        $.ajax({
            url: "{{route('nuevorecibocobro.formas_pago',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                //console.log(data);
                $('#detalles_forma_pago').empty().html(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function lista_productos() {
        $.ajax({
            url: "{{route('nuevorecibocobro.detalles',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                //console.log(data);
                $('#detalles_orden').empty().html(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function seleccionar_producto() {
        var producto_nuevo = $('#producto_nuevo').val();

        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.guardar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'producto_nuevo': producto_nuevo,
                'id': '{{ $orden->id }}'
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });

    }

    function seleccionar_metodo() {
        var fp_metodo_nuevo = $('#fp_metodo_nuevo').val();
        var fp_fecha_nueva = $('#fp_fecha_nueva').val();
        var fp_tarjetanueva = $('#fp_tarjetanueva').val();
        var fp_numero_nuevo = $('#fp_numero_nuevo').val();
        var fp_banco = $('#fp_banco').val();
        var fp_cuenta_nueva = $('#fp_cuenta_nueva').val();
        var fp_girado_nuevo = $('#fp_girado_nuevo').val();
        var fp_valor_nuevo = $('#fp_valor_nuevo').val();

        var msj = "";

        if(fp_metodo_nuevo == 2 ){
        
            
            if (fp_numero_nuevo == "") {
                msj += "Por favor,Ingrese Numero<br/>";
            }
            if (fp_banco == "") {
                msj += "Por favor,Ingrese Banco<br/>";
            }
            
            
        }

        if(fp_metodo_nuevo > 2 && fp_metodo_nuevo < 7){
        
            if(fp_metodo_nuevo == 3 || fp_metodo_nuevo == 5){
                if (fp_banco == "") {
                    msj += "Por favor,Ingrese Banco<br/>";
                }
            }else{
                if (fp_tarjetanueva == "") {
                    msj += "Por favor,Ingrese la Tarjeta<br/>";
                }
                if (fp_numero_nuevo == "") {
                    msj += "Por favor,Ingrese Numero<br/>";
                }
                if (fp_banco == "") {
                    msj += "Por favor,Ingrese Banco<br/>";
                }
                if (fp_cuenta_nueva == "") {
                    msj += "Por favor,Ingrese la Cuenta<br/>";
                }
            }    
            
        }

        if (fp_fecha_nueva == "") {
            msj += "Por favor,Ingrese la Fecha<br/>";
        }
        if (fp_valor_nuevo == "") {
            msj += "Por favor,Ingrese el Valor<br/>";
        }
        if (fp_metodo_nuevo == "") {
            msj += "Por favor,Seleccione el método<br/>";
        }


        if(msj == ''){

            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.guardar_formapago')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': '{{ $orden->id }}',
                    'fp_metodo_nuevo': fp_metodo_nuevo,
                    'fp_fecha_nueva': fp_fecha_nueva,
                    'fp_tarjetanueva': fp_tarjetanueva,
                    'fp_numero_nuevo': fp_numero_nuevo,
                    'fp_banco': fp_banco,
                    'fp_cuenta_nueva': fp_cuenta_nueva,
                    'fp_girado_nuevo': fp_girado_nuevo,
                    'fp_valor_nuevo': fp_valor_nuevo
                },
                success: function(data) {
                    lista_formas_pago();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    alert("No se pudo agregar el producto");
                }
            });

        }else{

            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
            
        }

            

    }

    function actualizar_valor(id) {


        var cantidad    = $('#cantidad' + id).val();
        var precio      = $('#precio' + id).val();
        var p_cpac      = $('#p_cpac' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();
        var p_dcto          = $('#p_dcto' + id).val();
        var descuento       = $('#descuento' + id).val();
        var deducible       = $('#valor_deducible' + id).val();
        //var iva = $('#iva'+id).val();
        var id_producto     = $('#id_producto' + id).val();

        var msj = "";
        
        if (cantidad == "") {
            msj += "Por favor,Ingrese la cantidad<br/>";
        }

        if (precio == "") {
            msj += "Por favor,Ingrese el precio<br/>";
        }

        if (p_cpac == "") {
            msj += "Por favor,Ingrese el porcentaje cobrar paciente<br/>";
        }

        if (cobrar_paciente == "") {
            msj += "Por favor,Ingrese el valor cobrar paciente<br/>";
        }

        if (p_dcto == "") {
            msj += "Por favor,Ingrese el porcentaje descuento<br/>";
        }

        if (descuento == "") {
            msj += "Por favor,Ingrese el valor descuento<br/>";
        }

        if (deducible == "") {
            msj += "Por favor,Ingrese el valor deducible<br/>";
        }

        if( msj == '' ){

            cantidad = parseFloat(cantidad);
            precio = parseFloat(precio);
            p_cpac = parseFloat(p_cpac);
            cobrar_paciente = parseFloat(cobrar_paciente);
            p_dcto = parseFloat(p_dcto);
            descuento = parseFloat(descuento);
            deducible = parseFloat(deducible);
            //iva = parseFloat(iva);
            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.actualizar_producto')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': id,
                    'id_producto': id_producto,
                    'cantidad': cantidad,
                    'precio': precio,
                    'p_cpac': p_cpac,
                    'cobrar_paciente': cobrar_paciente,
                    'p_dcto': p_dcto,
                    'descuento': descuento,
                    'deducible': deducible
                },
                success: function(data) {
                    lista_productos();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    alert("No se pudo agregar el producto");
                }
            });

        }else{

            swal({
                title: "Error!",
                type: "error",
                html: msj
            });

        }    

            
    }

    function actualizar_p_cobro(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();
        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        cobrar_paciente = parseFloat(cobrar_paciente);
        var neto = cantidad * precio;
        var pct_cobra = cobrar_paciente / neto;
        pct_cobra = pct_cobra * 100;
        pct_cobra = Math.round(pct_cobra * 100) / 100;

        $('#p_cpac' + id).val(pct_cobra);
        actualizar_valor(id);
    }

    function actualizar_p_dcto(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var descuento = $('#descuento' + id).val();
        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        descuento = parseFloat(descuento);
        var neto = cantidad * precio;
        var pct_dcto = descuento / neto;
        pct_dcto = pct_dcto * 100;
        pct_dcto = Math.round(pct_dcto * 100) / 100;

        $('#p_dcto' + id).val(pct_dcto);
        actualizar_valor(id);
    }

    function actualizar_descripcion(id) {
        var descripcion = $('#descripcion' + id).val();
        var id_producto = $('#id_producto' + id).val();
        id_producto = parseFloat(id_producto);
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.actualizar_descripcion')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id,
                'id_producto': id_producto,
                'descripcion': descripcion,
                'id_producto': id_producto
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }

    function eliminar_detalle(id) {
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.eliminar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }

    function eliminar_forma_pago(id) {
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.eliminar_pago')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                lista_formas_pago();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }


    function guardar_cabecera() {

        var msn = validar_formulario();

        if( msn == '' ){

            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.actualizar_cabecera')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_actualizar_cabecera").serialize(),
                success: function(data) {
                    //console.log(data);
                    swal({
                        title: "Guardado",
                        type: "success",
                        html: 'Guardado Con Exito'
                    });
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }else{
            swal({
                title: "Error!",
                type: "error",
                html: msn
            });
        }

            

    }

    function guardar_auto() {


        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.actualizar_cabecera')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_actualizar_cabecera").serialize(),
            success: function(data) {
                
            },
            error: function(data) {
                console.log(data);
            }
        });


            

    }

    function cargar_nivel() {
        var id_emp = $('#id_empresa').val();
        var xseguro = $('#id_seguro').val();
        $.ajax({
            type: 'post',
            url: "{{route('lista_nivel.seguro')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_seguro': xseguro,
                'id_empresa': id_emp
            },
            success: function(data) {
                //console.log("vt:");
                //console.log(data);
                if (data.value != 'no') {
                    if (xseguro != 0) {
                        //Muestra el Nivel del Seguro  
                        $("#id_nivel").empty();
                        $.each(data, function(key, registro) {
                            $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                        });
                        @if($orden->id_nivel != null )
                        $("#id_nivel option[value='{{$orden->id_nivel}}']").prop('selected', true);
                        @endif
                    } else {
                        $("#id_nivel").empty();
                    }
                } else {
                    //Oculta el Nivel del Seguro
                    document.getElementById("ident_nivel").style.visibility = "hidden";
                }
            },
            error: function(data) {}
        });
    }

    function cargar_seguro() {

        var id_tipo   = $('#id_tipo').val();

        $.ajax({
            type: 'post',
            url: "{{route('facturaagenda.tipifica_seguros')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_tipo'   : id_tipo,
            },
            success: function(data) {
                
                if (data.value != 'no') {
                    var contador = 0;
                    @if($orden->seguro != null ) var contador = 1; @endif
                    $("#id_seguro").empty();
                    $.each(data, function(key, registro) {
                        if(contador == 0){
                            $("#id_seguro").append('<option selected value=' + registro.id + '>' + registro.nombre + '</option>');
                            $('#id_seguro').val(registro.id); 
                            
                            contador ++;
                        }else{
                            $("#id_seguro").append("<option value=" + registro.id + ">" + registro.nombre + "</option>");
                        }
                        
                    });
                    @if($orden->seguro != null )
                    $("#id_seguro option[value='{{$orden->id_seguro}}']").prop('selected', true);
                    @endif
                    cargar_nivel();
                } else {
                    $("#id_seguro").empty();
                }
                
            },
            error: function(data) {}
        });
    }

    function buscar() {
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if (cedula_1.length < 10) {
            alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if (cedula == false && pasaporte != 6 && pasaporte != 8) {
            console.log("no");
        } else {
            console.log("si");
            $.ajax({
                type: 'post',
                url: "{{route('facturacion.cliente')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#cedula"),
                success: function(data) {
                    if (data.value != 'no') {
                        $('#razon_social').val(data.nombre);
                        $('#ciudad').val(data.ciudad);
                        $('#direccion').val(data.direccion);
                        $('#telefono').val(data.telefono);
                        $('#email').val(data.email);
                        //$('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
    }

    function soloNumeros(e) {
        // capturamos la tecla pulsada
        var teclaPulsada = window.event ? window.event.keyCode : e.which;
        // capturamos el contenido del input
        var valor = e.value;
        if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
            document.getElementById("inputNumero").value = "-" + valor;
        }
        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
            return true;
        }
        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }

    function validar_formulario() {
        
        var formulario = document.forms["form_actualizar_cabecera"];
        //Datos Generales
        
        var ced_cliente = formulario.cedula.value;
        var raz_social = formulario.razon_social.value;
        var ciud_cliente = formulario.ciudad.value;
        var dire_cliente = formulario.direccion.value;
        var telf_cliente = formulario.telefono.value;
        var email_cliente = formulario.email.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";
        
        if (ced_cliente == "") {
            msj += "Por favor,Ingrese la cedula del cliente<br/>";
        }
        if (raz_social == "") {
            msj += "Por favor,Ingrese la razon social<br/>";
        }
        if (ciud_cliente == "") {
            msj += "Por favor,Ingrese la ciudad del cliente<br/>";
        }
        if (dire_cliente == "") {
            msj += "Por favor,Ingrese la direccion cliente<br/>";
        }
        if (telf_cliente == "") {
            msj += "Por favor,Ingrese el telefono del cliente<br/>";
        }
        if (email_cliente == "") {
            msj += "Por favor,Ingrese el email del cliente<br/>";
        }
        
        if (caja_pago == "") {
            msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        return msj;
        
    }

    function emitir(){

        var msn = validar_formulario();

        if(msn == ''){

            Swal.fire({
                title: 'Al emitir se enviará al Cierre de Caja',
                text: "Esta seguro que desa Emitir?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Emitir!'
            }).then((result) => {
                if (result.isConfirmed){
                    validar_cuadre();
                    
                }else{
                    
                }
            }) 

        }else{

            swal({
                title: "Error!",
                type: "error",
                html: msn
            });

        }

    }

   /* function devolver(){

        Swal.fire({
            title: 'Al devolver ya no estará en el Cierre de Caja',
            text: "Esta seguro que desa devolver?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Devolver!'
        }).then((result) => {
            if (result.isConfirmed){
                $.ajax({
                    url: "{{route('nuevorecibocobro.devolver',['id' => $orden->id])}}",
                    type: 'get',
                    datatype: 'html',
                    success: function(data) {
                        if(data.estado != 'ok'){
                            swal({
                                title: "Devuelto!",
                                type: "success",
                                html: 'Devuelto a Recepción'
                            });
                            location.reload();
                        }else{
                            

                        }    
                    
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })    
                
            }else{
                
            }
        }) 

    }*/

    function confirmado_devolder() {
        let observacion = document.getElementById("observacion_modal").value;
        if (observacion === '') {
            swal({
                title: "El motivo no puede ser vacio",
                type: "error",
            });
            return;
        }
        $.ajax({
            url: "{{ route('nuevorecibocobro.devolver', ['id' => $orden->id]) }}",
            type: 'get',
            data: {
                'observacion': observacion,
                'id': {{ $orden->id }}
            },
            datatype: 'html',
            success: function(data) {
                if (data.estado != 'ok') {
                    swal({
                        title: "trans('contableM.devuel')",
                        type: "success",
                        html: `{{ trans('contableM.devuelarecep') }}`
                    });
                    location.reload();
                } 
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function devolver() {
    $('#myModal').modal("show");
    }

    function validar_cuadre(){

        $.ajax({
            url: "{{route('nuevorecibocobro.validar_valores',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                if(data.estado != 'ok'){
                    swal({
                        title: "Error!",
                        type: "error",
                        html: data.mensaje
                    });
                }else{
                    guardar_cabecera();
                    emitir_recibo_cobro();

                }    
            
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function emitir_recibo_cobro(){

        $.ajax({
            url: "{{route('nuevorecibocobro.emitir_recibo',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                if(data == 'ok'){
                    swal({
                        title: "Emision Correcta!",
                        type: "success",
                        html: 'Su Recibo de Cobro ya se encuentra en el cierre de caja'
                    });
                    window.open("{{route('facturacion.imprimir_ride',['id' => $orden->id])}}", '_blank ');
                    
                    location.reload();
                }else{

                    swal({
                        title: "Error!",
                        type: "error",
                        html: data
                    }); 

                } 
               
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function agregar_deducible( id ){

        $.ajax({
            url: "{{url('nuevo_rc/deducible/crear/item/xseguro')}}/" + id,
            type: 'get',
            datatype: 'html',
            success: function(data) {
                lista_productos();
                
            },
            error: function(data) {
                console.log(data);
            }
        })            

    }

    function aplicarResultado() {
        $("#id_valores").modal("hide");
        let det = $("#id_detalle").val();
        $.ajax({
            url: '{{route("nuevo.crear_deducible")}}',
            data: {
                id: det,
                fee: $("#fee").val(),
                deducible: $("#deducible").val()
            },
            type: 'get',
            datatype: 'json',
            success: function(data) {
                if (data.msj == "ok") {
                    $("#valor_deducible"+det).val(data.total);
                    actualizar_valor(det);
                    lista_productos()
                  
                    
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }   






    //////////////


</script>
@endsection