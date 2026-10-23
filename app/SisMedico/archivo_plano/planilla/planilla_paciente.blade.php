@extends('archivo_plano.planilla.base')
@section('action-content')
<style type="text/css">

    .control-label{
        padding: 0;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
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


<!--<div class="modal fade" id="mdetalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <iframe id="dito" class="embed-responsive-item" src="http://192.168.75.125/sis_medico_prb/public/administracion/procedimientos/lista?cabecera={{$archivo_plano->id}}" allowfullscreen style="width:100%; height:500px"></iframe>
    </div>
  </div>
</div>-->

<section class="content">
	<div class="box">
		
		    <!--<div class="row">
		        <div class="col-sm-8">
		          <h3 class="control-label">INGRESO DE PLANILLAS IESS</h3>
		        </div>
		    </div>-->
            <!--<div class="col-md-5 size_text">
              <span id="Label6" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Ingreso de Item IESS</span>
            </div>-->
            <!--div class="row head-title size_text">
                <div class="col-md-12">
                  <label class="color_texto" for="title">INGRESO DE PLANILLAS IESS</label>
                </div>
            </div-->
		
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
    				<!--<div class="col-md-10">
    	                <h4 for="cedula_ben"><b>Beneficiario: {{$archivo_plano->id_paciente}} - {{$archivo_plano->paciente->apellido1}} {{$archivo_plano->paciente->apellido2}} {{$archivo_plano->paciente->nombre1}} {{$archivo_plano->paciente->nombre2}}</b>
    	                </h4>
                    </div>-->
                    <!--<div class="col-md-6">
                        <h4 for="cedula_ben"><b>Afiliado: {{$archivo_plano->id_usuario}} - {{$archivo_plano->usuario->apellido1}} {{$archivo_plano->usuario->apellido2}} {{$archivo_plano->usuario->nombre1}} {{$archivo_plano->usuario->nombre2}}</b>
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
                        <label for="hc_iess" class="col-md-4 control-label">Hc:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$ult_digito}}
                            <!--<input id="hc_iess" maxlength="10" type="text" class="form-control input-sm" name="hc_iess" value="{{$ult_digito}}" readonly>-->
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
                        <label for="sexo" class="col-md-4 control-label">Sexo:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$sexo}}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="edad" class="col-md-4 control-label">Edad:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$xedad}}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento:</label>
                        <div class="col-md-7" style="padding-top: 7px;">
                            {{$fech_inver}}
                        </div>
                    </div>
                    @if($archivo_plano->id_paciente != null)
                        <div class="form-group col-md-4 ">
                            <label for="cedula" class="col-md-4 control-label">Cedula Afiliado:</label>
                            <div class="col-md-7">
                                <input id="cedula" maxlength="15" required type="text" class="form-control input-sm" name="cedula" value="{{$archivo_plano->id_paciente}}">
                            </div>
                        </div>
                    @else
                        <div class="form-group col-md-4 ">
                            <label for="cedula" class="col-md-4 control-label">Cedula Afiliado:</label>
                            <div class="col-md-7">
                                <input id="cedula" maxlength="15" required type="text" class="form-control input-sm" name="cedula" value="{{$archivo_plano->id_usuario}}">
                            </div>
                        </div>
                    @endif
                    @if($archivo_plano->nombres != null)
                        <div class="form-group col-md-4">
                            <label for="nombre" class="col-md-4 control-label">Nombres Afiliado:</label>
                            <div class="col-md-7">
                                <input id="nombre" type="text"  required class="form-control input-sm" name="nombre" value="{{$archivo_plano->nombres}}">
                            </div>
                        </div>
                    @else
                        <div class="form-group col-md-4">
                            <label for="nombre" class="col-md-4 control-label">Nombre Afiliado:</label>
                            <div class="col-md-7">
                                <input id="nombre" type="text" required class="form-control input-sm" name="nombre" value="{{$archivo_plano->usuario->apellido1}} {{$archivo_plano->usuario->apellido2}} {{$archivo_plano->usuario->nombre1}} {{$archivo_plano->usuario->nombre2}}">
                            </div>
                        </div>
                    @endif

                    
                    
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
                                <input type="text"  class="form-control input-sm" id="fecha_ing" name="fecha_ing" placeholder="DD/MM/AAAA">
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
                                <input type="text"  class="form-control input-sm" id="fecha_alt" name="fecha_alt" placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="id_tipo_seguro" class="col-md-4 control-label">Tipo Seguro:</label>
                        <div class="col-md-7">
                            <select id="id_tipo_seguro" name="id_tipo_seguro" class="form-control input-sm" >
                                @foreach($tipo_seguros as $value)
                                    <option @if($archivo_plano->id_tipo_seguro == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4 ">
                        <label for="id_seguro_priv" class="col-md-4 control-label">Seguro Privado:</label>
                        <div class="col-md-7">
                            <select id="id_seguro_priv" name="id_seguro_priv" class="form-control input-sm" >
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
                    <div class="form-group col-md-8">
                        <label for="cie10" class="col-md-2 control-label">Diagnostico Prin.:</label>
                        <div class="col-md-10">
                            <input type="hidden" name="codigo" id="codigo" value="{{$archivo_plano->cie10}}">
                            <input id="cie10" type="text" required class="form-control input-sm" name="cie10" placeholder="Diagnóstico" value="{{$txt_cie10}}">
                        </div>
                    </div>
                    <!--<div class="form-group col-md-2">
                      <label for="ocul" class="col-md-4 control-label"></label>
                      <div class="col-md-7">
                      </div> 
                    </div>-->
                    <div class="form-group col-md-4 ">
                        <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                        <div class="col-md-7">
                            <input id="mes_plano" maxlength="10" required type="text" class="form-control input-sm" name="mes_plano" value="{{$archivo_plano->mes_plano}}" >
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
                            <select id="id_cod_deriva" name="id_cod_deriva" class="form-control input-sm" >   
                            @foreach($codigos_dev as $value)
                            <option @if($archivo_plano->id_cod_deriva == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                          @endforeach
                            </select>
                        </div>
                    </div>
                   
                   <div class="form-group col-md-4 ">
                        <label for="presuntivo_def" class="col-md-4 control-label">Presuntivo/Definitivo:</label>
                        <div class="col-md-7">
                            <select id="presuntivo_def" class="form-control input-sm" name="presuntivo_def"  >
                                <option @if($archivo_plano->presuntivo_def=='Presuntivo') selected @endif value="Presuntivo">Presuntivo</option>
                                <option @if($archivo_plano->presuntivo_def=='Definitivo') selected @endif value="Definitivo">Definitivo</option>
                            </select>    
                        </div>
                    </div>
                    
                    <div class="form-group col-md-4 ">
                        <label for="id_cod_dep" class="col-md-4 control-label">Cod. Dependencia:</label>
                        <div class="col-md-7">
                            <select id="id_cod_dep" name="id_cod_dep" class="form-control input-sm" >
                            @foreach($codigos_dep as $value)
                            <option @if($archivo_plano->id_cod_dep == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre_cod}}</option>
                          @endforeach
                            </select>
                        </div>
                    </div>
                    <!--<div class="form-group col-md-4 ">
                        <label for="nom_planilla" class="col-md-4 control-label">Nombre de Plantilla:</label>
                        <div class="col-md-7">
                            <input id="nom_planilla" maxlength="40" type="text" class="form-control input-sm" name="nom_planilla"  value="{{$archivo_plano->nom_planilla}}" >
                        </div>
                    </div>-->
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
                    <div class="form-group col-md-8 ">
                        <label for="nom_procedimiento" class="col-md-2 control-label">Procedimiento:</label>
                        <div class="col-md-9">
                            <input id="nom_procedimiento"  required maxlength="40" type="text" class="form-control input-sm" name="nom_procedimiento"  value="{{$archivo_plano->nom_procedimiento}}" >
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
                    <span style="color:white">{{$archivo_plano->id}}</span>
                </div>
			</form>
            <div class="row">
                <div class="col-md-1">
                    <a id="items" class="btn btn-success oculto" data-remote="{{route('planilla_item.iess',['cabecera' => $archivo_plano->id])}}" data-toggle="modal" data-target="#item" ><span class="glyphicon glyphicon-file"> Item</span> </a>
                    <a id="item1" class="btn btn-success" ><span class="glyphicon glyphicon-file"> Item</span> </a>
                    <!--<a id="item" class="btn btn-success" data-remote="{{route('planilla_item.iess',['cabecera' => $archivo_plano->id])}}" data-toggle="modal" data-target="#item" ><span class="glyphicon glyphicon-file"> Item</span> </a>-->
                </div>
                <div class="col-md-2">
                    <a id="procedimientos" class="btn btn-success oculto"  data-remote="{{route('lista_item_modal.iess', ['cabecera' =>$archivo_plano->id])}}"  data-toggle="modal" data-target="#detalle_items_iess"><span class="glyphicon glyphicon-plus"> Procedimiento</span> </a>
                    <a id="procedimiento1" class="btn btn-success"><span class="glyphicon glyphicon-plus"> Procedimiento</span> </a>
                </div>
                <!--<div class="col-md-2">
                    <a class="btn btn-success" href="" data-toggle="modal" data-target="#mdetalle" >Procedimiento</a>
                </div>-->
                <div class="col-md-2">
                    <a id="lab" class="btn btn-success oculto" href="{{route('planilla.busca_ordenes_labs',['cabecera' => $archivo_plano->id ])}}" data-toggle="modal" data-target="#laboratorio" > <span class="glyphicon glyphicon-plus"> Laboratorio </span></a>
                    <a id="laboratorio1" class="btn btn-success"> <span class="glyphicon glyphicon-plus"> Laboratorio </span></a>
                </div>
                <div class="col-md-2">
                    <!--a id="planilla" class="btn btn-success oculto" href="{{route('archivo_plano.planilla_individual',['hcid' => $archivo_plano->id])}}"><span class="glyphicon glyphicon-download-alt"></span> Planilla individual</a>
                    <a id="planilla1" class="btn btn-success"><span class="glyphicon glyphicon-download-alt"></span> Planilla individual</a-->
                    <a id="planilla" class="btn btn-success" href="{{route('archivo_plano.planilla_individual',['hcid' => $archivo_plano->id])}}"><span class="glyphicon glyphicon-download-alt"></span> Planilla individual</a>
                </div>
                <div class="col-md-2">
                    <a id="cardio" class="btn btn-success oculto" href="{{route('planilla.cardiologia',['cabecera' =>$archivo_plano->id])}}" data-toggle="modal" data-target="#cardiologia" > <span class="glyphicon glyphicon-heart"> Cardiología </span></a>
                    <a id="cardio1" class="btn btn-success"> <span class="glyphicon glyphicon-heart"> Cardiología </span></a>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary" onclick="guardar()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger" onclick="elimina_todo_items_iess()"><span>Eliminar Todo</span> </button>
                </div>
                
                <div class="col-md-2">
                    <input name="lulu" type="hidden"> 
                </div>
            </div>
            <br>
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

    $('#laboratorio').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    }); 

    $('#item').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    });
    $('#upd_item_iess').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    });

    $('#detalle_items_iess').on('hidden.bs.modal', function(){
        location.reload();
        $(this).removeData('bs.modal');
    });

    $('#item1').click(function(){
        //alert('entra');
        var cedula = $('#cedula').val();
        var nombres =$('#nombres').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = '';
        if(cedula==''){
            texto = "Cédula del Afiliado";
        }
        if(nombres==''){
            texto = texto + "Nombres del Afiliado";
        }
        if(fecha_ingreso==''){
            texto = texto + "Fecha Ingreso";
        }
        if(fecha_alta==''){
            texto = texto + "Fecha Alta";
        }
        if(diagnostico==''){
            texto = texto + "Diagnostico";
        }
        if(mesplano==''){
            texto = texto + "Mes Plano";
        }
        if(procedimiento==''){
            texto = texto + "Procedimiento";
        }
        if(texto != ''){
            swal({
                title: "Ingrese :",
                text: texto,
                icon: "warning",
                type: 'error',
            });
            //$("#err").text(texto);    
        }
        if (texto=='') { 
          $('#items').click();
          //guardar();
        }

    });
    

    $('#procedimiento1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombres').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = '';
        if(cedula==''){
            texto = "Cédula del Afiliado";
        }
        if(nombres==''){
            texto = texto +"  "+"Nombres del Afiliado";
        }
        if(fecha_ingreso==''){
            texto = texto +"  "+ "Fecha Ingreso";
        }
        if(fecha_alta==''){
            texto = texto +"  "+ "Fecha Alta";
        }
        if(diagnostico==''){
            texto = texto +"  "+ "Diagnostico";
        }
        if(mesplano==''){
            texto = texto +"  "+ "Mes Plano";
        }
        if(procedimiento==''){
            texto = texto +"  "+ "Procedimiento";
        }
        if(texto != ''){
            swal({
                title: "Ingrese :",
                text: texto,
                icon: "warning",
                type: 'error',
            });
            //$("#err").text(texto);    
        }
        if (texto=='') { 
         $('#procedimientos').click();
         //guardar();
        }
    });

    $('#laboratorio1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombres').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = '';
        if(cedula==''){
            texto = "Cédula del Afiliado";
        }
        if(nombres==''){
            texto = texto +"  "+"Nombres del Afiliado";
        }
        if(fecha_ingreso==''){
            texto = texto +"  "+ "Fecha Ingreso";
        }
        if(fecha_alta==''){
            texto = texto +"  "+ "Fecha Alta";
        }
        if(diagnostico==''){
            texto = texto +"  "+ "Diagnostico";
        }
        if(mesplano==''){
            texto = texto +"  "+ "Mes Plano";
        }
        if(procedimiento==''){
            texto = texto +"  "+ "Procedimiento";
        }
        if(texto != ''){
            swal({
                title: "Ingrese :",
                text: texto,
                icon: "warning",
                type: 'error',
            });
            //$("#err").text(texto);    
        }
        if (texto=='') { 
         $('#lab').click();
         //guardar();
        }
    });

    $('#cardio1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombres').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = '';
        if(cedula==''){
            texto = "Cédula del Afiliado";
        }
        if(nombres==''){
            texto = texto +"  "+"Nombres del Afiliado";
        }
        if(fecha_ingreso==''){
            texto = texto +"  "+ "Fecha Ingreso";
        }
        if(fecha_alta==''){
            texto = texto +"  "+ "Fecha Alta";
        }
        if(diagnostico==''){
            texto = texto +"  "+ "Diagnostico";
        }
        if(mesplano==''){
            texto = texto +"  "+ "Mes Plano";
        }
        if(procedimiento==''){
            texto = texto +"  "+ "Procedimiento";
        }
        if(texto != ''){
            swal({
                title: "Ingrese :",
                text: texto,
                icon: "warning",
                type: 'error',
            });
            //$("#err").text(texto);    
        }
        if (texto=='') { 
          $('#cardio').click();
          //guardar();
        }
    });

    $('#planilla1').click(function(){
        var cedula = $('#cedula').val();
        var nombres =$('#nombres').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = '';
        if(cedula==''){
            texto = "Cédula del Afiliado";
        }
        if(nombres==''){
            texto = texto +"  "+"Nombres del Afiliado";
        }
        if(fecha_ingreso==''){
            texto = texto +"  "+ "Fecha Ingreso";
        }
        if(fecha_alta==''){
            texto = texto +"  "+ "Fecha Alta";
        }
        if(diagnostico==''){
            texto = texto +"  "+ "Diagnostico";
        }
        if(mesplano==''){
            texto = texto +"  "+ "Mes Plano";
        }
        if(procedimiento==''){
            texto = texto +"  "+ "Procedimiento";
        }
        if(texto != ''){
            swal({
                title: "Ingrese :",
                text: texto,
                icon: "warning",
                type: 'error',
            });
            //$("#err").text(texto);    
        }
        if (texto=='') { 
          $('#planilla').click();
          //guardar();
        }
    });
    

    function seleccionar_orden(orden,cabecera){
        $.ajax({
          type: 'get',
          url:"{{ url('archivo_plano/planilla/detalle/laboratorio/ingresar') }}/" + orden + "/" + cabecera,
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

    function seleccionar_cardio(hc_cardio,cabecera,seguro,empresa,agenda){
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
            location.reload();
          },
          error: function(data){
             console.log(data);
          }
        });

    }
    
    
    function guardar(){

        var cedula = $('#cedula').val();
        var nombres =$('#nombres').val();
        var fecha_ingreso =$('#fecha_ing').val();
        var fecha_alta =$('#fecha_alt').val();
        var diagnostico =$('#cie10').val();
        var mesplano =$('#mes_plano').val();
        var procedimiento =$('#nom_procedimiento').val();

        var texto = '';
        if(cedula==''){
            texto = "Cédula del Afiliado";
        }
        if(nombres==''){
            texto = texto +"  "+"Nombres del Afiliado";
        }
        if(fecha_ingreso==''){
            texto = texto +"  "+ "Fecha Ingreso";
        }
        if(fecha_alta==''){
            texto = texto +"  "+ "Fecha Alta";
        }
        if(diagnostico==''){
            texto = texto +"  "+ "Diagnostico";
        }
        if(mesplano==''){
            texto = texto +"  "+ "Mes Plano";
        }
        if(procedimiento==''){
            texto = texto +"  "+ "Procedimiento";
        }
        if(texto != ''){
            swal({
                title: "Ingrese :",
                text: texto,
                icon: "warning",
                type: 'error',
            });
            //$("#err").text(texto);    
        }
        if (texto=='') { 
        $.ajax({
          type: 'post',
          url:"{{ route('planilla.guardar') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            console.log(data);
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
             //swal("Complete todos los campos");
          }
        });
        }
    }

    function elimina_todo_items_iess(){

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
                
            };
            location.reload();
          },
          error: function(data){
             console.log(data);
          }
        });
    
    }


    $(function () {

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

    $("#nom_procedimiento").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('archivo_plano.procedimiento_plantilla')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {term: request.term},
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
    /*function CloseModal(frameElement, i) {
        if (frameElement) {
            var dialog = $(frameElement).closest("#mdetalle");
            if (dialog.length > 0) {
                dialog.modal("hide");
         
                var id=i;
                var nameArr = id.split(',');
                var nu1 = nameArr[0];
                var nu2 = nameArr[1].trim();
                var nu3 = nameArr[2].trim();
                
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

</script>





@endsection