
@extends('agenda.base_doc')

@section('action-content')

<style type="text/css">
    
    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    } 

    /*#mceu_13-body, #mceu_44-body, .mce-branding{
        display: none;
    }*/ 


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

</style>

<div class="container-fluid" >
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>@lang('agenda.nombres')</b></td>
                                    <td><b>@lang('agenda.apellidos')</b></td>
                                    <td><b>@lang('agenda.identificacion')</b></td>
                                    <td style="text-align:right;"><b>@lang('agenda.cortesiaseneldia')</b></td>
                                </tr>
                                <tr>
                                    <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</td>
                                    <td>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                    <td>{{$agenda->id_paciente}}</td>
                                    <td style="text-align:right; @if($cant_cortesias>1) color:red; @endif">{{$cant_cortesias}}</td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a type="button" href="{{route('visitas.index', ['id_paciente' => $agenda->id_paciente, 'id_agenda' => $agenda->id ])}}" class="btn btn-success btn-sm">
            @lang('agenda.visitas')
            </a>
            <a type="button" href="{{route('estudio.lista', ['id' => $agenda->id ])}}" class="btn btn-success btn-sm">
                <span> @lang('agenda.procedimientos')</span>
            </a>
            <a type="button" href="{{ route('agenda.agenda2') }}" class="btn btn-primary btn-sm">
                <span class="glyphicon glyphicon-calendar"> @lang('agenda.agenda')</span>
            </a>
        </div>
        <div class="col-md-6" style="padding-right: 6px;">
            
            <div class="box box-primary">
                <div class="box-body" style="padding: 5px;">
                    <form id="frm">
                        <input type="hidden" name="id_hc_procedimientos2" value=@if(!is_null($protocolo))"{{$protocolo->id_hc_procedimientos}}"@else "" @endif>
                        <input type="hidden" name="snombre" id="snombre" value="{{$agenda->snombre}}">
                        <div class="col-md-2 {{ $errors->has('cortesia') ? ' has-error' : '' }}" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.cortesia')</label>
                            <select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);">
                                <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">@lang('agenda.no')</option>
                                <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">@lang('agenda.si')</option>
                            </select>    
                        </div>
                        <input type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">
                        
                        <div class="col-md-4" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.sexo')</label>
                            <select name="sexo" id="sexo" onchange="guardar();" class="form-control input-sm" >
                               <option @if($agenda->sexo == 1) selected @endif value="1">@lang('agenda.masculino')</option> 
                               <option @if($agenda->sexo == 2) selected @endif value="2">@lang('agenda.femenino')</option> 
                            </select>
                        </div>
                        <div class="col-md-4" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.fechaNacimiento')</label>
                            <input id="fecha_nacimiento" type="date" onchange="guardar();" name="fecha_nacimiento" value='{{$agenda->fecha_nacimiento}}' class="form-control input-sm" >
                        </div>
                        <div class="col-md-2" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.edad')</label>
                            <input id="edad" type="text" name="edad" value='' class="form-control input-sm" readonly>
                        </div>


                        <div class="col-md-5" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.estadoCivil')</label>
                            <select class="form-control input-sm"  name="estadocivil" onchange="guardar();">
                               <option @if($agenda->estadocivil == 1) selected @endif value="1">@lang('agenda.soltero')</option> 
                               <option @if($agenda->estadocivil == 2) selected @endif value="2">@lang('agenda.casado')</option>
                               <option @if($agenda->estadocivil == 3) selected @endif value="3">@lang('agenda.viudo')</option> 
                               <option @if($agenda->estadocivil == 4) selected @endif value="4">@lang('agenda.divorciado')</option> 
                            </select>
                        </div>    
                        
                        
                        <div class="col-md-4" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.grupoSanguineo')</label>
                            <select id="gruposanguineo" class="form-control input-sm" name="gruposanguineo" onchange="guardar();">
                                <option value="">Seleccionar ..</option>
                                <option @if($agenda->gruposanguineo == "AB-") selected @endif value="AB-">AB-</option>
                                <option @if($agenda->gruposanguineo == "AB+") selected @endif value="AB+">AB+</option>
                                <option @if($agenda->gruposanguineo == "A-") selected @endif value="A-">A-</option>
                                <option @if($agenda->gruposanguineo == "A+") selected @endif value="A+">A+</option>
                                <option @if($agenda->gruposanguineo == "B-") selected @endif value="B-">B-</option>
                                <option @if($agenda->gruposanguineo == "B+") selected @endif value="B+">B+</option>
                                <option @if($agenda->gruposanguineo == "O-") selected @endif value="O-">O-</option>
                                <option @if($agenda->gruposanguineo == "O+") selected @endif value="O+">O+</option>
                            </select> 
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.transfusiones')</label>
                            <select id="transfusion" name="transfusion" class="form-control input-sm" onchange="guardar();  ">
                                <option @if($agenda->transfusion=='NO'){{'selected '}}@endif value="NO">@lang('agenda.no')</option>
                                <option @if($agenda->transfusion=='SI'){{'selected '}}@endif value="SI">@lang('agenda.si')</option>
                            </select>
                        </div> 
                        <div class="col-md-12" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.direccionDomicilio')</label>
                            <input class="form-control input-sm" onchange="guardar();" type="text" name="direccion" value="{{$agenda->direccion}}">
                        </div>
                                
                        
                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.alergias')</label>
                            <textarea name="alergias" id="alergias" style="width: 100%;font-size: 13px;" rows="3" onchange="guardar();">{{$agenda->alergias}}</textarea>
                        </div>
                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.vacunas')</label>
                            <textarea name="vacuna" style="width: 100%;font-size: 13px;" rows="3" onchange="guardar();">{{$agenda->vacuna}}</textarea>
                        </div>
                        
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.fechaNacimiento')</label>
                            <input class="form-control input-sm" onchange="guardar();" type="text" name="lugar_nacimiento" value="{{$agenda->lugar_nacimiento}}">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.telefonoDomicilio')</label>
                            <input class="form-control input-sm" onchange="guardar();" type="text" name="telefono1" value="{{$agenda->telefono1}}">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            
                            <label for="cortesia" class="control-label">@lang('agenda.telefonoCelular')</label>
                            <input class="form-control input-sm" onchange="guardar();" type="text" name="telefono2" value="{{$agenda->telefono2}}">
                        </div>

                        <div class="col-md-3" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.ocupacion')</label>
                            <input class="form-control input-sm" onchange="guardar();" type="text" name="ocupacion" value="{{$agenda->ocupacion}}">
                        </div>

                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.antecedentesPatologicos')</label>
                            <textarea name="antecedentes_pat" id="antecedentes_pat" style="width: 100%;font-size: 13px;" rows="3" onchange="guardar();">{{$agenda->antecedentes_pat}}</textarea>
                        </div>

                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.antecedentesFamiliares')</label>
                            <textarea name="antecedentes_fam" id="antecedentes_fam" style="width: 100%;font-size: 13px;" rows="3" onchange="guardar();">{{$agenda->antecedentes_fam}}</textarea>
                        </div>
                        
                        <div class="col-md-12" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.antecedentesQuirurgicos')</label>
                            <textarea name="antecedentes_quir" id="antecedentes_quir" style="width: 100%;font-size: 13px;" rows="2" onchange="guardar();">{{$agenda->antecedentes_quir}}</textarea>
                        </div>
                        
                        <div class="col-md-12" style="padding: 1px;">
                            <label for="cortesia" class="control-label">@lang('agenda.historiaClinica')</label>
                            @if(!is_null($evoluciones))
                            <div class="table-responsive col-md-12 col-xs-12" style="max-height: 200px;font-size: 12px;">
                                <table class="table table-bordered table-hover dataTable">
                                    @foreach($evoluciones as $value)
                                    <tr>
                                        <td><b>{{$value->fechaini}}</b><br><?php echo $value->cuadro_clinico ?></td>
                                    </tr>
                                    @endforeach    
                                </table>
                            </div>
                            @endif    
                        </div>

                        <div class="col-md-12" style="padding: 1px;">
                            <label for="thistoria_clinica" class="control-label">{{$agenda->fechaini}}</label>
                            <div id="thistoria_clinica" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $evolucion->cuadro_clinico ?>@endif</div>
                            <input type="hidden" name="historia_clinica" id="historia_clinica">
                        </div>
                            
                    </form>
                       
                </div>
            </div>
            
        </div>
        <div class="col-md-6" style="padding-left: 6px;">
            
                <div class="box box-primary">
                    <div class="box-body">
                        
                        <div class="col-md-4" style="padding: 1px;">
                            <label class="control-label">@lang('agenda.evolucion-')</label>
                        </div>
                        <div class="col-md-1" style="padding: 1px;">
                            @if(!is_null($protocolo_ant))
                            <a href="{{route('consulta.consulta_sig_ant',['id' => $protocolo_ant->id, 'agenda_hoy' => $agenda_hoy])}}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-backward"></span></a>
                            @endif
                        </div>
                        <div class="col-md-1" style="padding: 1px;">
                            <a href="{{ route('agenda.detalle', ['id' => $agenda_hoy])}}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-check"></span></a>
                        </div>
                        <div class="col-md-1" style="padding: 1px;">
                            @if(!is_null($protocolo_des))
                            <a href="{{route('consulta.consulta_sig_ant',['id' => $protocolo_des->id, 'agenda_hoy' => $agenda_hoy])}}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-forward"></span></a>
                            @endif
                        </div>

                        @if($no_admin)
                        <div class="col-md-4 callout callout-warning" style="padding-top: 2px;padding-bottom: 2px;padding-left: 0px;padding-right: 0px;margin-bottom: 5px;">
                            <span >**@lang('agenda.pacientenoingresado-')</span>
                        </div>
                        @endif
                        <div class="col-md-12" style="padding: 1px;">
                           
                        </div>
                            
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="hc_fecha" class="control-label">@lang('agenda.fecha')</label>
                            <input id="hc_fecha" type="text" name="hc_fecha" class="form-control input-sm" value="{{substr($agenda->fechaini,0,10)}}" style="font-size: 14px;font-weight: bold;" readonly>
                        </div>
                           
                        <div class="col-md-5" style="padding: 1px;">
                            <label for="tipo" class="control-label">@lang('agenda.tipo')</label>
                            <input id="tipo" type="text" name="tipo" value=@if($agenda->proc_consul=='0')'CONSULTA'@elseif($agenda->proc_consul=='1')'PROCEDIMIENTO'@endif class="form-control input-sm" readonly>
                        </div>
                        <div class="col-md-2" style="padding: 1px;">
                            <span style="color: Cornsilk;">@if(!is_null($protocolo)){{$protocolo->protocolo}}@endif</span>
                        </div>    
                        
                        
                        <div class="col-md-12" style="padding: 1px;">
                            
                        </div>
                                                            
                        <form id="frm_evol"> 
                            <input type="hidden" name="protocolo" value=@if(!is_null($protocolo))"{{$protocolo->protocolo}}"@else "" @endif>
                            <input type="hidden" name="id_hc_procedimientos" value=@if(!is_null($protocolo))"{{$protocolo->id_hc_procedimientos}}"@else "" @endif>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="motivo" class="control-label">@lang('agenda.motivo')</label>
                                <textarea name="motivo" style="width: 100%;" rows="3" onchange="guardar_protocolo();" @if(is_null($protocolo))readonly="yes"@endif>@if(!is_null($protocolo)){{$protocolo->motivo}}@endif</textarea>
                            </div>
                            <div class="col-md-12" style="padding: 1px;">
                                <label for="proc_com" class="control-label">@lang('agenda.procedimiento')</label>
                                @if($agenda->proc_consul=='1')
                                <select class="form-control input-sm select2"  name="proc_com" onchange="Carga_proc();" required>
                                    <option value="">@lang('agenda.seleccione') ...</option> 
                                    @if(!is_null($protocolo))
                                    @foreach($proc_completo as $value)    
                                        <option @if(!is_null($protocolo))@if($value->id == $protocolo->id_procedimiento_completo) selected @endif @endif value="{{$value->id}}">{{$value->nombre_general}}</option>
                                    @endforeach 
                                    @endif   
                                </select>
                                @elseif($agenda->proc_consul=='0')
                                <input id="proc_com" type="text" name="proc_com" class="form-control input-sm" value="@if(!is_null($protocolo)){{$proc_completo->where('id',$protocolo->id_procedimiento_completo)->first()->nombre_general}}@endif" readonly>
                                @endif
                            </div>
                            
                            
                            <div class="col-md-12" style="padding: 1px;">
                                <label for="hallazgos" class="control-label">@lang('agenda.descripcionHallazgos')</label>
                                <textarea name="hallazgos" id="hallazgos" style="width: 100%;" rows="12" onchange="guardar_protocolo();" >@if(!is_null($protocolo)){{$protocolo->hallazgos}}@endif</textarea>
                            </div>

                        </form>    
                        
                            <input type="hidden" name="codigo" id="codigo">

                            <label for="cie10" class="col-md-12 control-label"><b>@lang('agenda.diagnostico')</b></label>
                            <div class="form-group col-md-10" style="padding: 1px;">
                                <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="@lang('hc_admision.diagnostico')" @if(is_null($protocolo))readonly="yes"@endif>
                            </div>
                            <button id="bagregar" class="btn btn-success btn-sm col-md-2"><span class="glyphicon glyphicon-plus"> @lang('agenda.agregar')</span></button>

                            <div class="form-group col-md-12" style="padding: 1px;">
                                <table id="tdiagnostico" class="table table-striped">
                                    
                                </table>
                            </div>
                            @if(!is_null($protocolo))
                            <div class="form-group col-md-12"> 
                                <a href="{{route('receta.receta', ['hcid' => $protocolo->hcid])}}" class="col-md-offset-10 btn btn-success btn-sm col-md-2"><span> @lang('agenda.receta')</span></a>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="rp" class="control-label">RP</label>
                                <textarea name="rp" style="width: 100%;" readonly="readonly" rows="4" onchange="guardar();">{{$protocolo->rp}}</textarea>
                            </div>
                            @endif

                        
                            
                            
                    </div>
                </div>
        
        </div>
    </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    
    $(document).ready(function() {
        var edad;
        edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>');
        $('#edad').val( edad );
        
        //cargar_historia();
        @if(!is_null($protocolo))
        cargar_tabla();
            
        @endif

    });

    @if(!is_null($protocolo))
    function cargar_tabla(){
        $.ajax({
                url:"{{route('epicrisis.cargar',['id' => $protocolo->id_hc_procedimientos])}}",
                dataType: "json",
                type: 'get',
                success: function(data){
                    //console.log(data);
                    var table = document.getElementById("tdiagnostico");

                    $.each(data, function (index, value) {
                        
                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;
                        //alert(value.cie10);
                        //console.log(row);
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = value.cie10;
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.descripcion;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="" data-original-title="Eliminar"></span></a>';
                        //alert(index);                       
                    });

                }
            })    
    }
    @endif

    function eliminar(id_h){

        
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        
        document.getElementById("tdiagnostico").deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',
          
          success: function(data){
            //console.log(data);
            //cargar_tabla();
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    $('.select2').select2({
            tags: false
        });

    

    tinymce.init({
        selector: '#thistoria_clinica',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        @if(is_null($protocolo))
        readonly: 1,
        @endif
        @if(!is_null($evolucion))
        @if($evolucion->cuadro_clinico==' ')
        setup: function (editor) {
            editor.on('init', function (e) {
                
                var sexo = $('#sexo').val();
                if(sexo=='1') {var tsexo='MASCULINO';}
                else if(sexo=='2') {var tsexo='FEMENINO';}
                var hc = "PACIENTE "+tsexo+" DE "+$('#edad').val()+" AÑOS DE EDAD ACUDE CON ORDEN DEL "+$('#snombre').val()+" PARA REALIZACIÓN DE: <br>AP: "+$('#antecedentes_pat').val()+"<br>AQX: "+$('#antecedentes_quir').val()+"<br>APF : "+$('#antecedentes_fam').val()+"<br>ALERGIAS: "+$('#alergias').val()+"<br>DETALLE:";
                $('#historia_clinica').val(hc);
                tinyMCE.get('thistoria_clinica').setContent(hc);

            
                });
        },
        @endif
        @endif

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica');
                $("#historia_clinica").val(ed.getContent());
                guardar_evolucion(); 
              
            });
          }
      });
 
    tinymce.init({
        selector: '#hallazgos',
        menubar: false,
        content_style: ".mce-content-body {font-size:13px;}",
        @if(is_null($protocolo))
        readonly: 1,
        @endif
        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('hallazgos');
                $("#hallazgos").val(ed.getContent());
                guardar_protocolo(); 
              
            });
          }
      });



    function guardar(){

        $.ajax({
          type: 'post',
          url:"{{route('admision_datos.doctor')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            //alert(edad);
            $('#edad').val( edad );
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    

    function guardar_protocolo(){

        $.ajax({
          type: 'post',
          url:"{{route('consulta.actualizar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            //console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    function guardar_evolucion(){

        $.ajax({
          type: 'post',
          url:"{{route('consulta.actualiza_historia')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //alert(data);
            //console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){
             //console.log(data);
          }
        });

    }


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
                    //console.log(data);
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
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo').val(data.id);
                    guardar_cie10();
                    
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });

    $('#bagregar').click( function(){
        $('#codigo').val('');
        $('#cie10').val('');     
    });

    @if(!is_null($protocolo))
    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': null, 'hcid': {{$protocolo->hcid}}, 'hc_id_procedimiento': {{$protocolo->id_hc_procedimientos}}, 'in_eg': null },
            success: function(data){
                
                //console.log(data);
                
                var indexr = data.count-1 
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                var cell1 = row.insertCell(0);
                cell1.innerHTML = data.cie10;
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = '<a href="javascript:eliminar('+data.id+', this);" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="" data-original-title="Eliminar"></span></a>';

                    /*$.each(data, function (index, value) {
                        
                        
                        alert(value.cie10);
                        console.log(row);
                       

                        
                        //alert(index);
                       
 
                    });*/
               
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }
    @endif

    function Carga_proc(){
        $.ajax({
          type: 'post',
          url:"{{route('procedimiento.tecnica')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            //console.log(data);
            //var edad;
            //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            //edad = calcularEdad(fecha_nacimiento);
            if(data.tecnica_quirurgica!=null){
                var tecnica = data.tecnica_quirurgica;
            }else{
                var tecnica = "";
            }
            $('#hallazgos').val(tecnica);
            tinyMCE.activeEditor.setContent(tecnica);
            //alert(data);
            guardar_protocolo();
            //tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, data )
          },
          error: function(data){
             //console.log(data);
          }
        });

        //guardar();
    }

    function actualiza(e){
    cortesia = document.getElementById("cortesia").value;
    
    if (cortesia == "SI"){

        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 1])}}";

    }
    else if(cortesia == "NO"){
        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 0])}}";
    }

}  
    
    /*f
    unction cargar_historia()
    {
        $.ajax({
        type: 'get',
        url:'{{route('consulta.evolucion',['hcid' => $agenda->hcid])}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            console.log(data);
            if(data=='error'){
                alert("NO SE PUEDE DAR DE ALTA A PACIENTE, TIENE PENDIENTE EXÁMENES");    
            }
            
               
            var unix =  Math.round(new Date(fecha).getTime()/1000);
            location.href='{{route('pentax.pentax')}}/buscar/'+unix;
            //location.href='{{route('pentax.pentax')}}';
        },
        error: function(data){
            
            if(data.responseJSON.id_doctor1!=null){
                $(".cl_doctor").addClass("has-error");
                $('#str_doctor').empty().html(data.responseJSON.id_doctor1);
            }
            if(data.responseJSON.proc!=null){
                $(".cl_proc").addClass("has-error");
                $('#str_proc').empty().html(data.responseJSON.proc);
            }
            if(data.responseJSON.observacion!=null){
                $(".cl_obs").addClass("has-error");
                $('#str_obs').empty().html(data.responseJSON.observacion);
            }
            if(data.responseJSON.id_subseguro!=null){
                $(".cl_sub").addClass("has-error");
                $('#str_sub').empty().html(data.responseJSON.id_subseguro);
            }
        }
    })

    }*/


</script>                     
                           

                        
                           

                       

                                           



	
</section>

@include('sweet::alert')
@endsection
