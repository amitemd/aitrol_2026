@extends('contable.rol_pago.base')
@section('action-content')

<style type="text/css">

    .separator{
      width:100%;
      height:30px;
      clear: both;
    }

    .alerta_guardado{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .head-title{
      background-color: #888;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .separator1{
      width:100%;
      height:8px;
      clear: both;
    }

    .card-header{
      border-radius: 6px 6px 0 0;
      background-color: #3c8dbc;
      border-color: #b2b2b2;
      padding: 8px;
      font-family: 'Roboto', sans-serif;
    }

    .div_load{
      display: none;
    }

    .texto{
      font-size: 12px;
    }

    .table-responsive{
      padding-left: 0;
      padding-right: 0;
    }

  </style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
@php $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'); @endphp
<section class="content">
  <div class="modal fade" id="otros_anticipos_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="div_otros_anticipos">
      </div>
    </div>
  </div>

  <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item"><a href="{{route('nomina.index')}}">Empleado</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rol de Pago</li>
      </ol>
  </nav>
  <div class="box">
    <div class="box-header color_cab">
      <div class="col-md-7">
        <h5><b>EDITAR ROL DE PAGO</b></h5>
      </div>
     
      <div class="col-md-3 text-right" id="act_rol">
          <button id="actualizar" onclick="actualizar_valores();" class="btn btn-primary btn-gray"> Actualizar </button>
          <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $rol->id])}}" class="btn btn-primary btn-gray"><span class="glyphicon glyphicon-download-alt"></span> PDF</a>
          <button id="crear_rol_pago" onclick="enviar_correo();" class="btn btn-primary btn-gray">
            ENVIAR CORREO
          </button>
      </div>
      
      <div class="col-md-1 text-right">
          <button onclick="goBack()" class="btn btn-default btn-gray" >
              <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
          </button>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="row head-title">
        <div class="col-md-12">
         <label class="color_texto" for="title">DETALLES ROL PAGO</label>
        </div>
      </div>

      <form id="actualiza_rol_pago" method="post">
        <input  name="id_rol" id="id_rol" type="text" class="hidden" value="{{$rol->id}}">
        <input  name="id_detalle_rol" id="id_detalle_rol" type="text" class="hidden" value="{{$detalle_rol->id}}">
        <div class="form-group  col-xs-6">
          <label for="year" class="col-md-7 texto">Apellidos:</label>
          <h4>{{$rol->usuario->apellido1}} {{$rol->usuario->apellido2}}</h4>  
        </div>
        <div class="form-group  col-xs-6">
          <label for="year" class="col-md-7 texto">Nombres:</label>
          <h4>{{$rol->usuario->nombre1}} {{$rol->usuario->nombre2}}</h4>  
        </div>
        <!--Anio-->
        <div class="form-group  col-xs-4">
          <label for="year" class="col-md-7 texto">Año</label>
          <label for="year" class="col-md-5 texto">{{$rol->anio}}</label>  
        </div>
        <!--Mes-->
        <div class="form-group  col-xs-4">
          <label for="mes" class="col-md-7 texto">Mes</label>
          <label for="mes" class="col-md-5 texto">{{$meses[$rol->mes - 1]}}</label>
        </div>
        <!--Tipo Rol -->
        <div class="form-group  col-xs-4">
          <label for="tipo_rol" class="col-md-7 texto">Tipo</label>
          <label for="tipo_rol" class="col-md-5 texto">{{$rol->tipo_rol->descripcion}}</label>
        </div>
        <!--Sueldo Mensual-->
        <div class="form-group  col-xs-4">
          <label for="sueldo_mensual" class=" col-md-7 texto">Sueldo:</label>
          <label for="sueldo_mensual" class=" col-md-5 texto">{{$nomina->sueldo_neto}}</label>
        </div>
        <!--Dias Laborados-->
        <div class="form-group  col-xs-2">
          <label for="dias_laborados" class=" col-md-7 texto">Dias Laborados:</label>
          <div class="col-md-8">
            <input id="dias_laborados" name="dias_laborados" type="text" class="form-control input-sm" value="{{$detalle_rol->dias_laborados}}" onkeypress="return isNumberKey(event)" autocomplete="off" onchange="recalcular();">
          </div>
        </div>
        <!--Sueldo Recibir-->
        <div class="form-group  col-xs-3">
          <label for="sueldo_recibir" class=" col-md-5 texto">Sueldo Recib:</label>
          <div class="col-md-8">
            <input  id="sueldo_recibir" name="sueldo_recibir" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->sueldo_mensual}}@endif" readonly>
          </div>
        </div>
        <!--Sueldo Pasante-->
        <div class="form-group  col-xs-3">
          <label for="sueldo_pasante" class=" col-md-5 texto">Sueldo Pasante:</label>
          <div class="col-md-8">
            <input  id="sueldo_pasante" name="sueldo_pasante" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->sueldo_pasante}}@endif" onkeypress="return isNumberKey(event)" onchange="recalcular();">
          </div>
        </div>
        <div class="separator1"></div>
                <div class="row head-title">
                  <div class="col-md-12">
                  <label class="color_texto" for="title">INGRESOS</label>
                  <div class="col-md-12" style="height: 15px;">&nbsp;</div>
                  <div class= "col-md-12">
                  </div>
                  </div>
                </div>
        <!--Cantidad Horas al 50%-->
        <div class="form-group  col-xs-6">
          <label for="cant_horas_50" class="col-md-5 texto">Cantidad Horas 50%:</label>
          <div class="col-md-6">
            <input  id="cant_horas_50" name="cant_horas_50" type="text" class="form-control input-sm" value="{{$detalle_rol->cantidad_horas50}}" onkeypress="return isNumberKey(event)"  autocomplete="off" onchange="recalcular();" >
          </div>
        </div>
        <!--Sobre Tiempo 50%-->
        <div class="form-group  col-xs-6">
          <label for="sobre_tiempo_50" class="col-md-5 texto">Horas al 50%:</label>
          <div class="col-md-7">
            <input  id="sobre_tiempo_50" name="sobre_tiempo_50" type="text" class="form-control input-sm" value="{{$detalle_rol->sobre_tiempo50}}" readonly>
          </div>
        </div>
        <!--Observacion_50-->
        <div class="form-group col-xs-6">
          <label for="observacion_horas_extras_50" class="col-md-5 texto">Observación HE 50%:</label>
          <div class="col-md-6">
            <input  id="observacion_horas_extras_50" name="observacion_horas_extras_50" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_horas_extras_50}}" onchange="guardar()">
          </div>
        </div>
        <div class="form-group col-xs-6">
          <br><br>
        </div>
        <!--Cantidad Horas al 100%-->
        <div class="form-group  col-xs-6">
          <label for="cant_horas_100" class="col-md-5 texto">Cantidad Horas 100%:</label>
          <div class="col-md-6">
            <input  id="cant_horas_100" name="cant_horas_100" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->cantidad_horas100}}@else 0.00 @endif"  onkeypress="return isNumberKey(event)" autocomplete="off" onchange="recalcular();">
          </div>
        </div>
        <!--Sobre Tiempo 100%-->
        <div class="form-group  col-xs-6">
          <label for="sobre_tiempo_100" class="col-md-5 texto">Horas al 100%:</label>
          <div class="col-md-7">
            <input  id="sobre_tiempo_100" name="sobre_tiempo_100" type="text" class="form-control input-sm" value="{{$detalle_rol->sobre_tiempo100}}"  readonly>
          </div>
        </div>
        <!--Observacion_50-->
        <div class="form-group col-xs-6">
          <label for="observacion_horas_extras_100" class="col-md-5 texto">Observación HE 100%:</label>
          <div class="col-md-6">
            <input  id="observacion_horas_extras_100" name="observacion_horas_extras_100" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_horas_extras_100}}" onchange="guardar()">
          </div>
        </div>
        <div class="form-group col-xs-6">
          <br><br>
        </div>
        <!--Bonificacion-->
        <div class="form-group  col-xs-6">
          <label for="valor_bono" class="col-md-5 texto">Bono:</label>
          <div class="col-md-6">
            <input  id="valor_bono" name="valor_bono" type="text" class="form-control input-sm" value="{{$detalle_rol->bonificacion}}" onkeypress="return isNumberKey(event)" onchange="recalcular();">
          </div>
        </div>
        <!--Observacion_Bono-->
        <div class="form-group col-xs-6">
          <label for="observacion_bono" class="col-md-2 texto">Observación:</label>
          <div class="col-md-10">
            <input  id="observacion_bono" name="observacion_bono" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_bono}}" onchange="guardar()">
          </div>
        </div>
        <!--Bono Imoputable-->
        <div class="form-group  col-xs-6">
          <label for="bono_imputable" class="col-md-5 texto">Bono Imputable:</label>
          <div class="col-md-6">
            <input id="bono_imputable" name="bono_imputable" type="text" class="form-control input-sm" value="{{$detalle_rol->bono_imputable}}" onkeypress="return isNumberKey(event)" onchange="recalcular();">
          </div>
        </div>
        <!--Observacion_Bono imputable-->
        <div class="form-group col-xs-6">
          <label for="observacion_bonoimp" class="col-md-2 texto">Observación:</label>
          <div class="col-md-10">
            <input id="observacion_bonoimp" name="observacion_bonoimp" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_bonoimp}}" onchange="guardar()">
          </div>
        </div>
        <!--Alimentacion-->
        <div class="form-group  col-xs-6">
          <label for="alimentacion" class="col-md-5 texto">Alimentacion:</label>
          <div class="col-md-6">
            <input id="alimentacion" name="alimentacion" type="text" class="form-control input-sm" value="{{$detalle_rol->alimentacion}}"  onkeypress="return isNumberKey(event)" onchange="recalcular();">
          </div>
        </div>
        <!--Observacion_Alimentacion-->
        <div class="form-group col-xs-6">
          <label for="observacion_alimentacion" class="col-md-2 texto">Observación:</label>
          <div class="col-md-10">
            <input id="observacion_alimentacion" name="observacion_alimentacion" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_alimentacion}}" onchange="guardar()">
          </div>
        </div>
          
        <!--Transporte-->
        <div class="form-group  col-xs-6">
          <label for="transporte" class="col-md-5 texto">Transporte:</label>
          <div class="col-md-6">
            <input id="transporte" name="transporte" type="text" class="form-control input-sm" value="{{$detalle_rol->transporte}}"  onkeypress="return isNumberKey(event)" onchange="recalcular();">
          </div>
        </div>
        <!--Observacion_Transporte-->
        <div class="form-group col-xs-6">
          <label for="observacion_transporte" class="col-md-2 texto">Observación:</label>
          <div class="col-md-10">
            <input id="observacion_transporte" name="observacion_transporte" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_transporte}}" onchange="guardar()">
          </div>
        </div>

        @foreach($rubros_ingresos as $rubro)
        <!--Transporte-->
        <div class="form-group  col-xs-6">
          <label for="val_{{$rubro->id}}" class="col-md-5 texto">{{$rubro->descripcion}}:</label>
          <div class="col-md-6">
            <input id="val_{{$rubro->id}}" name="val_{{$rubro->id}}" type="text" class="form-control input-sm" value="{{$rubro->valor}}"  onkeypress="return isNumberKey(event)" onchange="guardar_detalle(this);">
          </div>
        </div>
        <!--Observacion_Transporte-->
        <div class="form-group col-xs-6">
          <label for="obs_{{$rubro->id}}" class="col-md-2 texto">Observación:</label>
          <div class="col-md-10">
            <input id="obs_{{$rubro->id}}" name="obs_{{$rubro->id}}" type="text" class="form-control input-sm" value="{{$rubro->observacion}}" onchange="guardar_detalle(this)">
          </div>
        </div>
        @endforeach

        <!--Vacaciones pagadas en el rol-->
        <div class="form-group col-xs-6">
          <label for="vacaciones" class="col-md-5 texto">Vacaciones:</label>
          <div class="col-md-6">
            <input id="vacaciones" name="vacaciones" type="text" class="form-control input-sm" value="{{$detalle_rol->vacaciones}}" onkeypress="return isNumberKey(event)" onchange="recalcular();">
          </div>
        </div>
        <div class="form-group col-xs-6">
          <label for="observacion_vacaciones" class="col-md-2 texto">Observación:</label>
          <div class="col-md-10">
            <input id="observacion_vacaciones" name="observacion_vacaciones" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_vacaciones}}" onchange="guardar()">
          </div>
        </div>
        
        <!--Mensualiza Fondo de Reserva-->
        
        <div class="form-group  col-xs-6">
          
            <label class="col-md-5 texto">Fondo Reserva @if($nomina->pago_fondo_reserva == 2) Mensual @else Acumulado @endif</label>
          
          <div class="col-md-6">
            <input id="fondo_reserva" name="fondo_reserva" type="text" class="form-control input-sm" value="{{$detalle_rol->fondo_reserva}}"  onkeypress="return isNumberKey(event)" readonly >
          </div>
        </div>
      
        {{-- <div class="form-group  col-xs-6">
          
          <label class="col-md-5 texto">{{trans('nomina.extension_de_conyuge')}} </label>
        
        <div class="col-md-6">
          <input id="extension_de_conyuge" name="extension_de_conyuge" type="text" class="form-control input-sm" value="{{$detalle_rol->extension_conyuge}}"  readonly >
        </div>
      </div> --}}
        <div class="form-group col-xs-12"></div>
        <!--Mensualiza Decimo Tercero-->
        <div class="form-group  col-xs-6">
        
              <label class="col-md-5 texto">Decimo Tercer @if($nomina->decimo_tercero == 2) Mensual @else Acumulado @endif</label>
          
          <div class="col-md-6">
            <input id="decimo_tercero" name="decimo_tercero" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->decimo_tercero}}@endif"  onkeypress="return isNumberKey(event)" readonly>
          </div>
        </div>
        <div class="form-group col-xs-12"></div>
        <!--Mensualiza Decimo Cuarto -->
        <div class="form-group  col-xs-6">
          <label for="decimo_cuarto" class="col-md-5 texto">Decimo Cuart @if($nomina->decimo_cuarto == 2) Mensual @else Acumulado @endif</label>
          <div class="col-md-6">
            <input id="decimo_cuarto" name="decimo_cuarto" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->decimo_cuarto}}@endif"  onkeypress="return isNumberKey(event)" readonly>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="total_ingresos" class="col-md-5 texto">Total Ingresos</label>
          <div class="col-md-6">
            <input id="total_ingresos" name="total_ingresos" type="text" class="form-control input-sm" value="{{$detalle_rol->total_ingresos}}"  onkeypress="return isNumberKey(event)" readonly>
          </div>
        </div>
          
        <div class="separator1"></div>
        <div class="row head-title">
          <div class="col-md-12">
          <label class="color_texto" for="title">EGRESOS</label>
          <div class="col-md-12" style="height: 15px;">&nbsp;</div>
          <div class= "col-md-12">
          </div>
          </div>
        </div>

        <!--Calculo de Aporte al IESS-->
        <div class="form-group  col-xs-6">
          <div class="col-md-4">
            <label>Aporte IESS:</label>
          </div>
          <div class="col-md-6">
            <input onchange="recalcular();" id="iess" name="iess" type="text" class="form-control input-sm" value="{{$detalle_rol->porcentaje_iess}}" readonly >
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label class="col-md-5 texto">Extension de Conyuge</label>
        <div class="col-md-6">
          <input id="extension_de_conyuge" name="extension_de_conyuge" type="text" class="form-control input-sm" value="{{$detalle_rol->extension_conyuge}}"  readonly >
        </div>
      </div>
        <div class="form-group col-xs-12"></div>
          <!--Seguro Privado-->
          <div class="form-group  col-xs-6">
            <label for="seguro_privado" class="col-md-4 texto">Seguro Privado:</label>
            <div class="col-md-6">
              <input onchange="recalcular();"  id="seguro_privado" name="seguro_privado" type="text" class="form-control input-sm" value="{{$detalle_rol->seguro_privado}}" onkeypress="return isNumberKey(event)" readonly>
            </div>
          </div>
          <!--observacion_seguro_privado-->
          <div class="form-group col-xs-6">
              <label for="observacion_seg_priv" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input onchange="guardar();"  id="observacion_seg_priv" name="observacion_seg_priv" type="text" class="form-control input-sm" value="{{$detalle_rol->observ_seg_privado}}">
              </div>
          </div>
          
          <!--Impuesto a la Renta-->
          <div class="form-group  col-xs-6">
            <label for="impuesto_renta" class="col-md-4 texto">Impuesto a la Renta:</label>
            <div class="col-md-6">
              <input id="impuesto_renta" name="impuesto_renta" type="text" class="form-control input-sm" value="{{$detalle_rol->impuesto_renta}}" onchange="recalcular();" onkeypress="return isNumberKey(event)" >
            </div>
          </div>
          <!--observacion_impuesto_renta-->
          <div class="form-group col-xs-6">
            <label for="observacion_imp_rent" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="observacion_imp_rent" name="observacion_imp_rent" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->observ_imp_renta}}@endif">
            </div>
          </div>
          <!--Check Multa-->
          <!--div class="form-group col-xs-3">
            <label for="multa" class="col-md-6 texto">Multa</label>
            <div class="col-md-2">
                <input onchange="calculo_porcentaje_iess();"  style="width:17px;height:17px;" type="checkbox" id="multa" class="checkVal_multa" name="multa" value="1"
                @if(old('multa')=="1")
                  checked
                @elseif(!is_null($detalle_rol) and $detalle_rol->multa > 0)
                  checked
                @endif>
            </div>
          </div-->
          <!--Valor Multa-->
          <div class="form-group  col-xs-6" id="dato_multa">
            <label for="valor_multa" class="col-md-4 texto">Multa:</label>
            <div class="col-md-6">
              <input id="valor_multa" name="valor_multa" type="text" class="form-control input-sm" value="{{$detalle_rol->multa}}" onchange="recalcular(); return calculo_multa(this);">
            </div>
          </div>
          <!--Observacion Multa-->
          <div class="form-group col-xs-6" id="observacion_multa">
            <label for="observ_multa" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input id="observ_multa" name="observ_multa" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_multa}}" onchange="guardar()">
            </div>
          </div>
          <!--Fondo de Reserva Por Cobrar Trabajadores-->
          <div class="form-group  col-xs-6">
          <label for="fond_res_cobrar_trab" class="col-md-4 texto">Fondo de Res Cobrar:</label>
            <div class="col-md-6">
              <input id="fond_res_cobrar_trab" onload="calculo_porcentaje_iess(); return isNumberKey(event); checkformat(this)" name="fond_res_cobrar_trab" type="text" class="form-control input-sm" value="{{$detalle_rol->fond_reserv_cobrar}}" onchange="recalcular();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" readonly >
            </div>
          </div>
          <!--observacion_Fondo de Reserva Por Cobrar Trabajadores-->
          <div class="form-group col-xs-6">
            <label for="obs_fond_cob_trab" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input id="obs_fond_cob_trab" name="obs_fond_cob_trab" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_fondo_cobrar}}" onchange="guardar()">
            </div>
          </div>
          <!--Otros Egresos-->
          <div class="form-group  col-xs-6">
            <label for="otros_egresos_trab" class="col-md-4 texto">Otros Egresos:</label>
            <div class="col-md-6">
              <input  id="otros_egresos_trab" name="otros_egresos_trab" type="text" class="form-control input-sm" value="{{$detalle_rol->otros_egresos}}" onchange="recalcular();" onkeypress="return isNumberKey(event)" >
            </div>
          </div>
          <!--observacion Otros Egresos-->
          <div class="form-group col-xs-6">
            <label for="obs_otros_egres_trab" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
            <input onchange="guardar();" id="obs_otros_egres_trab" name="obs_otros_egres_trab" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_otro_egreso}}">
            </div>
          </div>
          <!--Examen de laboratorio-->
          <div class="form-group  col-xs-6">
            <label for="exam_laboratorio" class="col-md-4 texto">Examen de laboratorio:</label>
            <div class="col-md-6">
              <input id="exam_laboratorio" name="exam_laboratorio" type="text" class="form-control input-sm" value="{{$detalle_rol->exam_laboratorio}}" onchange="recalcular();" onkeypress="return isNumberKey(event)" readonly>
            </div>
          </div>
          <!--observacion Otros Egresos-->
          <div class="form-group col-xs-6">
            <label for="observ_examlaboratorio" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
            <input onchange="calculo_porcentaje_iess();"  id="observ_examlaboratorio" name="observ_examlaboratorio" type="text" class="form-control input-sm" value="@if(!is_null($detalle_rol)){{$detalle_rol->observ_examlaboratorio}}@endif">
            </div>
          </div>
          <!--Anticipo_quincena-->
          <div class="form-group  col-xs-6">
            <label for="anticipo_quincena" class="col-md-4 texto">Anticipo 1era Quinc:</label>
            <div class="col-md-6">
              <input id="anticipo_quincena" name="anticipo_quincena" type="text" class="form-control input-sm" value="{{$detalle_rol->anticipo_quincena}}" onchange="recalcular();" onkeypress="return isNumberKey(event)" readonly>
            </div>
          </div>
          <!--Concepto_Anticipo_quincena-->
          <div class="form-group  col-xs-6">
            <label for="concepto_quincena" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="guardar();"  id="concepto_quincena" name="concepto_quincena" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_anticip_quinc}}">
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="parqueo" class="col-md-4 texto">Parqueo:</label>
            <div class="col-md-6">
              <input id="parqueo" name="parqueo" type="text" class="form-control input-sm" value="{{$detalle_rol->parqueo}}" onchange="recalcular();" onkeypress="return isNumberKey(event)" >
            </div>
          </div>
          <div class="form-group col-xs-12"></div>
          <div class="form-group col-xs-6">
            <div class="card-header">
              <div class="row">
                <div class="col-md-6">
                  <b style="font-size: 10px;color: white;">PRESTAMO EMPRESA</b>
                </div>  
                <div class="col-md-6">
                  <button type="button" class="btn btn-success size_text" onclick="cargar_prestamos_rol()">
                    Agregar
                  </button>
                </div> 
              </div>   
              <div class="col-md-12" style="height: 15px;">&nbsp;</div>
              
              <div class="table-responsive col-md-12">
                <table id="example1" role="grid" aria-describedby="example2_info" >
                  <thead style="background-color: #FFF3E3">
                    <tr>
                      <th style="width: 10%; text-align: center;font-size: 11px;">Año/Mes Inicio</th>
                      <th style="width: 10%; text-align: center;font-size: 11px;">Año/mes Cobro</th>
                      <th style="width: 10%; text-align: center;font-size: 11px;">Descripciòn</th>
                      <th style="width: 10%; text-align: center;font-size: 11px;">Cuota</th>
                      <th style="width: 10%; text-align: center;font-size: 11px;">Acción</th>
                    </tr>
                  </thead>
                  <tbody >
                    @foreach($prestamos_rol as $prestamo_rol)
                    <tr>
                      <td>{{$prestamo_rol->prestamos->anio_inicio_cobro}}/{{$meses[$prestamo_rol->prestamos->mes_inicio_cobro - 1]}}</td>
                      <td>{{$prestamo_rol->anio}}/{{$meses[$prestamo_rol->mes - 1]}}</td>
                      <td>{{$prestamo_rol->prestamos->concepto}}</td>
                      <td style="text-align: right;">{{$prestamo_rol->valor_cuota}}</td>
                      <td style="text-align:center;"><button type="button" class="btn btn-danger btn-gray delete btn-xs" onclick="eliminar_prestamo_rol('{{$prestamo_rol->id}}')"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
                
              
              <!--Prestamo a Empresa-->
              <div class="form-group  col-xs-5">
                <label for="prestamo_empleado" class="col-md-12 texto">Total Prestamo:</label>
                <div class="col-md-12">
                  <input id="prestamo_empleado" name="prestamo_empleado" type="text" class="form-control input-sm" value="{{$detalle_rol->prestamos_empleado}}"  onkeypress="return isNumberKey(event)" readonly>
                </div>
              </div>
              <!--Concepto_Prestamo-->
              <div class="form-group  col-xs-6">
                <label for="concepto_prestamo" class="col-md-12 texto">Observación:</label>
                <div class="col-md-12">
                  <input onchange="guardar();" id="concepto_prestamo" name="concepto_prestamo" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_prestamo}}">
                </div>
              </div>
            </div>
          </div>  
            
          <div class="form-group col-xs-6">
            <div class="card-header">
              <div class="row">
                <div class="col-md-6">
                  <b style="font-size: 10px;color: white;">SALDOS INICIALES</b>
                </div>  
                <div class="col-md-6">
                  <button type="button" class="btn btn-success size_text" onclick="cargar_saldos_rol()">
                    Agregar
                  </button>
                </div> 
              </div>
              <div class="col-md-12" style="height: 15px;">&nbsp;</div>
              
                <div class="table-responsive col-md-12">
                  <table id="example1" role="grid" aria-describedby="example2_info">
                    <thead style="background-color: #FFF3E3">
                      <tr>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Año/Mes Inicio</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Año/mes Cobro</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Descripciòn</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Cuota</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Acción</th>
                      </tr>
                    </thead>
                    <tbody >
                      @foreach($saldos_ini as $saldo_ini)
                      <tr>
                        <td>{{$saldo_ini->saldos->anio_inicio_cobro}}/{{$meses[$saldo_ini->saldos->mes_inicio_cobro - 1]}}</td>
                        <td>{{$saldo_ini->anio}}/{{$meses[$saldo_ini->mes - 1]}}</td>
                        <td>{{$saldo_ini->saldos->observacion}}</td>
                        <td style="text-align: right;">{{$saldo_ini->valor_cuota}}</td>
                        <td style="text-align:center;"><button type="button" class="btn btn-danger btn-gray delete btn-xs" onclick="eliminar_saldo_ini('{{$saldo_ini->id}}')"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              
              <!--Saldo Inicial-->
              <div class="form-group  col-xs-5" id="dato_saldo">
                  <label for="saldo_inicial" class="col-md-12 texto">Total Saldo:</label>
                  <div class="col-md-12">
                    <input id="saldo_inicial" name="saldo_inicial" type="text" class="form-control input-sm" value="{{$detalle_rol->saldo_inicial_prestamo}}"   onkeypress="return isNumberKey(event)" readonly>
                  </div>
              </div>
              <!--Observacion_Saldo_Inicial-->
              <div class="form-group  col-xs-6" id="observacion_saldo">
                <label for="obser_saldo_inicial" class="col-md-12 texto">Observación:</label>
                <div class="col-md-12">
                  <input onchange="guardar();"  id="obser_saldo_inicial" name="obser_saldo_inicial" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_saldo_inicial}}">
                </div>
              </div>
            </div>
          </div>  
          <div class="form-group col-xs-12"></div>    
          <div class="form-group col-xs-6">
            <div class="card-header">
              <div class="row">
                <div class="col-md-6">
                  <b style="font-size: 10px;color: white;">OTROS ANTICIPOS</b>
                </div>  
                <div class="col-md-6">
                  <button type="button" class="btn btn-success size_text" onclick="cargar_anticipos()">
                    {{trans('contableM.agregar')}}
                  </button>
                  <button type="button" class="btn btn-success size_text" onclick="cargar_otros_anticipos()">
                    {{trans('contableM.agregar_otros_anticipos')}}
                  </button>
                </div> 
              </div>
              <div class="col-md-12" style="height: 15px;">&nbsp;</div>
          
                <div class="table-responsive col-md-12">
                  <table id="example1" role="grid" aria-describedby="example2_info">
                    <thead style="background-color: #FFF3E3">
                      <tr>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Año/mes Cobro</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Descripciòn</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Monto</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Acción</th>
                      </tr>
                    </thead>
                    <tbody >
                      @foreach($otros_anticipos as $anticipo)
                      <tr>
                        <td>{{$anticipo->anio_cobro_anticipo}}/{{$meses[$anticipo->mes_cobro_anticipo - 1]}}</td>
                        <td>{{$anticipo->conceptos}}</td>
                        <td style="text-align: right;">{{$anticipo->monto_anticipo}}</td>
                        <td style="text-align:center;"><button type="button" class="btn btn-danger btn-gray delete btn-xs" onclick="eliminar_anticipo('{{$anticipo->id}}')"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
      
              <!--Otros_Anticipos-->
              <div class="form-group  col-xs-6">
                <label for="otro_anticipo" class="col-md-12 texto">Total Otros Anticipos:</label>
                <div class="col-md-12">
                  <input id="otro_anticipo" name="otro_anticipo" type="text" class="form-control input-sm" value="{{$detalle_rol->otro_anticipo}}" onkeypress="return isNumberKey(event)" readonly>
                </div>
              </div>
              <!--Concepto_Otros_Anticipos-->
              <div class="form-group  col-xs-6">
                <label for="concep_otros_anticipos" class="col-md-12 texto">Observación:</label>
                <div class="col-md-12">
                  <input onchange="guardar();"  id="concep_otros_anticipos" name="concep_otros_anticipos" type="text" class="form-control input-sm" value="{{$detalle_rol->observacion_otro_anticip}}">
                </div>
              </div>
            </div>
          </div>
              
          <div class="form-group col-xs-6">
            <div class="card-header">
              <b style="font-size: 10px;color: white">CUOTA PRESTAMO QUIROGRAFARIO</b>
              <div class="col-md-12" style="height: 15px;">&nbsp;</div>
              <div class="col-md-12">
                <div class="form-group  col-xs-5" style="padding: 1px;">
                  <label for="detalle_quiro" class="col-md-12 texto" style="padding: 1px;">Detalle</label>
                  <div class="col-md-12" style="padding: 1px;">
                    <input id="detalle_quiro" name="detalle_quiro" type="text" class="form-control input-sm" value="">
                  </div>
                </div>
                <div class="form-group  col-xs-4" style="padding: 1px;">
                  <label for="cuota_quiro" class="col-md-12 texto" style="padding: 1px;">Cuota</label>
                  <div class="col-md-12">
                    <input id="cuota_quiro" name="cuota_quiro" type="text" class="form-control input-sm" value="" onkeypress="return isNumberKey(event)" style="padding: 1px;">
                  </div>
                </div>
                <div class="form-group  col-xs-3">
                  <br>
                  <button type="button" id="btn_quirografario" class="btn btn-primary size_text" onclick="cargar_quirografario();">
                    Agregar
                  </button>
                </div>  
              </div>
              <div class= "col-md-12">
                <div class="table-responsive col-md-12">
                  

                  <table id="example1" role="grid" aria-describedby="example2_info">
                    <thead style="background-color: #FFF3E3">
                      <tr>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Detalle</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Valor Cuota</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Acción</th>
                      </tr>
                    </thead>
                    <tbody id="agregar_cuot_quir">
                      @foreach($cuotas_quiro as $cuota_q)
                        <tr>
                          <td>{{$cuota_q->detalle_cuota}}</td>
                          <td style="text-align: right;">{{$cuota_q->valor_cuota}}</td>
                          <td style="text-align: center;"><a class="btn btn-danger btn-gray delete btn-xs" onclick="eliminar_cuota_quiro('{{$cuota_q->id}}');"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></a></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                
              </div>
              <div class="form-group  col-xs-12">&nbsp;</div>
              <div class="form-group  col-xs-12" id="dato_saldo">
                <label for="saldo_inicial" class="col-md-6 texto">Total Quirografario:</label>
                <div class="col-md-6">
                  <input id="saldo_inicial" name="saldo_inicial" type="text" class="form-control input-sm" value="{{$detalle_rol->total_quota_quirog}}"   onkeypress="return isNumberKey(event)" readonly>
                </div>
              </div>
            </div>
          </div>  

          <div class="form-group col-xs-6">
            <div class="card-header">
              <b style="font-size: 10px;color: white">CUOTA PRESTAMO HIPOTECARIO</b>
              <div class="col-md-12" style="height: 15px;">&nbsp;</div>
              <div class="col-md-12">
                
                <div class="form-group  col-xs-5" style="padding: 1px;">
                  <label for="detalle_quiro" class="col-md-12 texto" style="padding: 1px;">Detalle</label>
                  <div class="col-md-12" style="padding: 1px;">
                    <input id="detalle_hipo" name="detalle_hipo" type="text" class="form-control input-sm" value="">
                  </div>
                </div>
                <div class="form-group  col-xs-4" style="padding: 1px;">
                  <label for="cuota_quiro" class="col-md-12 texto" style="padding: 1px;">Cuota</label>
                  <div class="col-md-12" style="padding: 1px;">
                    <input id="cuota_hipo" name="cuota_hipo" type="text" class="form-control input-sm" value="" onkeypress="return isNumberKey(event)" >
                  </div>
                </div>
                <div class="form-group  col-xs-3" style="padding: 1px;">
                  <br>
                  <button type="button" id="btn_hipotecario" class="btn btn-primary size_text" onclick="cargar_hipotecario();">
                    Agregar
                  </button>
                </div>  
              </div>
              <div class= "col-md-12">
                <div class="table-responsive col-md-12" >
                  
                  <table id="example1" role="grid" aria-describedby="example2_info">
                    <thead style="background-color: #FFF3E3">
                      <tr>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Detalle</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Valor Cuota</th>
                        <th style="width: 10%; text-align: center;font-size: 11px;">Acción</th>
                      </tr>
                    </thead>
                    <tbody id="agregar_cuot_hipot">
                      @foreach($cuotas_hipo as $cuota_h)
                        <tr>
                          <td>{{$cuota_h->detalle_cuota}}</td>
                          <td style="text-align: right;">{{$cuota_h->valor_cuota}}</td>
                          <td style="text-align: center;"><a class="btn btn-danger btn-gray delete btn-xs" onclick="eliminar_cuota_hipo('{{$cuota_h->id}}');"><i class="glyphicon glyphicon-trash" aria-hidden="true" ></i></a></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group  col-xs-12">&nbsp;</div>
              <div class="form-group  col-xs-12" id="dato_saldo">
                <label for="saldo_inicial" class="col-md-6 texto">Total Hipotecario:</label>
                <div class="col-md-6">
                  <input id="saldo_inicial" name="saldo_inicial" type="text" class="form-control input-sm" value="{{$detalle_rol->total_quota_hipot}}"   onkeypress="return isNumberKey(event)" readonly>
                </div>
              </div>
          
            </div>
          </div>  
        
          <div class="col-md-6" style="height:30px;">
            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto">TOTAL A PAGAR</label>
                </div>
            </div>
            <!--Monto-->
            
            <div class="form-group  col-xs-12">
              <label for="total_ingresos2" class="col-md-5 texto">Total Ingresos</label>
              <div class="col-md-6">
                <input id="total_ingresos2" name="total_ingresos2" type="text" class="form-control input-sm" value="{{$detalle_rol->total_ingresos}}"  onkeypress="return isNumberKey(event)" readonly>
              </div>
            </div>
            <div class="form-group  col-xs-12">
              <label for="total_egresos" class="col-md-5 texto">Total Egresos</label>
              <div class="col-md-6">
                <input id="total_egresos" name="total_egresos" type="text" class="form-control input-sm" value="{{$detalle_rol->total_egresos}}"  onkeypress="return isNumberKey(event)" readonly>
              </div>
            </div>
            <div class="form-group  col-xs-12">
              <label for="neto_recibido" class="col-md-5 texto">Pagar:</label>
              <div class="col-md-6">
                <input id="neto_recibido" name="neto_recibido" type="text" class="form-control input-sm" value="{{$detalle_rol->neto_recibido}}"  onkeypress="return isNumberKey(event)" readonly>
              </div>
            </div>
            
          </div>
          <div class="separator1"></div>

          <div class="col-md-12" style="height:30px;">
            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto">Forma de Pago</label>
                </div>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group col-md-12 col-xs-12">
               <label class="texto" for="tipo_pago">Tipo de Pago: </label>
            </div>
            <div class="form-group col-md-12 col-xs-12">
              <select class="form-control input-sm" id="tipo_pago" name="tipo_pago" onchange="revisar_seleccion()">
                @foreach($tipo_pago_rol as $value)
                  <option value="{{$value->id}}">{{$value->tipo}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group col-md-12 col-xs-12">
              <label class="texto" for="banco">Banco: </label>
            </div>
            <div class="form-group col-md-12 col-xs-12">
              <select class="form-control input-sm" id="banco" name="banco">
                <option value="">Seleccione...</option>
                @foreach($lista_banco as $value)
                  <option value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div> 
          <div class="col-md-2">
            <div class="form-group col-md-12 col-xs-2">
              <label class="texto" for="numero_cuenta">N# Cuenta: </label>
            </div>
            <div class="form-group col-md-12 col-xs-12">
               <input id="numero_cuenta" type="text" class="form-control input-sm" name="numero_cuenta" value="" onkeypress="return isNumberKey(event)">
            </div>  
          </div>
          <div class="col-md-2">
            <div class="form-group col-md-12 col-xs-12">
              <label class="texto" for="num_cheque">N# cheque: </label>
            </div>
            <div class="form-group col-md-12 col-xs-12">
               <input id="num_cheque" type="text" class="form-control input-sm" name="num_cheque" value="" onkeypress="return isNumberKey(event)">
            </div> 
          </div> 
          <div class="col-md-2">
            <div class="form-group col-md-12 col-xs-12">
              <label class="texto" for="num_cheque">Valor: </label>
            </div>
            <div class="form-group col-md-12 col-xs-12">
               <input id="valor_forma_pago" type="text" class="form-control input-sm" name="valor_forma_pago" value="" onkeypress="return isNumberKey(event)">
            </div> 
          </div>  
          <div class="col-md-2">
            <br>
            <button id="btn_pago" type="button" class="btn btn-success btn-gray" onclick="guardar_forma_pago()">
               <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
            </button>
          </div>  
          <div class="col-md-12 table-responsive ">
            <table id="example1" role="grid" class="table table-bordered table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
              <thead >
                <tr class='well-dark'>
                  <th width="20%" style="text-align: center;">Metodo</th>
                  <th width="20%" style="text-align: center;">Banco</th>
                  <th width="20%" style="text-align: center;">N# Cuenta</th>
                  <th width="20%" style="text-align: center;">N# Cheque</th>
                  <th width="15%" style="text-align: center;">Valor</th>
                </tr>
              </thead>
                <tbody id="agregar_pago">
                @foreach($forma_pago as $pago)
                <tr class='well' >
                  <td width="20%" style="text-align: center;">
                    @if($pago->id_tipo_pago!=null)
                    {{$tipo_pago_rol->find($pago->id_tipo_pago)->tipo}}
                    @endif
                  </td>
                  <td width="20%" style="text-align: center;">
                    @if($pago->banco!=null)
                    {{$lista_banco->find($pago->banco)->nombre}}
                    @endif
                  </td>
                  <td>
                    {{$pago->numero_cuenta}}
                  </td>
                  <td>
                    {{$pago->num_cheque}}
                  </td>
                  <td>
                    {{$pago->valor}}
                  </td>
                  <td>
                    <button type="button" onclick="eliminar_form_pag('{{$pago->id}}')" class="btn btn-danger btn-margin btn-lg"><span class="glyphicon glyphicon-trash"></span></button>
                  </td>
                </tr>
                @endforeach
                </tbody>
            </table>
          </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script type="text/javascript">
    revisar_seleccion();
    function recalcular(){

      var confirmar = confirm("Al cambiar actualizará el rol");
      if(confirmar){
        $.ajax({
          type: 'post',
          url:"{{route('nuevo_rol.update')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $('#actualiza_rol_pago').serialize(),
          success: function(data){
            console.log(data);
            location.reload();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })

      }else{
        location.reload();
      }

    } 
    function cargar_otros_anticipos(){
      $.ajax({
        type: 'get',
        url:"{{route('nuevo_rol.calcular_otros_anticipos',['id_empleado'=>$rol->id])}}",
        datatype: 'json',
        success: function(data){
          $('#div_otros_anticipos').empty().html(data);
          $('#otros_anticipos_modal').modal();
        },
        error: function(data){
          //console.log(data);
        }
      });  
    } 

    function recalcular_sin_confirmar(){

      
        $.ajax({
          type: 'post',
          url:"{{route('nuevo_rol.update')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $('#actualiza_rol_pago').serialize(),
          success: function(data){
            console.log(data);
            location.reload();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })



    } 

    function eliminar_prestamo_rol(id) {
      var confirmar = confirm("Confirma desea eliminar detalle del prestamo");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{url('detalle_prestamos_rol/contable')}}/" + id,
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }    
    }

    
    function eliminar_saldo_ini(id) {
      var confirmar = confirm("Confirma desea eliminar detalle del saldo");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{url('detalle_saldos_rol/contable')}}/" + id,
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }    
    }

    function cargar_saldos_rol(){
      var confirmar = confirm("Confirma desea agregar saldos al rol");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{route('nuevo_rol.recargar_saldo_rol',['id_nomina' => $nomina->id,  'id_rol' => $rol->id])}}",
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }   

    }

    function cargar_prestamos_rol(){
      var confirmar = confirm("Confirma desea agregar prestamos al rol");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{route('nuevo_rol.recargar_prestammo_rol',['id_nomina' => $nomina->id,  'id_rol' => $rol->id])}}",
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }   

    }

    function eliminar_anticipo(id) {
      var confirmar = confirm("Confirma desea eliminar anticipo");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{url('anticipos_rol/contable')}}/" + id,
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }    
    }

    function cargar_anticipos(){
      var confirmar = confirm("Confirma desea agregar anticipos al rol");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{route('nuevo_rol.recargar_anticipo_rol',['id_nomina' => $nomina->id,  'id_rol' => $rol->id])}}",
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }   

    }


    function cargar_quirografario(){
      var confirmar = confirm("Confirma desea agregar prestamo quirografario");
      if(confirmar){
        $.ajax({
          type: 'post',
          url:"{{route('nuevo_rol.cargar_cuota_quirografario',['id_rol' => $rol->id])}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $('#actualiza_rol_pago').serialize(),
          success: function(data){
            console.log(data);
            recalcular_sin_confirmar();
          },
          error: function(data){

          }
        })
      }  
    }

    function eliminar_cuota_quiro(id) {
      var confirmar = confirm("Confirma desea eliminar cuota quirografario");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{url('cuota_rol/quirografario/eliminar/contable')}}/" + id,
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            //recalcular();
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }    
    }

    function cargar_hipotecario(){
      $.ajax({
        type: 'post',
        url:"{{route('nuevo_rol.cargar_cuota_hipotecario',['id_rol' => $rol->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $('#actualiza_rol_pago').serialize(),
        success: function(data){
          console.log(data);
          recalcular_sin_confirmar();
        },
        error: function(data){

        }
      })
    }


    function eliminar_cuota_hipo(id){
      var confirmar = confirm("Confirma desea eliminar cuota hipotecario");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{url('cuota_rol/hipotecario/eliminar/contable')}}/" + id,
          datatype: 'json',
          
          success: function(data){
            console.log(data);
            //recalcular();
            recalcular_sin_confirmar();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
      }

    }

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    } 

    function revisar_seleccion(){
      $('#num_cheque').prop('disabled',false);
      $('#banco').prop('disabled',false);
      $('#numero_cuenta').prop('disabled',false);
      var forma_pago = $('#tipo_pago').val();
      if(forma_pago == '1'){
        $('#num_cheque').prop('disabled','disabled');
      }
      if(forma_pago == '2'){
        $('#num_cheque').prop('disabled','disabled');
        $('#banco').prop('disabled','disabled');
        $('#numero_cuenta').prop('disabled','disabled');
      }       
  
    }

    function guardar_forma_pago(){
      var valor_forma_pago = $('#valor_forma_pago').val();
      if( valor_forma_pago == ''){
        alert("Ingrese valor");
      }else{
        var confirmar = confirm("Desea agregar forma de pago");
        if(confirmar){
          $.ajax({
            type: 'post',
            url:"{{route('nuevo_rol.forma_pago_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $('#actualiza_rol_pago').serialize(),
            success: function(data){
              console.log(data);
              location.reload();
            },
            error: function(data){
              alert("ocurrio un error en el proceso");
            }
          })

        }else{
          location.reload();
        }
      }  

    }

    function guardar(){
      $.ajax({
          type: 'post',
          url:"{{route('nuevo_rol.update_observaciones')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $('#actualiza_rol_pago').serialize(),
          success: function(data){
            console.log(data);
            //location.reload();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })
    }

    function guardar_detalle(element){
      
      var nombre = element.name;
      var id = nombre.substring(4);
      var valor = $('#val_'+id).val();
      var observacion = $('#obs_'+id).val();

      $.ajax({
          type: 'post',
          url:"{{route('nuevo_rol.actualiza_detalle')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: { 'id': id, 'valor': valor, 'observacion': observacion},
          success: function(data){
            if(data.msn == 'ok'){
              $('#total_ingresos').val(data.total_ingresos);
              $('#total_ingresos2').val(data.total_ingresos);
              $('#total_egresos').val(data.total_egresos);
              $('#neto_recibido').val(data.neto_recibido);
            }
            //location.reload();
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })


      console.log(nombre, id, valor, observacion);
    }


    function enviar_correo(){

      $.ajax({
        url:"{{asset('contable/rol/pago/envio/correo/')}}/"+"{{$rol->id}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'GET',
        success: function(data){
            if(data == 'ok'){
              alert("Enviado por Correo");
            }
        },
        error: function(data){
          alert("ocurrio un error en el proceso");
        }
      });
      
    }

    function goBack() {
      window.history.back();
    }

    function aplicar_anticipo( id_anticipo, id_rol)
    {

      $.ajax({
        url:"{{asset('nuevo_rol_anticipos/aplicar')}}/"+ id_anticipo + "/" + id_rol,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'GET',
        success: function(data){
            $('#anticipo'+id_anticipo).text('YA APLICADO');
        },
        error: function(data){
          alert("trans('contableM.ocurerrorproceso')");
        }
      });   

    }

    function actualizar_valores(){
      $.ajax({
        url:"{{asset('mnr_recalcular_db/')}}/"+"{{$rol->id}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'GET',
        success: function(data){
          location.reload(); 
        },
        error: function(data){
          alert("ocurrio un error en el proceso");
        }
      });

    }
    
    $('#otros_anticipos_modal').on('hidden.bs.modal', function(){
        location.reload();
        $(this).removeData('bs.modal');
    });

    function goBack() {
      window.history.back();
    }

</script>

</section>
@endsection
