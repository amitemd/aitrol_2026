@extends('archivo_plano.planilla.base_msp')
@section('action-content')
<style type="text/css">

    .control-label{
        padding: 0;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 1px;
        font-size: 14px;
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

<div class="modal fade" id="item_msp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

<!--Ventana Modal Item MSP -->
<!--<div class="modal fade" id="upd_item_msp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>  
</div>-->

<!--Ventana Modal Agrega Detalle Items Msp-->
<div class="modal fade" id="detalle_items_msp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>  
</div>
<!--<div class="modal fade" id="mdetalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <iframe id="dito" class="embed-responsive-item" src="http://192.168.75.125/sis_medico_prb/public/administracion/procedimientos/lista?cabecera={{$archivo_plano->id}}" allowfullscreen style="width:100%; height:500px"></iframe>
    </div>
  </div>
</div>-->
<div class="modal fade" id="cardiologia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<section class="content">
    @php
        $diagnostico_pre = $diagn_ingre[0] ?? null;
    @endphp
    <input type="hidden" id="cie_10_pre" value="{{ $diagnostico_pre ? '(' . trim($diagnostico_pre['cie10']) . ') ' . trim($diagnostico_pre['descripcion']) : '' }}">
	<div class="box">
		<div class="box-body">
            @php
                $ult_digito = substr($archivo_plano->id_paciente,5,10);
              
                $xedad = Carbon\Carbon::createFromDate(substr($archivo_plano->paciente->fecha_nacimiento, 0, 4), substr($archivo_plano->paciente->fecha_nacimiento, 5, 2), substr($archivo_plano->paciente->fecha_nacimiento, 8, 2))->age; 
             
                if($archivo_plano->paciente->sexo == '1'){
                    $sexo =  'M';
                }else{
                    
                    if($archivo_plano->paciente->sexo == '2'){
                    $sexo =  'F';
                    }

                }

                $fecha_ingreso=substr($archivo_plano->fecha_ing,0 ,10);
                $fecha_ing_inv=date("d-m-Y",strtotime($fecha_ingreso));

                $fecha_alta=substr($archivo_plano->fecha_alt,0 ,10);
                $fecha_alt_inv=date("d-m-Y",strtotime($fecha_alta));

                $fech_inver = date("d/m/Y",strtotime($archivo_plano->paciente->fecha_nacimiento));
            @endphp
            <form class="form-horizontal" id="form">
			{{ csrf_field() }}
                <div class="row" style="padding-left: 1px;">
                    <!--<div class="col-md-12">
    	                <h4 for="cedula_ben"><b>Beneficiario: {{$archivo_plano->id_paciente}} - {{$archivo_plano->paciente->apellido1}} {{$archivo_plano->paciente->apellido2}} {{$archivo_plano->paciente->nombre1}} {{$archivo_plano->paciente->nombre2}}</b>
    	                </h4>
                    </div>-->
                    <input type="hidden" name="id_archivo_plano" value="{{$archivo_plano->id}}">
                    <div class="form-group col-md-4">
                        <label for="id_beneficiario" class="col-md-4 control-label">id.Beneficiario:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$archivo_plano->id_paciente}}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="hc_iess" class="col-md-4 control-label">&nbsp</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="hc_iess" class="col-md-4 control-label">&nbsp</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nombre_beneficiario" class="col-md-4 control-label">Nombres Benef:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                           {{$archivo_plano->paciente->nombre1}} {{$archivo_plano->paciente->nombre2}}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="apellido_beneficiario" class="col-md-4 control-label">Apellidos Benef:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                           {{$archivo_plano->paciente->apellido1}} {{$archivo_plano->paciente->apellido2}}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="edad" class="col-md-4 control-label">Edad:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$xedad}}
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="hc_iess" class="col-md-4 control-label">Hc:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$ult_digito}}
                            <!--<input id="hc_iess" maxlength="10" type="text" class="form-control input-sm" name="hc_iess" value="{{$ult_digito}}" readonly>-->
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="sexo" class="col-md-4 control-label">Sexo:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$sexo}}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$fech_inver}}
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="cedula" class="col-md-4 control-label">Cedula Afiliado:</label>
                        <div class="col-md-7">
                            <input id="cedula" maxlength="10" type="text" class="form-control input-sm" name="cedula" value="{{$archivo_plano->id_paciente}}">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nombre" class="col-md-4 control-label">Nombre Afiliado:</label>
                        <div class="col-md-7">
                            @if(!is_null($archivo_plano->nombres))
                               <input id="nombre" type="text" class="form-control input-sm" name="nombre" value="@if(!is_null($archivo_plano->nombres)){{$archivo_plano->nombres}}@endif">
                            @else
                                <input id="nombre" type="text" class="form-control input-sm" name="nombre" 
                                value="@if(!is_null($archivo_plano->usuario)){{$archivo_plano->usuario->apellido1}} {{$archivo_plano->usuario->apellido2}} {{$archivo_plano->usuario->nombre1}} {{$archivo_plano->usuario->nombre2}}@endif">
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-8 ">
                        <label for="cie10" class="col-md-2 control-label">Diagnostico Prin.:</label>
                        <div class="col-md-9">
                            <input type="hidden" name="codigo" id="codigo" value="{{$archivo_plano->cie10}}">
                            <input id="cie10" type="text" class="form-control input-sm"  name="cie10" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  placeholder="Diagnóstico" value="{{$txt_cie10}}">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="presuntivo_def" class="col-md-4 control-label">Presuntivo/Definitivo:</label>
                        <div class="col-md-7">
                            <select id="presuntivo_def" class="form-control input-sm" name="presuntivo_def"  >
                                <option @if($archivo_plano->presuntivo_def=='Definitivo') selected @endif value="Definitivo">Definitivo</option>
                                <option @if($archivo_plano->presuntivo_def=='Presuntivo') selected @endif value="Presuntivo">Presuntivo</option>
                            </select>    
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
                        <div class="col-md-7">
                            <select id="id_empresa" name="id_empresa" class="form-control input-sm" onchange="recalculo_valores_items_msp({{$archivo_plano->id}},{{$archivo_plano->id_seguro}});">
                               
                                @foreach($empresas as $value)
                                  @if($value->id == session("id_empresa"))
                                   <option @if($archivo_plano->id_empresa == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                  @endif
                                @endforeach
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
                                <input type="text"  class="form-control input-sm" id="fecha_ing" name="fecha_ing" placeholder="DD/MM/AAAA"  >
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
                                <input type="text"  class="form-control input-sm" id="fecha_alt" name="fecha_alt" placeholder="DD/MM/AAAA"  >
                            </div>
                        </div>
                    </div>
                    <!--<div class="form-group col-md-4 ">
                        <label for="nom_planilla" class="col-md-4 control-label">Nombre de Plantilla:</label>
                        <div class="col-md-7">
                            <input id="nom_planilla" maxlength="40" type="text" class="form-control input-sm" name="nom_planilla"  value="{{$archivo_plano->nom_planilla}}" >
                        </div>
                    </div>-->
                    <div class="form-group col-md-6">
                        <label for="derivacion_cv" class="col-md-3 control-label">Cod. Derivación MSP:</label>
                        <div class="col-md-2">
                          <input id="derivacion_cv" type="text" class="form-control input-sm" name="derivacion_cv" value="{{$archivo_plano->derivacion_cv_msp ?? ($agenda->validacion_cv_msp ?? '')}}" placeholder="CV" required>
                        </div>
                        <div class="col-md-2">
                          <input id="derivacion_num_caso" type="text" class="form-control input-sm" name="derivacion_num_caso" value="{{$archivo_plano->derivacion_nc_msp ?? ($agenda->validacion_nc_msp ?? '')}}" placeholder="Numero" required>
                        </div>
                        <div class="col-md-2">
                          <input id="derivacion_secuencial" type="text" class="form-control input-sm" name="derivacion_secuencial" value="{{$archivo_plano->derivacion_sec_msp ?? ($agenda->validacion_sec_msp ?? '')}}" placeholder="Secuencia" required>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                        <div class="col-md-7">
                            <input id="mes_plano" maxlength="10" type="text" class="form-control input-sm" name="mes_plano" value="{{$archivo_plano->mes_plano}}" autocomplete="off" required>
                        </div>
                    </div>
                    
                    <!--div class="form-group col-md-4 ">
                        <label for="nom_plantilla" class="col-md-4 control-label">Plantilla Proced:</label>
                        <div class="col-md-7">
                            <select id="nom_plantilla" name="nom_plantilla" class="form-control input-sm" onchange="obtener_seleccion()">
                            <option value="">Seleccione...</option>
                            @foreach($lista as $fila)
                            <option value="{{ $fila->descripcion }}">{{ $fila->descripcion }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div-->
                    <div class="form-group col-md-3">
                        <label for="estado" class="col-md-4 control-label">Activo/Inactivo:</label>
                        <div class="col-md-7">
                            <select id="estado" class="form-control input-sm" name="estado"  >
                                <option @if($archivo_plano->estado=='1') selected @endif value="1">Activo</option>
                                <option @if($archivo_plano->estado=='0') selected @endif value="0">Inactivo</option>
                            </select>    
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nom_procedimiento" class="col-md-1 control-label">Procedimiento:</label>
                        <div class="col-md-9">
                            <input id="nom_procedimiento" type="text" class="form-control input-sm" name="nom_procedimiento" value="{{ $archivo_plano->nom_procedimiento ?? (is_object($texto_procedimiento) ? $texto_procedimiento->realizado : $texto_procedimiento) }}">
                        </div>
                    </div>
                    <span style="color:white">{{$archivo_plano->id}}</span>
                </div>
            </form>
            <div class="row">
                <div class="col-md-10" align="center">
                    <a type="button" class="btn btn-success btn-xs" onclick="openModalExcel()">Masivo plantillas</a>
                    <a id="items" class="btn btn-success btn-xs oculto" data-remote="{{route('planilla_item.msp',['cabecera' => $archivo_plano->id])}}" data-toggle="modal" data-target="#item_msp" ><span class="glyphicon glyphicon-file"> Item</span></a>
                    <a id="item1" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-file"> Item</span></a>
                    <!--<a id="item" class="btn btn-success" data-remote="{{route('planilla_item.msp',['cabecera' => $archivo_plano->id])}}" data-toggle="modal" data-target="#item_msp" ><span class="glyphicon glyphicon-file"> Item</span></a>-->
                    <a id="procedimientos" class="btn btn-success btn-xs oculto"  data-remote="{{route('lista_item_modal.msp', ['cabecera' =>$archivo_plano->id])}}"  data-toggle="modal" data-target="#detalle_items_msp">
                      <span class="glyphicon glyphicon-plus"> Procedimiento</span>
                    </a>
                    <a id="procedimiento1" class="btn btn-success btn-xs">
                      <span class="glyphicon glyphicon-plus"> Procedimiento</span>
                    </a>
                    <a id="lab" class="btn btn-success btn-xs oculto " href="{{route('planilla.busca_ordenes_labs',['cabecera' => $archivo_plano->id ])}}" data-toggle="modal" data-target="#laboratorio" ><span class="glyphicon glyphicon-plus"> Laboratorio </span></a>
                    <a id="laboratorio1" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"> Laboratorio </span></a>
                    <!--a id="planilla" class="btn btn-success oculto" href="{{route('archivo_plano_msp.planilla_cargo_individual',['hcid' => $archivo_plano->id_hc])}}"><span class="glyphicon glyphicon-download-alt"></span>Planilla Cargo Individual</a>
                    <a id="planilla1" class="btn btn-success"></span>Planilla Cargo Individual</a-->
                    <a id="planilla1" class="btn btn-success btn-xs oculto" href="{{route('archivo_plano_msp.planilla_cargo_individual',['id_cabecera' => $archivo_plano->id])}}"><span class="glyphicon glyphicon-download-alt"></span>Planilla Cargo Individual</a>
                    <a id="planilla" class="btn btn-success btn-xs" onclick="verifica_existe_data_msp({{$archivo_plano->id}})"><span class="glyphicon glyphicon-download-alt"></span> Planilla Cargo Individual</a>
                    <a class="btn btn-success btn-xs" href="{{route('nuevoArchivoPlano.consolidada_codigo_pdf', ['idcab' => $archivo_plano->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> PDF Planilla Individual</a>
                    <!--<div class="col-md-2">
                        <a class="btn btn-success" href="{{route('archivo_plano_msp.planilla_cargo_consolidado',['hcid' => $archivo_plano->id_hc])}}"><span class="glyphicon glyphicon-download-alt"></span>Planilla Cargo Consolidado</a>
                    </div>-->
                    <a id="cardio" class="btn btn-success btn-xs oculto" href="{{route('planilla.cardiologia',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#cardiologia" > <span>Interconsulta</span></a>
                    <a id="cardio1" class="btn btn-success btn-xs"> <span> Interconsulta </span></a>
                    <a id="insumo" class="btn btn-success btn-xs oculto" href="{{route('nueva_planilla.cargarInsumosPlanillaModal',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#m_insumos" > <span> Insumos </span></a>
                    <a id="insumo1" class="btn btn-success btn-xs"> <span>Insumos</span></a>
                    <a id="medicina" class="btn btn-success btn-xs oculto" href="{{route('nueva_planilla.medicinas',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#m_medicinas" > <span> Medicinas </span></a>
                    <a id="medicina1" class="btn btn-success btn-xs"> <span>Medicinas</span></a>
              
                    <button type="button" class="btn btn-primary btn-xs" onclick="guardar()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span></button>
              
                    <button type="button" class="btn btn-danger btn-xs" onclick="elimina_todo_items_msp()"><span>Eliminar Todo</span> </button>
                
                    <!--<div class="col-md-2">
                        <a class="btn btn-success" href="{{route('archivo_plano_msp.planilla_individual',['hcid' => $archivo_plano->id_hc])}}"><span class="glyphicon glyphicon-download-alt"></span>Planilla individual</a>
                    </div>-->
                    
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('30msp')"><span>I.Fungibles 30</span> </button>
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('60msp')"><span>I.Fungibles 60</span> </button>
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('ninosgfungiblescpre')"><span>Fungibles Niños CPRE</span></button>
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('adultosgfungiblescpre')"><span>Fungibles Adultos CPRE</span></button>
                    <button type="button" class="btn btn-primary btn-xs" onclick="carga_fungibles('salasmsp')"><span>Salas</span></button>
                    <a id="planilla_descargo" class="btn btn-success btn-xs" href="{{route('ap_planilladetalle.planilla_descargo_pdf',['id'=>$archivo_plano->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Informe de Descargo</a>
                    <a href="{{route('nuevoArchivoPlano.index_log_auditoria',['cabecera'=>$archivo_plano->id])}}" class="btn btn-info btn-xs" target="_blanck" id="log_auditoria">
                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true" style="font-size: 16px">Log Auditoria</span></a>
                    @if(!is_null($archivo_plano->nom_procedimiento) && strpos($archivo_plano->nom_procedimiento, 'CONSUL') === false)
                    <a href="{{route('formulario007.formato_tecnico',['cabecera'=>$archivo_plano->id])}}" class="btn btn-info btn-xs" target="_blank">Informe tecnico</a>
                    @endif
                    @if($archivo_plano->cierre == '0')
                    <button type="button" class="btn btn-danger btn-xs" onclick="cierra_liquidacion()"><span>Cerrar Liquidación</span></button>
                    @else
                    <button type="button" class="btn btn-danger btn-xs"><span>Liquidación Cerrada !!!</span></button>
                    @endif
                    <button type="button" class="btn btn-primary btn-xs" onclick="agrega_masivo_insumo_laboratorio('{{$archivo_plano->id}}')"><span>Agregar Insumos - Lab (Masivo)</span></button>
                </div>
            </div>
            <br>
            <div class="col-md-12" id="detalle" style="padding-left: 2px;"></div>
        </div>
        <!-- Modal Fausto 992026 -->
        @php
             $typeUser=Auth::user()->id_tipo_usuario;
        @endphp
        @if($typeUser == 1)
            <div class="modal fade" id="miModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Masivo</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">


                          <div class="form-group row">
                                <label for="auditoria" class="col-md-3 col-form-label">Campo audítoria</label>
                                <div class="col-md-9">
                                    <input type="text" name="auditoria" id="auditoria" class="form-control" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="archivo_c" class="col-md-3 col-form-label">Cabecera</label>
                                <div class="col-md-9">
                                    <input type="file" name="archivo_c" id="archivo_c" class="form-control" accept=".csv">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="archivo_d" class="col-md-3 col-form-label">Detalle</label>
                                <div class="col-md-9">
                                    <input type="file" name="archivo_d" id="archivo_d" class="form-control" accept=".csv">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="archivo_ap" class="col-md-3 col-form-label">Insumo (ap_procedimiento)</label>
                                <div class="col-md-9">
                                    <input type="file" name="archivo_ap" id="archivo_ap" class="form-control" accept=".csv">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="btnGenerar">
                                Generar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>


<script type="text/javascript">
    $("#btnGenerar").click(function(){
      let archivo_c = $("#archivo_c")[0].files[0];
      let archivo_d = $("#archivo_d")[0].files[0];
      let archivo_ap = $("#archivo_ap")[0].files[0];
      let auditoria = $("#auditoria").val().trim();

      if (!archivo_c || !archivo_d || !archivo_ap) {
        alert("Debe seleccionar los 3 archivos");
       return;
      }

      if(auditoria === ''){
         alert("Debe seleccionar el campo auditoria");
       return;
      }

    let archivos = [archivo_c, archivo_d, archivo_ap];
       for (let archivo of archivos) {
            let extension = archivo.name.split('.').pop().toLowerCase();

            if (extension !== 'csv') {
                alert("Todos los archivos deben ser CSV");
                return;
            }
        }
     let formData = new FormData();
    formData.append("archivo_c", archivo_c);
    formData.append("archivo_d", archivo_d);
    formData.append("archivo_ap", archivo_ap);
    formData.append("auditoria", auditoria);

    $.ajax({
        url:"{{ route('subida_plantilla_msp') }}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(!response.status){
                alert(response.mensaje);    
                return;
            }
            alert(response.mensaje);
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert("Ocurrió un error");
        }
    });


    });

    function openModalExcel(){

        $("#miModal").modal("show");
    }

    $('#laboratorio').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    });

    $('#item_msp').on('hidden.bs.modal', function(){
        location.reload();
        $(this).removeData('bs.modal');
    });

    $('#detalle_items_msp').on('hidden.bs.modal', function(){
        location.reload();
        $(this).removeData('bs.modal');
    });

    $('#m_medicinas').on('hidden.bs.modal', function(){
        location.reload();
        $(this).removeData('bs.modal');
    });

    /*$('#upd_item_msp').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });*/


    function verifica_existe_data_msp(id_cabecera){

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
            url:"{{ route('verifica_planilla_cargo.individualmsp') }}",
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
        var derivacion_cv =$('#derivacion_cv').val();
        var derivacion_num_caso =$('#derivacion_num_caso').val();
        var derivacion_secuencial =$('#derivacion_secuencial').val();


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
        if(derivacion_cv==""){
            texto+="Por favor complete el campo CV en Cod. Derivación MSP.<br>";
        }
        if(derivacion_num_caso==""){
            texto+="Por favor ingrese el campo Numero en Cod. Derivación MSP.<br>";
        }
        if(derivacion_secuencial==""){
            texto+="Por favor ingrese el campo Secuencia en Cod. Derivación MSP.<br>";
        }
        if(texto!= ''){
           
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
        var derivacion_cv =$('#derivacion_cv').val();
        var derivacion_num_caso =$('#derivacion_num_caso').val();
        var derivacion_secuencial =$('#derivacion_secuencial').val();

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
        if(derivacion_cv==""){
            texto+="Por favor ingrese el campo CV en Cod. Derivación MSP.<br>";
        }
        if(derivacion_num_caso==""){
            texto+="Por favor ingrese el campo Numero en Cod. Derivación MSP.<br>";
        }
        if(derivacion_secuencial==""){
            texto+="Por favor ingrese el campo Secuencia en Cod. Derivación MSP.<br>";
        }
        
        if(texto!= ''){
           
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
        var derivacion_cv =$('#derivacion_cv').val();
        var derivacion_num_caso =$('#derivacion_num_caso').val();
        var derivacion_secuencial =$('#derivacion_secuencial').val();
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
        if(derivacion_cv==""){
            texto+="Por favor ingrese el campo CV en Cod. Derivación MSP.<br>";
        }
        if(derivacion_num_caso==""){
            texto+="Por favor ingrese el campo Numero en Cod. Derivación MSP.<br>";
        }
        if(derivacion_secuencial==""){
            texto+="Por favor ingrese el campo Secuencia en Cod. Derivación MSP.<br>";
        }
        if(texto!= ''){
           
           swal("Error!",texto,"error");
             
        }
        if (texto=='') { 
          $('#lab').click();
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
        var derivacion_cv =$('#derivacion_cv').val();
        var derivacion_num_caso =$('#derivacion_num_caso').val();
        var derivacion_secuencial =$('#derivacion_secuencial').val();
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
        if(derivacion_cv==""){
            texto+="Por favor ingrese el campo CV en Cod. Derivación MSP.<br>";
        }
        if(derivacion_num_caso==""){
            texto+="Por favor ingrese el campo Numero en Cod. Derivación MSP.<br>";
        }
        if(derivacion_secuencial==""){
            texto+="Por favor ingrese el campo Secuencia en Cod. Derivación MSP.<br>";
        }
        if(texto!= ''){
           
           swal("Error!",texto,"error");
             
        }
        if (texto=='') { 
        $('#cardio').click();
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
        var derivacion_cv =$('#derivacion_cv').val();
        var derivacion_num_caso =$('#derivacion_num_caso').val();
        var derivacion_secuencial =$('#derivacion_secuencial').val();
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
        if(derivacion_cv==""){
            texto+="Por favor ingrese el campo CV en Cod. Derivación MSP.<br>";
        }
        if(derivacion_num_caso==""){
            texto+="Por favor ingrese el campo Numero en Cod. Derivación MSP.<br>";
        }
        if(derivacion_secuencial==""){
            texto+="Por favor ingrese el campo Secuencia en Cod. Derivación MSP.<br>";
        }
        if(texto!= ''){
           
           swal("Error!",texto,"error");
             
        }
        if (texto=='') { 
          $('#planilla').click();
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
    
    function seleccionar_orden(orden,cabecera){
        $.ajax({
          type: 'get',
          url:"{{ url('archivo_plano/planilla/detalle/laboratorio/ingresar') }}/" + orden + "/" + cabecera,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
            //$('#laboratorio_cerrar').click();
            location.reload();
          },
          error: function(data){
             console.log(data);
          }
        });

    } 

    function seleccionar_cardio(hc_cardio,cabecera,seguro,empresa,agenda){
        $.ajax({
          type: 'get',
          url:"{{ url('archivo_planoc/planilla/ingresar_cardio') }}/" + hc_cardio + "/" + cabecera + "/" + seguro + "/" + empresa + "/" + agenda,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
           location.reload();
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

    function recalculo_valores_items_msp(idcabecera,idseguro){ 

        var id_empresa = $('#id_empresa').val();

        $.ajax({
          type: 'get',
          url:"{{ url('archivo_plano/planilla/calcular_nuevos/valores/items') }}/" + idcabecera + "/" + idseguro+ "/" + id_empresa,
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
    
    function guardar(){

        var cedula = $('#cedula').val();
        var nombres =$('#nombre').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        //var procedimiento =$('#nom_procedimiento').val();
        var derivacion_cv =$('#derivacion_cv').val();
        var derivacion_num_caso =$('#derivacion_num_caso').val();
        var derivacion_secuencial =$('#derivacion_secuencial').val();

        inicio_mes = 3,
        fin_mes    = 5,
        inicio_anio = 6,
        fin_anio    = 10,
        subcad_mes = fecha_ingreso.substring(inicio_mes,fin_mes);
        subcad_anio = fecha_ingreso.substring(inicio_anio,fin_anio);
        conca_cad = subcad_mes.concat(subcad_anio);

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
        if(conca_cad != mesplano){
            texto+="La Fecha de Ingreso debe coincidir con el Mes de Plano.<br>";
        }
        if(derivacion_cv==""){
            texto+="Por favor ingrese el campo CV en Cod. Derivación MSP.<br>";
        }
        if(derivacion_num_caso==""){
            texto+="Por favor ingrese el campo Numero en Cod. Derivación MSP.<br>";
        }
        if(derivacion_secuencial==""){
            texto+="Por favor ingrese el campo Secuencia en Cod. Derivación MSP.<br>";
        }
        if(texto!= ''){
           
           swal("Error!",texto,"error");
             
        }
        if (texto=='') {
            $.ajax({
            type: 'post',
            url:"{{ route('planilla_msp.guardar') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
                //console.log(data);
                if(data == "existe"){
                    swal({
                        title: "No se puede ingresar planilla individual MSP debido a que el ciclo ya fue contabilizado.",
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
                swal("dsada");
            }
            });
        }
    }


    function elimina_todo_items_msp(){

        if (confirm('¿Desea Eliminar Todos los Items?')) {
            $.ajax({
            type: 'post',
            url:"{{ route('delete.todo_items_msp') }}",
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
                    
                };
                location.reload();
            },
            error: function(data){
                console.log(data);
            }
            });

        }else{
          location.reload();
        }
    
    }

    $(function () {

        var cie10Pre = $('#cie_10_pre').val();
        if ($('#cie10').val() == '' && cie10Pre != '') {
            $('#cie10').val(cie10Pre).trigger('change');
        }

        mostrar_detalle();

        $('#fecha_nac').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            //Important! See issue #1075
            
        });
        $('#fecha_ing').datetimepicker({
            useCurrent: false,
            format: 'DD/MM/YYYY',
            //defaultDate:'{{$fecha_ing_inv}}',
            @if($archivo_plano->fecha_ing !=null)
              defaultDate: '{{$archivo_plano->fecha_ing}}',
              //defaultDate:'{{date('d-m-Y',strtotime($archivo_plano->fecha_ing))}}',
            @endif
            
            
        });
        $('#fecha_alt').datetimepicker({
            useCurrent: false,
            format: 'DD/MM/YYYY',
            @if($archivo_plano->fecha_alt !=null)
               defaultDate:'{{$archivo_plano->fecha_alt}}',
              //defaultDate:'{{date('d-m-Y',strtotime($archivo_plano->fecha_alt))}}',
            @endif
            //defaultDate:'{{$fecha_alt_inv}}',
            
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
        minLength: 2,
    } );


    $("#cie10").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {
                cie10: $("#cie10").val()
            },
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

    /*$("#nom_procedimiento").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('archivo_plano.procedimiento_plantilla')}}",
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
        minLength: 2,
    } );*/


    /*function CloseModal(frameElement, i) {
        if (frameElement) {
            var dialog = $(frameElement).closest("#mdetalle");
            if (dialog.length > 0) {
                dialog.modal("hide");
            //   alert(i);
                var id=i;
                var nameArr = id.split(',');
                var nu1 = nameArr[0];
                var nu2 = nameArr[1].trim();
                var nu3 = nameArr[2].trim();
                //alert (nu3);
            $.ajax({
            url:"{{ url('archivo_plano/planilla/detalle/procedimiento/ingresar') }}/" + nu1 + "/"+ nu2 + "/"+ nu3,
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'POST',
                data: { id: i },
                success: function(response)
                {
                alert('Plantilla ingresada');
                window.location.reload(true);
                }
            });
            }
        }
    }*/
    
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

    function agrega_masivo_insumo_laboratorio(cabecera){
        $.ajax({
            type: 'post',
            url: "{{url('archivo_plano/boton_masivo/agrega_planilla/insumos')}}/" + cabecera,
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
            dataType: 'json',
            success: function(data){
                if(data && data.estado === 'ok'){
                    swal({
                        title: "Carga completada",
                        text: data.mensaje || "Se agregaron los insumos y laboratorios disponibles.",
                        type: 'success',
                    }).then(function(){ location.reload(); });
                    return;
                }
                swal("Error", data && data.mensaje ? data.mensaje : "No se pudo completar la carga.", "error");
            },
            error: function(xhr){
                var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
                    ? xhr.responseJSON.mensaje
                    : "No se pudo completar la operación.";
                swal("Error", mensaje, "error");
            }
        });
    }

    function cierra_liquidacion(){
        if (!confirm('¿Desea Cerrar la Liquidación?')) {
            return;
        }
        $.ajax({
            type: 'get',
            url: "{{route('nuevoarchivoplano.cerrar_liquidacion', ['id' => $archivo_plano->id])}}",
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
            data: {'funcion': 'PLANILLA MSP'},
            success: function(data){
                if(data == 'ok'){
                    swal({title: "Liquidación Cerrada", type: 'success'});
                }
                location.reload();
            },
            error: function(){
                swal("Error", "No se pudo cerrar la liquidación.", "error");
            }
        });
    }
    
</script>

@endsection
