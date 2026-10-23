@extends('archivo_plano.planilla.base')
@section('action-content')

@php
$tipo_usuario = Auth::user()->id_tipo_usuario;
@endphp
<style type="text/css">

    .control-label{
        padding: 0;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 1px;
        font-size: 14px
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

    .color_texto{
      color:#FFFFFF;
    }

    .head-title{
      background-color: #3c8dbc;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal fade" id="item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 95%;">
    </div>
  </div>
</div>

<div class="modal fade" id="laboratorio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<!--Ventana Modal Item Iess -->
<div class="modal fade" id="upd_item_iess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>  
</div>

<!--Ventana Modal Agrega Detalle Items Iess-->
<div class="modal fade" id="detalle_items_iess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>  
</div>

<div class="modal fade" id="cardiologia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="m_insumos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="m_medicinas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<section class="content">
	<div class="box">
		
	
		
	  	<div class="box-body">
            @php

             $ult_digito = substr($archivo_plano->id_paciente,5,10);
             
             $xedad = Carbon\Carbon::createFromDate(substr($archivo_plano->paciente->fecha_nacimiento, 0, 4), substr($archivo_plano->paciente->fecha_nacimiento, 5, 2), substr($archivo_plano->paciente->fecha_nacimiento, 8, 2))->age; 
             
             if($archivo_plano->paciente->sexo == '1'){
                   $sexo =  'MASCULINO';
             }else{
                
                if($archivo_plano->paciente->sexo == '2'){
                   $sexo =  'FEMENINO';
                }

             }
            
            $fecha_ingreso=substr($archivo_plano->fecha_ing,0 ,10);
            $fecha_ing_inv=date("d-m-Y",strtotime($fecha_ingreso));

            $fecha_alta=substr($archivo_plano->fecha_alt,0 ,10);
            $fecha_alt_inv=date("d-m-Y",strtotime($fecha_alta));

            $fech_inver = date("d/m/Y",strtotime($archivo_plano->paciente->fecha_nacimiento));
            $agenda = $historia_clinica->agenda;

            @endphp
			<form class="form-horizontal" id="form">

			{{ csrf_field() }}
				<div class="row" style="padding-left: 1px;">
    				
                    <input type="hidden" name="id_archivo_plano" value="{{$archivo_plano->id}}">
                    <input  name="nomb_afil" id="nomb_afil" type="text" class="hidden" value="{{$archivo_plano->paciente->apellido1}} {{$archivo_plano->paciente->apellido2}} {{$archivo_plano->paciente->nombre1}} {{$archivo_plano->paciente->nombre2}}">
                    <input  name="parent" id="parent" type="text" class="hidden" value="{{$archivo_plano->parentesco}}">
                    <div class="form-group col-md-1"><br></div>
                    <div class="form-group col-md-8">
                        <h4><b>Beneficiario: {{$archivo_plano->id_paciente}} - {{$archivo_plano->paciente->apellido1}} {{$archivo_plano->paciente->apellido2}} {{$archivo_plano->paciente->nombre1}} {{$archivo_plano->paciente->nombre2}} Edad:  {{$xedad}} Sexo: {{$sexo}} Nacimiento: {{$fech_inver}}</b></h4>
                    </div>
                    <div class="form-group col-md-1">
                        <span style="color:white">{{$archivo_plano->id_hc}}</span>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="col-md-4 control-label">Hc:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$ult_digito}}
                            <!--<input id="hc_iess" maxlength="10" type="text" class="form-control input-sm" name="hc_iess" value="{{$ult_digito}}" readonly>-->
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <a href="{{route('consultam.detalle',['id'=>$historia_clinica->id_agenda])}}" class="btn btn-info btn-xs" target="_blanck" id="log_agenda">
                        <span class="glyphicon glyphicon-folder-open" aria-hidden="true" style="font-size: 12px"> Agenda</span></a>
                    </div>
                    
                        <div class="form-group col-md-4 ">
                            <label for="cedula" class="col-md-4 control-label">Cedula Afiliado:</label>
                            <div class="col-md-7">
                                <input id="cedula" maxlength="15" required type="text" class="form-control input-sm" name="cedula" value="{{$archivo_plano->id_usuario}}">
                            </div>
                        </div>
                  
                  
                        <div class="form-group col-md-4">
                            <label for="nombre" class="col-md-4 control-label">Nombres Afiliado:</label>
                            <div class="col-md-7">
                                <input id="nombre" type="text"  required class="form-control input-sm" name="nombre" value="{{$archivo_plano->nombres}}">
                            </div>
                        </div>
                    
                    
                    
                    <div class="form-group col-md-4 ">
                        <label for="parentesco" class="col-md-4 control-label">Parentesco:</label>
                        <div class="col-md-7">
                            <select id="parentesco" name="parentesco" class="form-control input-sm" >
                                <option @if($archivo_plano->parentesco=='TITULAR') selected @endif value="TITULAR">TITULAR</option>
                                <option @if($archivo_plano->parentesco=='CONYUGE') selected @endif value="CONYUGE">CONYUGE</option>
                                <option @if($archivo_plano->parentesco=='HIJO/HIJA') selected @endif value="HIJO/HIJA">HIJO/HIJA</option>
                                <option @if($archivo_plano->parentesco=='PARIENTE') selected @endif value="PARIENTE">PARIENTE</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="fecha_ing" class="col-md-4 control-label">Fecha Ingreso:</label>
                        <div class="col-md-7">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text"  class="form-control input-sm" id="fecha_ing" name="fecha_ing" placeholder="DD/MM/AAAA" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="fecha_alt" class="col-md-4 control-label">Fecha Alta:</label>
                        <div class="col-md-7">
                            
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text"  class="form-control input-sm" id="fecha_alt" name="fecha_alt" placeholder="DD/MM/AAAA" autocomplete="off">
                            </div>
                        </div>
                    </div>
                   
                    <div class="form-group col-md-4 ">
                        <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                        <div class="col-md-7">
                            <input id="mes_plano" maxlength="10" required type="text" class="form-control input-sm" name="mes_plano" value="{{$archivo_plano->mes_plano}}" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="id_seguro_priv" class="col-md-4 control-label">Seguro Privado:</label>
                        <div class="col-md-7">
                            <select id="id_seguro_priv" name="id_seguro_priv" class="form-control input-sm select2_cuentas" >
                                <option value="">NINGUNO</option>
                                @foreach($seguros as $value)
                                    <option @if($archivo_plano->id_seguro_priv == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="id_cobertura_comp" class="col-md-4 control-label">Cobertura Comp.:</label>
                        <div class="col-md-7">
                            <select id="id_cobertura_comp" name="id_cobertura_comp" class="form-control input-sm" >
                                <option value="">NINGUNO</option>
                                @foreach($seguros_publicos as $value)
                                    @if($value->id == '3' || $value->id == '6')
                                    <option @if($archivo_plano->id_cobertura_comp == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="id_tipo_seguro" class="col-md-4 control-label">Tipo Seguro:</label>
                        <div class="col-md-7">
                            <select id="id_tipo_seguro" name="id_tipo_seguro" class="form-control select2_cuentas">
                                @foreach($tipo_seguros as $value)
                                    <option @if($archivo_plano->id_tipo_seguro == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    <div class="form-group col-md-4 ">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
                        <div class="col-md-7">
                            <select id="id_empresa" name="id_empresa" class="form-control input-sm" onchange="recalculo_valores_items({{$archivo_plano->id}},{{$archivo_plano->id_seguro}});">
                                @foreach($empresas as $value)
                                    @if($value->id == '0992704152001' || $value->id == '1307189140001')
                                    <option @if($archivo_plano->id_empresa == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_cod_deriva" class="col-md-4 control-label">Cod. Derivación:</label>
                        <div class="col-md-7">
                            <select id="id_cod_deriva" name="id_cod_deriva" class="form-control input-sm select2_cuentas" >
                            <option value="" >seleccione...</option>       
                            @foreach($codigos_dev as $value)
                            <option @if($archivo_plano->id_cod_deriva == $value->id) selected @endif value="{{$value->id}}">{{$value->codigo}} - {{$value->nombre}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                   
                   
                    
                    <div class="form-group col-md-4 ">
                        <label for="id_cod_dep" class="col-md-4 control-label">Cod. Dependencia:</label>
                        <div class="col-md-7">
                            <select id="id_cod_dep" name="id_cod_dep" class="form-control input-sm select2_cuentas" >
                            <option value="" >seleccione...</option>  
                            @foreach($codigos_dep as $value)
                            <option @if($archivo_plano->id_cod_dep == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre_cod}}</option>
                          @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-12 "></div>
                    <div class="form-group col-md-8">
                        <label for="cie10" class="col-md-2 control-label">Diagnostico Prin.:</label>
                        <div class="col-md-10">
                            <input type="hidden" name="codigo" id="codigo" value="{{$archivo_plano->cie10}}">
                            <input id="cie10" type="text" required class="form-control input-sm" name="cie10" placeholder="Diagnóstico" value="{{$txt_cie10}}">
                        </div>
                    </div>
                 
                    <div class="form-group col-md-4 ">
                        <label for="presuntivo_def" class="col-md-4 control-label">Presuntivo/Definitivo:</label>
                        <div class="col-md-7">
                            <select id="presuntivo_def" class="form-control input-sm" name="presuntivo_def"  >
                                <option @if($archivo_plano->presuntivo_def=='Definitivo') selected @endif value="Definitivo">Definitivo</option>
                                <option @if($archivo_plano->presuntivo_def=='Presuntivo') selected @endif value="Presuntivo">Presuntivo</option>
                            </select>    
                        </div>
                    </div>
                  
                    <div class="form-group col-md-8 ">
                        <label for="nom_procedimiento" class="col-md-2 control-label">Procedimiento:</label>
                        <div class="col-md-9">
                            <input id="nom_procedimiento" type="text" class="form-control input-sm" name="nom_procedimiento"  value="{{$archivo_plano->nom_procedimiento}}" >
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="estado" class="col-md-4 control-label">Activo/Inactivo:</label>
                        <div class="col-md-7">
                            <select id="estado" class="form-control input-sm" name="estado"  >
                                <option @if($archivo_plano->estado=='1') selected @endif value="1">Activo</option>
                                <option @if($archivo_plano->estado=='0') selected @endif value="0">Inactivo</option>
                            </select>    
                        </div>
                    </div>
                    <div class="form-group col-md-6 ">
                        <label for="code_derivacion" class="col-md-2 control-label">Codigo Derivación:</label>
                        <div class="col-md-9">
                            <input id="code_derivacion" type="text" class="form-control input-sm" name="code_derivacion"  value="@if($archivo_plano->codigo_iess == null){{$historia_clinica->agenda->cod_val}}@else{{$archivo_plano->codigo_iess}}@endif" >
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_autorizacion" class="col-md-4 control-label">Autorización:</label>
                        <div class="col-md-8">
                            <input id="fecha_autorizacion" type="text" class="form-control input-sm" name="fecha_autorizacion"  value="{{ $archivo_plano->fecha_autorizacion}}" >
                        </div>
                    </div>
                    <div class="form-group col-md-2 ">
                        <label for="carpeta" class="col-md-4 control-label">No. Atención:</label>
                        <div class="col-md-7">
                            <input id="carpeta" type="text" class="form-control input-sm" name="carpeta"  value="{{ $archivo_plano->atencion }}" readonly>
                        </div>
                    </div>
                </div>
                @if($tipo_usuario == '1' && $archivo_plano->id_empresa == '0992704152001')
                    <div class="form-group col-md-4" style="margin-top: 5px;">
                        <label for="doctor_archivo" class="col-md-4 control-label">Doctor:</label>
                        <div class="col-md-7">
                            <select id="doctor_archivo" name="doctor_archivo" class="form-control input-sm select2_doctor">
                                @foreach($doctores as $value)
                                    <option value="{{$value->id}}" @if($value->id == $archivo_plano->id_doctor) selected @endif>{{$value->nombre1}} {{$value->apellido1}} {{$value->apellido2}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
			</form>

            
            <br>
            <div class="row">
                <div class="col-md-10" align="center">
                    <a id="items" class="btn btn-success btn-xs oculto" data-remote="{{route('planilla_item.iess',['cabecera' => $archivo_plano->id])}}" data-toggle="modal" data-target="#item" ><span class="glyphicon glyphicon-file"> Item</span> </a>
                    <a id="item1" class="btn btn-success btn-xs" ><span class="glyphicon glyphicon-file"> Item</span> </a>
                    <!--<a id="item" class="btn btn-success" data-remote="{{route('planilla_item.iess',['cabecera' => $archivo_plano->id])}}" data-toggle="modal" data-target="#item" ><span class="glyphicon glyphicon-file"> Item</span> </a>-->
                
                    <a id="procedimientos" class="btn btn-success btn-xs oculto"  data-remote="{{route('lista_item_modal.iess', ['cabecera' =>$archivo_plano->id])}}"  data-toggle="modal" data-target="#detalle_items_iess"><span class="glyphicon glyphicon-plus"> Procedimiento</span> </a>
                    <a id="procedimiento1" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"> Procedimiento</span> </a>
                
                <!--<div class="col-md-2">
                    <a class="btn btn-success" href="" data-toggle="modal" data-target="#mdetalle" >Procedimiento</a>
                </div>-->
                
                    <a id="lab" class="btn btn-success btn-xs oculto" href="{{route('planilla.busca_ordenes_labs',['cabecera' => $archivo_plano->id ])}}" data-toggle="modal" data-target="#laboratorio" > <span class="glyphicon glyphicon-plus"> Laboratorio </span></a>
                    <a id="laboratorio1" class="btn btn-success btn-xs"> <span class="glyphicon glyphicon-plus"> Laboratorio </span></a>
                
                
                    <!--<a id="validar_planilla" class="btn btn-success btn-xs oculto" href="{{route('archivo_plano.planilla_individual',['hcid' => $archivo_plano->id])}}"><span class="glyphicon glyphicon-download-alt"></span> Planilla individual</a>
                    <a id="planilla1" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-download-alt"></span>Planilla individual</a>-->
                    <a id="planilla1" class="btn btn-success btn-xs oculto" href="{{route('archivo_plano.planilla_individual',['hcid' => $archivo_plano->id])}}"><span class="glyphicon glyphicon-download-alt"></span> Planilla individual</a>
                    <a id="planilla" class="btn btn-success btn-xs" onclick="verifica_existe_data({{$archivo_plano->id}})"><span class="glyphicon glyphicon-download-alt"></span> Planilla individual</a>
               
                    <a id="planilla" class="btn btn-success btn-xs" href="{{route('ap_planilladetalle.planilla_detalle_pdf',['id'=>$archivo_plano->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> PDF Planilla individual</a>
                    <a id="cardio" class="btn btn-success btn-xs oculto" href="{{route('planilla.cardiologia',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#cardiologia" > <span > Interconsulta </span></a>
                    <a id="cardio1" class="btn btn-success btn-xs"> <span>Interconsulta</span></a>
                    <a id="insumo" class="btn btn-success btn-xs oculto" href="{{route('nueva_planilla.cargarInsumosPlanillaModal',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#m_insumos" > <span> Insumos </span></a>
                    <a id="insumo1" class="btn btn-success btn-xs"> <span>Insumos</span></a>
                    <a id="medicina" class="btn btn-success btn-xs oculto" href="{{route('nueva_planilla.medicinas',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#m_medicinas" > <span> Medicinas </span></a>
                    <a id="medicina1" class="btn btn-success btn-xs"> <span>Medicinas</span></a>
                
                    <button type="button" class="btn btn-primary btn-xs" onclick="guardar()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                
                    <button type="button" class="btn btn-danger btn-xs" onclick="elimina_todo_items_iess()"><span>Eliminar Todo</span> </button>
                    
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('30')"><span>I.Fungibles 30</span> </button>
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('60')"><span>I.Fungibles 60</span> </button>
                    
                    @if(!is_null($archivo_plano->numero_paciente))
                    <a id="planilla" class="btn btn-warning btn-xs" href="{{route('ap_planilladetalle.nuevo_planilla_detalle_pdf',['numero_paciente' => $archivo_plano->numero_paciente,'id_empresa' => $archivo_plano->id_empresa,'id_seguro'  => $archivo_plano->id_seguro, 'mes_plano' =>$archivo_plano->mes_plano ])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>  Nueva PDF Planilla individual</a>
                    @endif
                    <a href="{{route('nuevoArchivoPlano.index_log_auditoria',['cabecera'=>$archivo_plano->id])}}" class="btn btn-info btn-xs" target="_blanck" id="log_auditoria">
                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true" style="font-size: 16px">Log Auditoria</span></a>
                    
                    @if($archivo_plano->cierre == '0')
                    <button type="button" class="btn btn-danger btn-xs" onclick="cierra_liquidacion()"><span>Cerrar Liquidación</span> </button>
                    @else
                    <button type="button" class="btn btn-danger btn-xs" onclick=""><span>Liquidación Cerrada !!!</span> </button>
                    @endif
                    @if($tipo_usuario == '1')
                    <a id="planilla_descargo" class="btn btn-success btn-xs" href="{{route('ap_planilladetalle.planilla_descargo_pdf',['id'=>$archivo_plano->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Informe de Descargo</a>
                    @endif
                </div>
            </div>
            <br>

            
            <form class="form-horizontal" id="guardar_item">
            
                {{ csrf_field() }}
                <input type="hidden" name="id_ap" value="{{$archivo_plano->id}}" id="id_ap"/>
                <input type="hidden" name="id_nivel" value="{{$archivo_plano->id_nivel}}" id="id_nivel"/>
                <input type="hidden" name="v_porcent_10" id="v_porcent_10" />
                <input type="hidden" name="v_iva" id="v_iva" />
                <input type="hidden" name="vt_val_tmp_anest" id="vt_val_tmp_anest" />
                <input type="hidden" name="v_tipo" id="v_tipo" />

                <div class="row" style="margin-left: 10px;margin-right: 10px;padding-top: 10px;background-color: #CEE8FC;">
                    <div class="form-group col-md-2">
                        <label for="v_codigo" class="col-md-2 control-label">Código:</label>
                        <div class="col-md-10">
                            <input id="v_codigo" maxlength="40" type="text" class="form-control input-sm" name="v_codigo" autocomplete="off">
                        </div>
                    </div>
                
                    <div class="form-group col-md-10">
                        <label for="v_descripcion" class="col-md-1 control-label">Descripción:</label>
                        <div class="col-md-11">
                            <input id="v_descripcion" type="text" class="form-control input-sm"  name="v_descripcion" value="{{old('v_descripcion')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase(); " autocomplete="on">
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="v_fecha" class="col-md-2 control-label">Fecha:</label>
                        <div class="col-md-10">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text"  class="form-control input-sm" id="v_fecha" name="v_fecha" value="{{ old('v_fecha') }}" autocomplete="off">
                                <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('v_fecha').value = '';"></i>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="v_cantidad" class="col-md-4 control-label">Cantidad:</label>
                        <div class="col-md-8">
                            <input id="v_cantidad" type="text" class="form-control input-sm " name="v_cantidad" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="v_precio" class="col-md-4 control-label">Precio:</label>
                        <div class="col-md-8">
                            <input id="v_precio" type="text" class="form-control input-sm" name="v_precio" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="v_porcent_honorario" class="col-md-4 control-label">Honorario:</label>
                        <div class="col-md-8">
                            <select id="v_porcent_honorario" name="v_porcent_honorario" class="form-control input-sm" >
                            <option value="100">100 %</option>
                            <option value="50">50 %</option>
                            </select>
                        </div>
                    </div> 
                
                    <div class="form-group col-md-2">
                        <label for="v_hono_Anest" class="col-md-4 control-label">Honorario<br>Anestesiologo:</label>
                        <div class="col-md-8">
                            <input id="v_hono_Anest" type="text" class="form-control input-sm" name="v_hono_Anest" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="v_tiempo_Anest" class="col-md-4 control-label">Tiempo<br>Anestesia:</label>
                        <div class="col-md-8">
                            <input id="v_tiempo_Anest" type="text" class="form-control input-sm" name="v_tiempo_Anest" value="0" autocomplete="off">
                        </div>
                    </div> 
                    <div class="form-group col-md-3">
                        <label for="v_modificador2" class="col-md-4 control-label">Modificador 2%:</label>
                        <div class="col-md-8">
                            <input type="checkbox" id="v_modificador2" name="v_modificador2" value="1">
                        </div>
                    </div> 

                    <!--div class="form-group col-md-3">
                        <label for="v_modificador" class="col-md-4 control-label">Modificador 5%:</label>
                        <div class="col-md-8">
                            <input type="checkbox" id="v_modificador" name="v_modificador" value="1">
                        </div>
                    </div> 
                    
                    <div class="form-group col-md-3">
                        <label for="v_ayudantia10" class="col-md-4 control-label">Ayudantia 10%:</label>
                        <div class="col-md-8">
                            <input type="checkbox" id="v_ayudantia10" name="v_ayudantia10" value="1">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="v_ayudantia20" class="col-md-4 control-label">Ayudantia 20%:</label>
                        <div class="col-md-8">
                            <input type="checkbox" id="v_ayudantia20" name="v_ayudantia20" value="1">
                        </div>
                    </div-->
                    
                    <div class="form-group col-md-1">
                        <a class="btn btn-light" onclick="vt_crea_detalle_items()">
                            <h1 style="font-size: 12px; margin:0;">
                                <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/agregar_item.png">
                            </h1> 
                        </a>
                    </div>
                </div>
            </form>
           
            <div class="col-md-12" id="detalle" style="padding-left: 2px;"></div>
	  	    </div>
            </div>
	</div>
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">

    $(document).ready(function(){

      
     
      
      
      var parentesc = $('#parent').val();

      if(parentesc == 'TITULAR'){

        var nomb_afi = $('#nomb_afil').val();

        $('#nombre').val(nomb_afi);

      }

      $('#id_tipo_seguro').select2().trigger('change');

      $('#id_seguro_priv').select2().trigger('change');

      $('#id_cod_deriva').select2().trigger('change');

      $('#id_cod_dep').select2().trigger('change');


    });
    
    $('#laboratorio').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    }); 

    $('#item').on('hidden.bs.modal', function(){
      //location.reload();
      $(this).removeData('bs.modal');
    });
    $('#upd_item_iess').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    });

    $('#detalle_items_iess').on('hidden.bs.modal', function(){
        //location.reload();
        $(this).removeData('bs.modal');
    });

   
    
    function vt_crea_detalle_items(){
        
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var v_codigo = $('#v_codigo').val(); 
        var v_descripcion = $('#v_descripcion').val(); 
        var v_fecha = $('#v_fecha').val();
        var v_cantidad = $('#v_cantidad').val();
        var v_precio = $('#v_precio').val();
        var v_porcent_honorario = $('#v_porcent_honorario').val();
        var v_hono_Anest = $('#v_hono_Anest').val();
        var v_tiempo_Anest = $('#v_tiempo_Anest').val();

        var texto = "";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }

        if( v_codigo  == ""){
            texto+="Por favor ingrese el Codigo.<br>";

        }
        if( v_descripcion == ""){
            texto+="Por favor ingrese la descripcion.<br>";

        }
        if( v_fecha == ""){
            texto+="Por favor ingrese la fecha.<br>";

        }
        if( v_cantidad == ""){
            texto+="Por favor ingrese la cantidad.<br>";

        }
        if( v_precio == ""){
            texto+="Por favor ingrese el precio.<br>";

        }
        if( v_porcent_honorario == ""){
            texto+="Por favor ingrese el % Honorario.<br>";

        }
        if( v_hono_Anest == ""){
            texto+="Por favor ingrese el Honorario.<br>";

        }
        if( v_tiempo_Anest == ""){
            texto+="Por favor ingrese el tiempo de anestesia.<br>";

        }

        if(procedimiento==""){
            texto+="Por favor ingrese el Nombre del Procedimiento.<br>";
        }
        
        if(texto != ''){
        
        swal("Error!",texto,"error");
            
        }

        if(texto==''){ 
            
            $.ajax({
                type: 'post',
                url:"{{ route('nuevoArchivoPlano.guardarItem') }}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#guardar_item").serialize(),
                    success: function(data){
                        mostrar_detalle();
                        $("#v_tipo").val('');
                        $("#v_descripcion").val('');
                        $("#v_cantidad").val('');
                        $("#v_iva").val('');
                        $("#v_porcent_10").val('');
                        $("#v_codigo").val('');
                        $("#v_precio").val('0');
                        $("#v_hono_Anest").val('0');
                        $("#v_tiempo_Anest").val('0');
                        $( "#v_modificador" ).prop( "checked", false );
                        $( "#v_ayudantia10" ).prop( "checked", false );
                        $( "#v_ayudantia20" ).prop( "checked", false );
                    },
                    error: function(data){
                        console.log(data);
                    }
            });
        }

    }

    function verifica_existe_data(id_cabecera){
        
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = "";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        if(procedimiento==""){
            texto+="Por favor ingrese el Nombre del Procedimiento.<br>";
        }
        if(texto != ''){
        
        swal("Error!",texto,"error");
            
        }

        if(texto==''){ 
            
            $.ajax({
            type: 'post',
            url:"{{ route('verifica_planilla.individual') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_cab': id_cabecera},
                success: function(data){
                    //console.log(data);
                    if(data == "existe"){

                        window.location = $('#planilla1').attr('href');
                        
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });
        }

    }

    
    $('#item1').click(function(){
        //alert('entra');
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        //var procedimiento =$('#nom_procedimiento').val();

        var texto = "";
        if(cedula==""){
            texto+= "Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+= "Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+= "Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+= "Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+= "Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+= "Por favor ingrese el Mes Plano.<br>";
        }
        /*if(procedimiento==""){
            texto+= "Por favor ingrese el Procedimiento.<br>";
        }*/
        if(texto != ''){
           
            swal("Error!",texto,"error");
             
        }
        if (texto=='') { 
          $('#items').click();
          
        }

    });
    

    $('#procedimiento1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        //var procedimiento =$('#nom_procedimiento').val();

        var texto = "";
        if(cedula==""){
            texto+= "Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        /*if(procedimiento==""){
            texto+="Por favor ingrese el Procedimiento.<br>";
        }*/
        if(texto != ''){
           
           swal("Error!",texto,"error");
            
        }
        if (texto=='') { 
         $('#procedimientos').click();
        
        }
    });

    $('#laboratorio1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        //var procedimiento =$('#nom_procedimiento').val();

        var texto ="";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        /*if(procedimiento==""){
            texto+="Por favor ingrese el Procedimiento.<br>";
        }*/
        if(texto != ''){
           
           swal("Error!",texto,"error");
            
        }
        if (texto=='') { 
         $('#lab').click();
         //guardar();
        }
    });

    $('#cardio1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        //var procedimiento =$('#nom_procedimiento').val();

        var texto ="";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        /*if(procedimiento==""){
            texto+="Por favor ingrese el Procedimiento.<br>";
        }*/
        if(texto != ''){
           
           swal("Error!",texto,"error");
            
        }
        if (texto=='') { 
          $('#cardio').click();
          //guardar();
        }
    });

    $('#planilla1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        //var procedimiento =$('#nom_procedimiento').val();

        var texto = "";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        /*if(procedimiento==""){
            texto+="Por favor ingrese el Procedimiento.<br>";
        }*/
        if(texto != ''){
           
           swal("Error!",texto,"error");
            
        }
        if (texto=='') { 
          $('#planilla').click();
          //guardar();
        }
    });
    

    function seleccionar_orden(orden,cabecera){
        //alert(orden);
        $.ajax({
          type: 'get',
          url:"{{ url('archivo_plano/planilla/detalle/laboratorio/ingresar') }}/" + orden + "/" + cabecera,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
           //console.log(data);
           location.reload();
          },
          error: function(data){
             console.log(data);
          }
        });

    } 

    function seleccionar_cardio(hc_cardio,cabecera,seguro,empresa,agenda){
        //alert("ingreso");
        $.ajax({
          type: 'get',
          url:"{{ url('archivo_planoc/planilla/ingresar_cardio') }}/" + hc_cardio + "/" + cabecera + "/" + seguro + "/" + empresa + "/" + agenda,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
           location.reload();
           console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });

    } 

    function obtener_seleccion(){
        
        var descripcion = $("#nom_plantilla").val();

        document.ready = document.getElementById("nom_procedimiento").value = descripcion;

    }

    function mostrar_detalle(){
        $.ajax({
          type: 'get',
          url:"{{ route('planilla.mostrar_detalle',[ 'cabecera' => $archivo_plano->id,'id_seguro' => $archivo_plano->id_seguro]) }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
            $('#detalle').empty().html(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }  

    function recalculo_valores_items(idcabecera,idseguro){

        var id_empresa = $('#id_empresa').val();
    
        $.ajax({
          type: 'get',
          url:"{{ url('archivo_plano/planilla/calcular_nuevos/valores/items') }}/" + idcabecera + "/" + idseguro+ "/" + id_empresa,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
            //console.log(data);
            if(data.cont_detalle > 0){
                location.reload();
            }    
        },
          error: function(data){
             console.log(data);
          }
        });

    }
    
    
    function guardar(){

        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();

        inicio_mes = 3,
        fin_mes    = 5,
        inicio_anio = 6,
        fin_anio    = 10,
        subcad_mes = fecha_ingreso.substring(inicio_mes,fin_mes);
        subcad_anio = fecha_ingreso.substring(inicio_anio,fin_anio);
        conca_cad = subcad_mes.concat(subcad_anio);
        
        //var procedimiento =$('#nom_procedimiento').val();
        var texto = "";
        
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        if(conca_cad != mesplano){
            texto+="La Fecha de Ingreso debe coincidir con el Mes de Plano.<br>";
        }

        if(texto != ''){
           swal("Error!",texto,"error");
        }

        if(texto==''){ 
            $.ajax({
            type: 'post',
            url:"{{ route('planilla.guardar') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
                //console.log(data);
                if(data == "existe"){
                    swal({
                        title: "No se puede ingresar planilla individual IESS debido a que el ciclo ya fue contabilizado.",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })
                    
                };
                if(data == "ok"){
                    swal({
                        title: "Datos Guardados",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })
                    
                };
            },
            error: function(data){
                console.log(data);
            }
            });
        
        }
    
    
    }

    function elimina_todo_items_iess(){

        if (confirm('¿Desea Eliminar Todos los Items?')) {
            $.ajax({
            type: 'post',
            url:"{{ route('delete.todo_items_iess') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
                //console.log(data);
                if(data == "ok"){
                    swal({
                        title: "Items Eliminados",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })
                    location.reload();
                };
                if(data == "err"){
                    swal({
                        title: "Liquidación Cerrada, No se puede Eliminar registros",
                        icon: "error",
                        type: 'error',
                        buttons: true,
                    })
                }; 
                
            },
            error: function(data){
                console.log(data);
            }
            });
        }else{
          location.reload();
        }
    
    }

    
    function cierra_liquidacion(){

        Swal.fire({
            title: "¿Desea Cerrar la Liquidación?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Continuar",
            denyButtonText: "Cancelar",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url:"{{ route('nuevoarchivoplano.cerrar_liquidacion',['id' => $archivo_plano->id]) }}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: {'funcion': 'NUEVA FUNCION DE LIQUIDACION'},
                    success: function(data){
                        //console.log(data);
                        if(data == "ok"){
                            swal({
                                title: "Liquidacion Cerrada",
                                icon: "success",
                                type: 'success',
                                buttons: true,
                            })
                            
                        };
                        location.reload();
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
                
            } else if (result.isDenied) {
                //Swal.fire("Changes are not saved", "", "info");
            }
        });

        /*if (confirm('¿Desea Cerrar la Liquidación?')) {
            
        }else{
        location.reload();
        }*/

    }

    $(function () {

        $('.select2_doctor').select2({
            tags: false
        });

        mostrar_detalle();
        
        $('#fecha_nac').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

        $('#fecha_ing').datetimepicker({
            useCurrent: false,
            //format: 'DD/MM/YYYY',
            format: 'DD/MM/YYYY',
            @if($archivo_plano->fecha_ing !=null)
              defaultDate: '{{$archivo_plano->fecha_ing}}',
	        @endif
            
        });


        $('#fecha_alt').datetimepicker({
            useCurrent: false,
            //format: 'DD/MM/YYYY',
            format: 'DD/MM/YYYY',
            @if($archivo_plano->fecha_alt !=null)
	          defaultDate:'{{$archivo_plano->fecha_alt}}',
	        @endif
            
	    });

        $('#v_fecha').datetimepicker({
            useCurrent: false,
            //format: 'DD/MM/YYYY',
            format: 'DD/MM/YYYY',
            @if($archivo_plano->fecha_alt !=null)
	          defaultDate:'{{$archivo_plano->fecha_ing}}',
	        @endif
            
	    });

        
    });

    $("#cie10").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('epicrisis.cie10_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    console.log(data);

                }
            })
        },
        minLength: 3,
        select: function(data, ui){
            
            $('#codigo').val(ui.item.id);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    } );

    $("#cie10").change( function(){
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
    });

    $("#v_codigo").autocomplete({
      
      source: function(request,response){
        var nivel_conve = '{{$archivo_plano->id_nivel}}';
        var term = request.term;
        $.ajax({
          url:"{{route('item_iess.buscarxcodigo')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {'term': term,'conv': nivel_conve},
          dataType: "json",
          type: 'post',
          success: function(data){
            response(data);
          }

        });

      },
      select:function(event, ui){
        if(ui.item!==null){
            console.log(ui);
            $("#v_tipo").val(ui.item.tipo);
            $("#v_descripcion").val(ui.item.descripcion);
            $("#v_cantidad").val(ui.item.cantidad);
            $("#v_iva").val(ui.item.iva);
            $("#v_porcent_10").val(ui.item.porcent10);
            obtener_data(ui.item.id_ap_proced,ui.item.tipo);
        }
        
      },
      minLength: 2,
      //Linea nueva, agrego el id del modal
    });

    function obtener_data(id_ap_proced,tip){

        var nivel_conve= '{{$archivo_plano->id_nivel}}';
        //console.log(id_ap_proced,nivel_conve,tip);
        $.ajax({
            type: "GET",
            url: "{{url('archivo_plano/planilla/obtener/precio/item')}}/"+id_ap_proced+ "/"+ nivel_conve+ "/"+ tip,
            dataType: "json",
            success: function(data){
                //console.log(data);
                $('#v_precio').val(data.precio);
                $('#v_hono_Anest').val(data.hono_anast);
                $("#vt_val_tmp_anest").val(data.val_tiemp_anest);
            },
            error:  function(){
                alert('error al cargar');
            }
        });


    }

    $('#medicina1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();

        var texto ="";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        
        if(texto != ''){
           
           swal("Error!",texto,"error");
            
        }
        if (texto=='') { 
          $('#medicina').click();
        }
    });

    $('#insumo1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();

        var texto ="";
        if(cedula==""){
            texto+="Por favor ingrese Cédula del Afiliado.<br>";
        }
        if(nombres==""){
            texto+="Por favor ingrese Nombres del Afiliado.<br>";
        }
        if(fecha_ingreso==""){
            texto+="Por favor Seleccione la Fecha Ingreso.<br>";
        }
        if(fecha_alta==""){
            texto+="Por favor Seleccione la Fecha Alta.<br>";
        }
        if(diagnostico==""){
            texto+="Por favor ingrese el Diagnostico Principal.<br>";
        }
        if(mesplano==""){
            texto+="Por favor ingrese el Mes Plano.<br>";
        }
        
        if(texto != ''){
           
           swal("Error!",texto,"error");
            
        }
        if (texto=='') { 
          $('#insumo').click();
        }
    });

    function carga_fungibles(tipo){

        $.ajax({
        type: 'get',
        url:"{{ url('agrega_insumos_fungibles') }}/" + {{ $archivo_plano->id }} + "/" + tipo, 
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        success: function(data){
            
            //console.log(data);
            if(data == "ok"){
                swal({
                    title: "Items Agregados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })
                location.reload();
            };
            if(data == "err"){
                swal({
                    title: "Liquidación Cerrada, No se puede Agregar registros",
                    icon: "error",
                    type: 'error',
                    buttons: true,
                })
            }; 
            
        },
        error: function(data){
            console.log(data);
        }
        });

    }


</script>





@endsection
