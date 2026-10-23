@extends('laboratorio.orden.base')

@section('action-content')

<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

  <style type="text/css">
  .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td{
        padding: 3px !important;

    }
    div.formgroup.col-md-4{
        margin-bottom: 0px !important; 
    }
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
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
        _width: 470px !important;
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
    .swal2-modal{
        width: 30%;
    }
</style>

    <div class="modal fade" id="informacion_factura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="forma_pago_gas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>    
    <div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="datos_factura">
                
            </div>
        </div>
    </div>

    <div class="modal fade" id="forma_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
      </div>
    </div>

    <!-- Ventana Modal Pago -->
    <div class="modal fade" id="mail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content" style="width: 95%;" id="ax_mail">
          </div>
        </div>  
    </div>

    <form class="form-vertical" role="form" id="form_aglabs">
        {{ csrf_field() }}
        <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">
        <section class="content" >
            <div class="modal fade bs-example-modal-lg" id="modal_privados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" id="agenda_privados">
                        
                    </div>
                </div>
            </div>
    </form>    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-5">
                        <h3 class="box-title">Cotización Seguros Privados/Particulares</h3>
                    </div>
                    <div class="col-md-5">
                        <h4 style="color: red;">Paciente: {{$orden->id_paciente}} - {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }}</h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{route('orden.index')}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a>
                        <!--button onclick="window.history.back();" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-left"></span>Regresar</button-->
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-8 oculto" id="mensaje_membresia">
                           
                        </div>
                        <span class="oculto" id="mensaje_membresia_oculto"></span>
                        <input type="hidden" name="id_plan" id="id_plan" >
                        @if($orden->puntos_aplicados != null)
                        <span style="background-color:blue;color: white;padding:10px">PUNTOS APLICADOS: {{$orden->puntos_aplicados}}</span>
                        @endif
                    </div>

                </div>

                <div class="box-body">


                    <span class="text-red" name="mensaje">{{old('mensaje')}}</span>

                    <form class="form-vertical" id="formulario" role="form" method="POST">
                        {{ csrf_field() }}
                        <!--div class="col-md-12" style="text-align: right;">
                            <b>Valor:</b><span id="xvalor"></span>
                        </div-->

                        <!--cedula-->
                        <!--div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="control-label">Cédula</label>
                            <input id="id" maxlength="10" type="hidden" class="form-control input-sm" name="id" value="{{$orden->id_paciente}}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();" readonly>
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div-->

                        <input id="id" maxlength="10" type="hidden" class="form-control input-sm" name="id" value="{{$orden->id_paciente}}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();" readonly>
                        <input id="nombre1" class="form-control input-sm" type="hidden" name="nombre1" value="{{ $orden->paciente->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                        <input id="nombre2" type="hidden" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ $orden->paciente->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required readonly>
                        <input id="apellido1" type="hidden" class="form-control input-sm" name="apellido1" value="{{ $orden->paciente->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                        <input id="apellido2" type="hidden" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ $orden->paciente->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required readonly>


                        <!--primer nombre-->
                        <!--div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="control-label">Primer Nombre</label>

                            <input id="nombre1" class="form-control input-sm" type="hidden" name="nombre1" value="{{ $orden->paciente->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                            @if ($errors->has('nombre1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre1') }}</strong>
                            </span>
                            @endif
                        </div-->

                        <!--//segundo nombre-->
                        <!--div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="control-label">Segundo Nombre</label>

                            <input id="nombre2" type="hidden" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ $orden->paciente->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required readonly>


                            @if ($errors->has('nombre2'))
                            <span class="help-block">
                             <strong>{{ $errors->first('nombre2') }}</strong>
                            </span>
                            @endif

                        </div-->

                        <!--primer apellido-->
                        <!--div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="control-label">Primer Apellido</label>

                            <input id="apellido1" type="hidden" class="form-control input-sm" name="apellido1" value="{{ $orden->paciente->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                            @if ($errors->has('apellido1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apellido1') }}</strong>
                            </span>
                            @endif

                        </div-->

                        <!--Segundo apellido-->
                        <!--div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="control-label">Segundo Apellido</label>


                            <input id="apellido2" type="hidden" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ $orden->paciente->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required readonly>


                            @if ($errors->has('apellido2'))
                            <span class="help-block">
                             <strong>{{ $errors->first('apellido2') }}</strong>
                            </span>
                            @endif

                        </div-->

                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="control-label">Sexo</label>
                            <select id="sexo" name="sexo" class="form-control input-sm" required onchange="actualiza_cabe();">
                                <option value="">Seleccionar ..</option>
                                <option @if($orden->paciente->sexo=='1') selected @endif value="1">MASCULINO</option>
                                <option @if($orden->paciente->sexo=='2') selected @endif value="2">FEMENINO</option>

                            </select>
                            @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                            @endif
                        </div>

                        <!--fecha_nacimiento-->
                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                            <label class="control-label">Fecha Nacimiento</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{$orden->paciente->fecha_nacimiento}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required onchange="actualiza_cabe();">

                            </div>
                            @if ($errors->has('fecha_nacimiento'))
                            <span class="help-block">
                              <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                            </span>
                            @endif


                        </div>


                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('id_doctor_ieced') ? ' has-error' : '' }}">
                            <label for="id_doctor_ieced" class="control-label">Médico</label>

                            <select id="id_doctor_ieced" name="id_doctor_ieced" class="form-control input-sm" required onchange="actualiza_cabe();">
                                <option value="">Seleccione ...</option>
                            @foreach ($usuarios as $usuario)
                                <option @if($orden->id_doctor_ieced == $usuario->id) selected @endif value="{{$usuario->id}}">{{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}} {{$usuario->nombre2}}</option>
                            @endforeach
                            </select>
                            @if ($errors->has('id_doctor_ieced'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_doctor_ieced') }}</strong>
                            </span>
                            @endif

                        </div>
                        <div class="col-md-12"></div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="control-label">Seguro</label>

                            <select id="id_seguro" name="id_seguro" class="form-control input-sm" required onchange="cargar_nivel();">
                                <option value="">Seleccione ...</option>
                            @foreach ($seguros as $seguro)
                                <option @if($orden->id_seguro == $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                            @endforeach
                            </select>
                            @if ($errors->has('id_seguro'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_seguro') }}</strong>
                            </span>
                            @endif

                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="control-label">Tipo</label>

                            <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required onchange="actualiza_cabe();">
                                <option @if($orden->est_abm_hos== '0') selected @endif value="0">Ambulatorio</option>
                                <option @if($orden->est_abm_hos== '1') selected @endif value="1">Hospitalizado</option>
                            </select>

                            @if ($errors->has('est_amb_hos'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                </span>
                            @endif

                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4{{ $errors->has('pres_dom') ? ' has-error' : '' }}">
                            <label for="pres_dom" class="control-label">Presencial/Domicilio</label>

                            <select id="pres_dom" name="pres_dom" class="form-control input-sm" required onchange="actualiza_cabe();">
                                <option value="">Seleccione...</option>
                                <option @if($orden->pres_dom== '0') selected @endif value="0">Presencial</option>
                                <option @if($orden->pres_dom== '1') selected @endif value="1">A Domicilio</option>
                            </select>

                            @if ($errors->has('pres_dom'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('pres_dom') }}</strong>
                                </span>
                            @endif

                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                            <label for="id_empresa" class="control-label">Empresa</label>

                            <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                <!--option value="">Seleccionar ...</option-->
                            @foreach ($empresas as $value)
                                @if($value->id=='0993075000001')
                                <option @if(old('id_empresa') == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                @endif
                            @endforeach
                            </select>
                            @if ($errors->has('id_empresa'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_empresa') }}</strong>
                            </span>
                            @endif

                        </div>

                        <div id="div_nivel" style="margin-bottom: 0px;display: none" class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('motivo_descuento') ? ' has-error' : '' }}">

                            <label for="motivo_descuento" class="control-label">Quien autoriza Descuento y por que medio</label>
                            
                            <input type="text" required name="motivo_descuento" id="motivo_descuento" min="0" max="100" step="0.01" value="{{$orden->motivo_descuento}}" class="form-control input-sm" onchange="actualiza_cabe()" @if($orden->puntos_aplicados != null) readonly @endif >   

                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('codigo') ? ' has-error' : '' }}">

                            <label for="codigo" class="control-label">Código</label>

                            <!-- <input type="text" required name="codigo" id="codigo" maxlength="10" value="{{$orden->codigo}}" class="form-control input-sm" onchange="actualiza_cabe();"> -->
                            <select  name="codigo" id="codigo" class="form-control input-sm" required onchange="actualiza_cabe();">
                                <option value="">Seleccione ...</option>
                                @foreach($codigo as $value)
                                <option @if($orden->codigo == $value->id) selected @endif value="{{$value->id}}">{{$value->id}} - {{$value->nombre1}} {{$value->apellido1}}</option>
                                @endforeach
                            </select> 
                            
                        </div>
                        @php
                            $txtprop = '';
                            $cantforma_pago = Sis_medico\Examen_Detalle_Forma_Pago::where('id_examen_orden',$orden->id)->count();
                            if($cantforma_pago > 0){
                                $txtprop = 'readonly';
                            }
                        @endphp
                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('descuento_p') ? ' has-error' : '' }}">

                            <label for="descuento_p" class="control-label">Descuento %</label>
                            
                            <input type="number" required name="descuento_p" id="descuento_p" min="0" max="100" step="0.01" value="{{$orden->descuento_p}}" class="form-control input-sm" {{$txtprop}} onchange="cotizador_recalcular();" @if($orden->puntos_aplicados != null) readonly @endif>   

                        </div>

                        <!--div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('id_forma_pago') ? ' has-error' : '' }}">
                            <label for="id_forma_pago" class="control-label">Forma de Pago</label>

                            <select id="id_forma_pago" name="id_forma_pago" class="form-control input-sm" onchange="cotizador_recalcular();">

                            @foreach ($formas as $value)

                                <option @if($orden->id_forma_de_pago == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>

                            @endforeach
                            </select>
                            @if ($errors->has('id_forma_pago'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_forma_pago') }}</strong>
                            </span>
                            @endif

                        </div-->

                        <div style="margin-bottom: 0px;" class="form-group col-md-4 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                            <label for="id_protocolo" class="control-label">Protocolo</label>

                            <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="protocolo();">
                                <option value="0">Seleccionar ...</option>
                            @foreach ($protocolos as $protocolo)
                                <option @if($orden->id_protocolo == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                            @endforeach
                            @foreach ($protocolos2 as $protocolo)
                                <option @if($orden->id_protocolo == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                            @endforeach
                            </select>
                            @if ($errors->has('id_protocolo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_protocolo') }}</strong>
                            </span>
                            @endif

                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-2 {{ $errors->has('cobrar_pac_pct') ? ' has-error' : '' }}">
                            <label for="cobrar_pac_pct" class="control-label">Cobrar Paciente(%)</label>
                            <input type="number" required name="cobrar_pac_pct" id="cobrar_pac_pct" min="0" max="100" step="0.01" value="{{$orden->cobrar_pac_pct}}" class="form-control input-sm" {{$txtprop}} onchange="cotizador_recalcular();">
                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-2 {{ $errors->has('numero_oda') ? ' has-error' : '' }}">
                            <label for="numero_oda" class="control-label">Número de Oda</label>
                            <input type="text" required name="numero_oda" id="numero_oda" min="0" max="100" step="0.01" value="{{$orden->numero_oda}}" class="form-control input-sm" onchange="actualiza_cabe();">
                        </div>

                        <div style="margin-bottom: 0px;" class="form-group col-md-4 ">
                            <label for="idasesor" class="control-label">Asesor de venta</label>
                            <select class="form-control input-sm select2_asesor" onchange="actualiza_cabe()" style="width: 100%;" name="idasesor" id="idasesor" >
                                @if(!is_null($asesor)) <option  selected  value="{{$asesor->id}}">{{$asesor->nombre1}} {{$asesor->nombre2}} {{$asesor->apellido1}} {{$asesor->apellido2}}  </option> @endif
                            </select>
                        </div>
                        <div style="margin-bottom: 0px;" class="form-group col-md-4 ">
                            <label for="observacion_adicional" class="control-label">Observación Adicional</label>
                            <textarea rows="1" name="observacion_adicional" id="observacion_adicional" onblur="actualiza_cabe()" name="observacion_adicional" class="form-control">{{$orden->observacion_adicional}}</textarea>
                        </div>
                        <div style="margin-bottom: 0px;" class="form-group col-md-12 "></div>

                        @php $cont=count(old('examen')) @endphp 

                        <div style="margin-bottom: 0px;" class="form-group col-md-2 {{ $errors->has('deducible') ? ' has-error' : '' }}">
                            <label for="deducible" class="control-label">Deducible</label>
                            <input type="text" name="deducible" id="deducible" min="0" max="100" step="0.01" value="@if($orden->deducible != null) {{$orden->deducible}} @else 0 @endif" class="form-control input-sm" onchange="cotizador_recalcular();">
                        </div>

                        <input type="hidden" name="cotizacion" value="{{$orden->id}}">

                        <div class="col-md-12">&nbsp;</div>
                        <!-- BUSCADOR COTIZADOR-->
                        <div class="form-group col-md-5{{ $errors->has('buscador2') ? ' has-error' : '' }}">
                            <label for="buscador2" class="control-label">Buscar Agrupador</label>
                            <div class="input-group">
                                <input id="buscador2" class="form-control input-sm" type="text" name="buscador2" value="{{ old('buscador2') }}" required autofocus onchange="cargar_buscador();" style="background-color: #fff0e6;">
                                @if ($errors->has('buscador2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('buscador2') }}</strong>
                                </span>
                                @endif
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador2').value = '';cargar_buscador();"></i>
                                </div>

                            </div>
                        </div>



                        <!-- BUSCADOR COTIZADOR-->
                        <div class="form-group col-md-5{{ $errors->has('buscador') ? ' has-error' : '' }}">
                            <label for="buscador" class="control-label">Buscar Examen</label>
                            <div class="input-group">
                                <input id="buscador" class="form-control input-sm" type="text" name="buscador" value="{{ old('buscador') }}" required autofocus onchange="cargar_buscador();" style="background-color: #fff0e6;">
                                @if ($errors->has('buscador'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('buscador') }}</strong>
                                </span>
                                @endif
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador').value = '';cargar_buscador()"></i>
                                </div>
                                <div class="input-group-addon">
                                <i style="cursor: pointer" class="fa fa-search" aria-hidden="true"></i>
                              </div>
                            </div>
                        </div>

                        <div class="form-group col-md-5">
                             
                            <!-- <input id="buscadorasesor" class="form-control input-sm" onblur="actualiza_cabe()" type="text" name="buscadorasesor"    style="background-color: #fff0e6;">
                            <input type="hidden" id="idasesor" name="idasesor"> -->
                        </div>

                        <div class="form-group col-md-2">
                            <input id="seleccionados" name="seleccionados" type="checkbox" class="flat-red" onchange="quitar_buscador();cargar_buscador();" value="1" @if($orden->detalles->count() > 0) checked @endif><label style="color: red;font-size: 14px;"> Ver Seleccionados</label>
                        </div>

                        <div id="div_buscador" class="form-group col-md-12"></div>






                    </form>
                </div>

            </div>
        </div>
    </div>

</section>


<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
<script type="text/javascript">

    $('.select2_asesor').select2({
      placeholder: 'Ingrese Asesor',
      allowClear: true, 
      ajax: {
        url: '{{route("cierrecaja.buscar_usuarios")}}',
        data: function(params) {
          var query = {
            search: params.term,
            type: 'public'
          }
          return query;
        },
        processResults: function(data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          console.log(data);
          return {
            results: data
          };
        }
      },
    });

    /*$("#buscadorasesor").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('ticketpermisos.vh_buscar_usuario')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term,
                    tipo: 7,
                },
                dataType: "json",
                type: 'post',
                success: function(data){
                    //console.log(data);
                    response(data);
                }
            })
        },
        minLength: 3,
        select: function(data, ui){

            //console.log("++"+ui.item.codigo);
            $('#idasesor').val(ui.item.id);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    } );*/


    document.querySelector('#formulario').addEventListener('keydown', function (e) {
        if (e.code === 'Enter'){
            e.preventDefault();
        }
    });

    function crear_forma_pago(id_orden){
        $.ajax({
                type: 'get',
                url: "{{ url('cotizador/datos_forma')}}/"+id_orden,
                datatype: 'json',
          success: function(datahtml){
              //alert("sucess");
              $("#crear_registro").empty().html(datahtml);
              $("crear_registro").show();

          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }

    function agregar_forma_pago_gastro(id_orden){
      //alert("hola");
        $.ajax({
                type: 'get',
                url: "{{ url('cotizador/datos_forma')}}/"+id_orden,
                datatype: 'json',
          success: function(datahtml){
              //alert("sucess");
              $("#crear_registro_gastroclinica").empty().html(datahtml);
              //$("crear_registro").show();

          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }


    function crear_forma_pago_gastroclinica(){
        var confirmar = confirm("Desea Agregar forma de pago para EndoscopyNet");
        if(confirmar){
            $('#info_factura').show();
            $('#div_boton_gastro').hide();
            $('#crear_registro').hide();
        }

    }

    /*function crear_orden_venta(id_orden){
      //alert("hola");

        $.ajax({
            type: 'get',
            url: "{{ url('gastroclinica/facturacion/labs')}}/"+id_orden,
            datatype: 'json',
            success: function(datahtml){
            $("#crear_registro_gastroclinica").empty().html(datahtml);
            $("#forma_pago_tabla_gastro").show();
        },
        error:  function(){
                alert('error al cargar');
            }
        });

    }*/

    function cargar_forma_pago_tabla(id_orden){
        $.ajax({
                type: 'get',
                url: "{{ url('cotizador/forma_pago/ajax')}}/"+id_orden,
                datatype: 'json',
          success: function(datahtml){

              $("#forma_pago_tabla").empty().html(datahtml);

          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }

    function cargar_forma_pago_gastro_tabla(id_ordv){
        $.ajax({
                type: 'get',
                url: "{{ url('gastroc/forma_gastro/ajax')}}/"+id_ordv,
                datatype: 'json',
          success: function(datahtml){

              $("#forma_pago_gastro_tabla").empty().html(datahtml);

          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }

    function cargar_valor_oda(id_ordv){
        $.ajax({
                type: 'get',
                url: "{{ url('gastroc/pago/calculo_oda')}}/"+id_ordv,
                datatype: 'json',
          success: function(datahtml){

              $("#div_calcula_oda").empty().html(datahtml);

          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }


    

    $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });

    $('input[type="checkbox"].flat-red').on('ifChecked', function(event){
        quitar_buscador();
        cargar_buscador();
      });

    $('input[type="checkbox"].flat-red').on('ifUnchecked', function(event){
        cargar_buscador();
      });

    function quitar_buscador(){
        $('#buscador').val('');
        $('#buscador2').val('');
      }

    var js_examenes = [];
    $(document).ready(function() {

        cargar_nivel_ini();
        busca_membresia();
        //cargar_buscador();



        $('#fecha_nacimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075

        });



        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'


            });

        $(".breadcrumb").append('<li><a href="{{asset('orden/')}}"> Inicio</a></li>');
        $(".breadcrumb").append('<li class="active">Solicitar</li>');


        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

       @if($orden->id_protocolo==null)

        promociones();

       @endif

       $("#buscador2").autocomplete({
            source: function( request, response ) {

                $.ajax({
                    url:"{{route('agrupador_labs.buscar_aj')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    data: {
                        term: request.term
                          },
                    dataType: "json",
                    type: 'post',
                    success: function(data){
                        response(data);
                        //console.log(data);

                    },

                })
            },
            minLength: 2,
            select: function(data, ui){
                //console.log(ui.item.label);
                $('#buscador2').val(ui.item.label);
                cargar_buscador();
            }
        } );

        $("#buscador").autocomplete({
            source: function( request, response ) {

                $.ajax({
                    url:"{{route('examenes.buscar_aj')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    data: {
                        term: request.term
                          },
                    dataType: "json",
                    type: 'post',
                    success: function(data){
                        response(data);
                        //console.log(data);

                    },

                })
            },
            minLength: 3,
            select: function(data, ui){
                //console.log(ui.item.label);
                $('#buscador').val(ui.item.label);
                cargar_buscador();
            }
        } );

        /*$("#buscador2").change( function(){
            $.ajax({
                type: 'post',
                url:"{{route('epicrisis.cie10_nombre2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype:'json',
                data: $("#cie10"),
                success: function(data){
                    console.log(data);
                    if(data!='0'){
                        $('#codigo').val(data.id);
                    }

                },
                error: function(data){

                }
            })
        }); */


    });

    function promociones(){
        //console.log("promociones");
    //ingreso rapido de promos
        var xseguro = $('#id_seguro').val();
        var xdoctor = $('#id_doctor_ieced').val();
         //seguros 30, 31 o 32 protocolos 10, 11 o 12
        if(xseguro=='30'){
            //0001 covid-19
            var xcodigo = '0001';
            var xprotocolo = 10;

            if(xdoctor=='1234517896'){
                $("#codigo option[value="+ xcodigo +"]").attr("selected",true);
            }
            $("#id_protocolo option[value="+ xprotocolo +"]").attr("selected",true);


            actualiza_cabe_sin_buscar();
            protocolo();

        }
        if(xseguro=='31'){
            //0001 covid-19
            var xcodigo = '0001';
            var xprotocolo = 11;
            if(xdoctor=='1234517896'){
                $("#codigo option[value="+ xcodigo +"]").attr("selected",true);
            }
            $("#id_protocolo option[value="+ xprotocolo +"]").attr("selected",true);

            actualiza_cabe_sin_buscar();
            protocolo();

        }
        if(xseguro=='32'){
            //0001 covid-19
            var xcodigo = '0001';
            var xprotocolo = 12;
            if(xdoctor=='1234517896'){
                $("#codigo option[value="+ xcodigo +"]").attr("selected",true);
            }
            $("#id_protocolo option[value="+ xprotocolo +"]").attr("selected",true);

            actualiza_cabe_sin_buscar();
            protocolo();

        }
    }


    var buscapaciente = function ()
    {


        var js_paciente = document.getElementById('id').value;

        $.ajax({
            type: 'get',
            url: "{{ url('hospitalizados/buscapaciente')}}/"+js_paciente, //hospitalizados.buscapaciente

            success: function(data){
                if(data=='no'){

                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    $('#fecha_nacimiento').val(data.fecha_nacimiento);
                    //$('#procedencia').focus();
                }
            }
        });

    }
    var contador = 0;
    function cargar_buscador(){
        //console.log('cargar_buscador');
        //console.log(document.getElementById('formulario'));
        contador ++;
        //console.log(contador);
        $.ajax({
            type: 'post',
            url:"{{route('agrupador_labs.buscar')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                //console.log(data);
                $('#div_buscador').empty().html(data);
                if(contador == 1){
                    //domicilio();promo_covid();
                }

            },
            error: function(data){

                }
        })

    }
    function actualiza_cabe(){
        //alert("hola");
        $.ajax({
            type: 'post',
            url:"{{route('cotizador.cotizador_cabecera')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                //console.log(data);
                if(data!='ok'){
                    $('#div_buscador').empty().html(data);
                }else{
                    cargar_buscador();
                }



            },
            error: function(data){

                }
        })
    }

    function actualiza_cabe_sin_buscar(){
        //alert("hola");
        $.ajax({
            type: 'post',
            url:"{{route('cotizador.cotizador_cabecera')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                //console.log(data);
                if(data!='ok'){
                    $('#div_buscador').empty().html(data);
                }else{

                }
            },
            error: function(data){

                }
        })
    }

    function cargar_nivel(){
        //console.log('nivel');
        var xseguro = $('#id_seguro').val();
        var js_seguro = document.getElementById('id_seguro').value;

        /*if(js_seguro =='1'){
            //$('#div_nivel').addClass('oculto');
            $('#div_nivel').hide();
        }else{
            //$('#div_nivel').removeClass('oculto');
            $('#div_nivel').show();
        }*/

        $.ajax({
            type: 'post',
            url:"{{route('agrupador_labs.nivel')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                if(data!='no'){
                    $('#div_nivel').empty().html(data);
                    //$('#div_nivel').removeClass('oculto');
                    $('#div_nivel').show();
                }else{
                    //$('#div_nivel').addClass('oculto');
                    $('#div_nivel').hide();
                    $('#div_nivel').empty().html('');
                }
                //cargar_buscador();
                //deseleccionar();
                //cotizador_crear();
                if(xseguro>='30' && xseguro<='32' ){
                    promociones();
                }else{
                    cotizador_recalcular();
                }
            },
            error: function(data){

                }
        });
    }

    function cargar_nivel_ini(){
        //console.log("cargar_nivel_ini");
        var js_seguro = document.getElementById('id_seguro').value;
        //alert(js_seguro);
        /*if(js_seguro =='1'){
            //$('#div_nivel').addClass('oculto');
            $('#div_nivel').hide();
        }else{
            //$('#div_nivel').removeClass('oculto');
            $('#div_nivel').show();
        }*/

        $.ajax({
            type: 'post',
            url:"{{route('agrupador_labs.nivel')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                if(data!='no'){
                    $('#div_nivel').empty().html(data);
                    $('#div_nivel').show();

                }else{
                    $('#div_nivel').hide();
                    $('#div_nivel').empty().html('');
                }
                cargar_buscador();



            },
            error: function(data){

                }
        });
    }

    function cotizador_crear(){
        //alert("crear");

        $.ajax({
            type: 'post',
            url:"{{route('cotizador.store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                //alert("creo");
                cargar_buscador();
                recalcularsinpreguntar();




            },
            error: function(data){

                }
        })
    }

    function cotizador_recalcular(){
        //console.log("cotizador_recalcular");
        @if($orden->puntos_aplicados == null)
        swal.fire({
            title: 'Al confirmar se procederá a recalcular los valores de la cotización',
            //text: "You won't be able to revert this!",
            icon: "warning",
            type: 'warning',
            buttons: true,

        }).then((result) => {
          if (result.value) {
            $.ajax({
                type: 'post',
                url:"{{route('cotizador.recalcular')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#formulario").serialize(),
                success: function(data){
                    //console.log(data);

                    cargar_buscador();
                    console.log(data);

                },
                error: function(data){
                    alert("Error al ejecutar");
                    }
            })

          }
        })
        @else
             nosepuedeeditar();
        @endif

            
    }

    function recalcularsinpreguntar(){
        $.ajax({
            type: 'post',
            url:"{{route('cotizador.recalcular')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data){
                //console.log(data);
                
                cargar_buscador();
                console.log(data);
                
            },
            error: function(data){
                alert("Error al ejecutar");       
                }
        }) 
    }


    function cotizador_crear_id(id){
        //alert(id);
        $.ajax({
            type: 'get',
            url:"{{url('cotizador/update')}}/{{$orden->id}}/"+id,

            datatype: 'json',

            success: function(data){
                //console.log('crear');
                //alert(data);
                //cargar_buscador();
                //cotizador_recalcular();
                $('#scantidad').empty().html(data.cantidad);
                $('#svalor').empty().html(data.valor);
                $('#sdescuento_valor').empty().html(data.descuento_valor);
                $('#srecargo_valor').empty().html(data.recargo_valor);
                let total_valor = data.total_valor;
                total_valor = total_valor.toFixed(2);
                $('#stotal_valor').empty().html(total_valor);
                $('#tiene_domicilio').val(data.tiene_domicilio);
                $('#tiene_covid').val(data.tiene_covid);
                $('#valor_covid').val(data.valor_covid);
                $('#stotal').val(data.total_valor);
                var membresia = $('#id_plan').val();
                //console.log("vh --");
                //console.log(membresia);
                if(membresia != ''){
                    $('#motivo_descuento').val(data.motivo);
                    $('#descuento_p').val(data.descuento_p);
                }
                //domicilio();promo_covid();



            },
            error: function(data){

                }
        })
    }

    function cotizador_delete_id(id){
        //alert(id);
        $.ajax({
            type: 'get',
            url:"{{url('cotizador/delete')}}/{{$orden->id}}/"+id,

            datatype: 'json',

            success: function(data){
                //alert(data);
                //cargar_buscador();
                //cotizador_recalcular();
                $('#scantidad').empty().html(data.cantidad);
                $('#svalor').empty().html(data.valor);
                $('#sdescuento_valor').empty().html(data.descuento_valor);
                $('#srecargo_valor').empty().html(data.recargo_valor);
                $('#stotal_valor').empty().html(data.total_valor);
                $('#tiene_domicilio').val(data.tiene_domicilio);
                $('#tiene_covid').val(data.tiene_covid);
                $('#valor_covid').val(data.valor_covid);
                $('#stotal').val(data.total_valor);
                //domicilio();promo_covid();
                var membresia = $('#id_plan').val();
                //console.log("vh --");
                //console.log(membresia);
                if(membresia != ''){
                    $('#motivo_descuento').val(data.motivo);
                    $('#descuento_p').val(data.descuento_p);
                }



            },
            error: function(data){

                }
        })
    }

    function protocolo(){

        @if($orden->puntos_aplicados == null)

        //console.log("protocolo");
        $('#seleccionados').iCheck('check');
        swal.fire({
            title: 'Al confirmar se agregaran los examenes del protocolo',
            //text: "You won't be able to revert this!",
            icon: "warning",
            type: 'warning',
            buttons: true,

        }).then((result) => {
          if (result.value) {
            //$('input[type="checkbox"].flat-orange').prop('checked',false).iCheck('update');

            var protocolo = document.getElementById('id_protocolo').value;
            //alert(protocolo);
            $.ajax({
                type: 'get',
                url: "{{ url('cotizador/protocolo')}}/"+"{{$orden->id}}"+"/"+protocolo, //protocolo.buscaexamen

                success: function(data){
                    //alert("ok");
                    //console.log(data);
                    //cotizador_recalcular();
                    cargar_buscador();


                },
                error: function(data){
                    //alert("error");
                }
            });

          }
        })

        @else

            nosepuedeeditar();

        @endif

    } 

    function busca_membresia(){

        var js_paciente = document.getElementById('id').value;
        $.ajax({
            type: 'get',
            url: "{{ url('labs_membresias')}}/{{$orden->paciente->id}}",
            success: function(data){
                $('#mensaje_membresia').empty().html('');
                if(data.estado == 'ok'){
                    $('#mensaje_membresia').empty().html('<span style="background-color:green;color: white;padding:10px">PACIENTE CON MEMBRESIA :'+data.nombre+'</span>&nbsp;<button type="button" class="btn btn-info" onclick="aplicar_puntos('+data.puntos+')">PUNTOS ACUMULADOS:'+data.puntos+'</button>');
                    $('#mensaje_membresia').removeClass('oculto');
                    $('#id_plan').val(data.id);
                }else{
                    $('#mensaje_membresia').addClass('oculto');
                    $('#mensaje_membresia_oculto').text(data.mensaje);
                    $('#id_plan').val('');
                }
            }
        });

    }

    function nosepuedeeditar(){
    
    alert("Ya se aplicaron puntos a esta cotización, no se puede editar");

    location.reload();  
  
  }







</script>
@endsection
