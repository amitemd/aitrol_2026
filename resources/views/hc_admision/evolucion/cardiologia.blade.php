@extends('hc_admision.visita.base')
@section('action-content')

<style type="text/css">
    
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
</style>
 
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr> @php $paciente = $evolucion->historiaclinica->paciente; @endphp
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$paciente->id}}</td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        
        @php $agenda = $evolucion->historiaclinica->agenda;@endphp
        <div class="col-md-12" style="padding-right: 6px;">
            @php $dia =  Date('N',strtotime($agenda->fechaini));
                 $mes =  Date('n',strtotime($agenda->fechaini)); 
            @endphp 
 
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-body" style="padding: 5px;">  
                    <div class="col-md-12" style="padding: 1px;">

                            
                            
                            <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                                <div class="col-md-7">
                                    <b>Fecha Visita: </b>@if($agenda->proc_consul ==0 )
                                    @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}} <b>Hora: <input type="hidden" value="{{$agenda->fechaini}}" name="fecha_doctor"></b>{{substr($agenda->fechaini,10,10)}}
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <b>FORMULARIO 007</b>
                                </div>
                            </div>


                            
                           
                            
                            <div class="col-md-6">
                                <h4>ULTIMAS EVOLUCIONES</h4>
                                <table class="table table-striped">
                                    <thead>
                                        <th>Fecha</th>
                                        <th>Doctor</th>
                                        <th>Especialidad</th>
                                        <th>Acción</th>
                                    </thead>
                                    <tbody>
                                        @foreach($historiaclinica as $h)
                                            <tr>
                                                <td id="{{$h->id}}-{{$h->proc_consul}}-{{$h->secuencia}}">{{substr($h->created_at,0,10)}}</td>
                                                <td>{{$h->apellido1}} {{$h->apellido2}} {{$h->nombre1}}</td>
                                                <td>{{$h->especialidad}}</td>
                                                <td><button id="btn{{$h->id}}" @if($h->id == $cardio->id_evo_orden ) class="btn btn-danger btn-xs" @else class="btn btn-success btn-xs" @endif onclick="carga_evolucion({{$h->id}})"><i class="glyphicon glyphicon-ok-sign"></i></button></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="font-size:12px; font-weight:bold;">
                                                <?php
                                                    // Eliminar etiquetas HTML
                                                    $texto_plano = strip_tags($h->cuadro_clinico);                                                
                                                    // Cortar los primeros 30 caracteres
                                                    $texto_cortado = substr($texto_plano, 0, 150);
                                                    // Mostrar el texto cortado
                                                    echo $texto_cortado;
                                                ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table> 
                                
                                <form id="frm_evol1"> 
                                    <input type="hidden" name="cardioid" value="{{$cardio->id}}">  
                                    <input type="hidden" name="id_evo_orden" id="id_evo_orden" value="{{$cardio->id_evo_orden}}">  
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="id_doctor_examinador" class="control-label">Medico Examinador</label>
                                        <select onchange="guardar_cardiologia();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador">
                                            @foreach($doctores as $value)
                                                <option @if($cardio->id_drsol == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="thistoria_clinica_ev" class="control-label">CUADRO CLÍNICO ACTUAL</label>
                                        <div id="thistoria_clinica_ev" style="border: solid 1px;"><?php echo $cardio->cuadro_actual; ?></div>
                                        <input type="hidden" name="historia_clinica_ev" id="historia_clinica_ev">
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="tresultado_ev" class="control-label">RESULTADOS DE EXAMENES Y PROCEDIMIENTOS DIAGNOSTICOS</label>
                                        <div id="tresultado_ev" style="border: solid 1px;"><?php echo $cardio->resultados; ?></div>
                                        <input type="hidden" name="resultado_ev" id="resultado_ev">
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="examenes_realizar" class="control-label">PLANES TERAPEUTICOS  Y EDUCACIONALES REALIZADOS</label>
                                        <textarea name="examenes_realizar" id="examenes_realizar" style="width: 100%;" rows="2" onchange="guardar_cardiologia();" >{{$cardio->examenes_realizar}}</textarea>
                                    </div>
                                </form>    
                                                                 
                                <input type="hidden" name="codigo_ev" id="codigo_ev">

                                <label for="cie10_ev" class="col-md-12 control-label" style="padding-left: 0px; "><b>DIAGNOSTICO</b></label>
                                <div class="form-group col-md-10" style="padding: 1px; ">
                                    <input id="cie10_ev" type="text" class="form-control input-sm"  name="cie10_ev" value="{{old('cie10_ev')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Diagnóstico" >
                                </div>

                                <div class="form-group col-md-2" style="padding: 1px;">
                                    <select id="pre_def_ev" name="pre_def_ev" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                                        <option value="DEFINITIVO">DEFINITIVO</option>   
                                    </select> 
                                </div>
                                 
                                <button id="bagregar_ev" class="btn btn-success btn-sm col-md-2" ><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                
                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                    <table id="tdiagnostico_ev" class="table table-striped" style="font-size: 12px;">
                                        
                                    </table>
                                </div>
                                   
                            </div>

                            <div class="col-md-6">    
                                
                                <form id="frm_evol2">

                                    <input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
                                    <input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">

                                    @php
                                        $cardiologia = DB::table('hc_cardio')->where('hcid',$evolucion->hcid)->first();
                                    @endphp    
                                    <div class="col-md-12" style="padding: 1px;"> 
                                        <label for="fecha" class="control-label col-md-4" style="padding: 0;">FECHA FORMATO</label>   
                                        <div class="input-group date" style="">
                                            <div class="input-group-addon" id="dt1">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" value="@if(!is_null($cardiologia->fecha_formato)){{$cardiologia->fecha_formato}}@else{{$agenda->fechaini}}@endif" name="fecha_formato" class="form-control input-sm" id="fecha_formato" onchange="guardar_cardio()">
                                        </div> 
                                    </div>    

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="thistoria_clinica" class="control-label">CUADRO CLÍNICO DE INTERCONSULTA</label>
                                        <div id="thistoria_clinica" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $evolucion->cuadro_clinico ?>@endif</div>
                                        <input type="hidden" name="historia_clinica" id="historia_clinica">
                                    </div> 


                                    @if($agenda->espid=='8')
                                        <div class="col-md-12" style="padding: 1px;">
                                            <label for="resumen" class="control-label">RESUMEN DEL CRITERIO CLINICO</label>
                                            <textarea name="resumen" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
                                        </div>
                                        <div class="col-md-12" style="padding: 1px;">
                                            <label for="plan_diagnostico" class="control-label">PLAN DIAGNOSTICO PROPUESTO</label>
                                            <textarea name="plan_diagnostico" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
                                        </div>
                                        <div class="col-md-12" style="padding: 1px;">
                                            <label for="plan_tratamiento" class="control-label">PLAN DE TRATAMIENTO PROPUESTO</label>
                                            <textarea name="plan_tratamiento" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
                                        </div>
                                    @endif  

                                    

                                </form>   

                                <input type="hidden" name="codigo" id="codigo">

                                <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px; @if($agenda->proc_consul=='1') display: none; @endif"><b>DIAGNOSTICO</b></label>
                                <div class="form-group col-md-10" style="padding: 1px; @if($agenda->proc_consul=='1') display: none; @endif">
                                    <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="form-group col-md-2" style="padding: 1px;">
                                    <select id="pre_def" name="pre_def" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                                        <option value="DEFINITIVO">DEFINITIVO</option>   
                                    </select> 
                                </div>
                                @if($agenda->estado_cita=='4') 
                                <button id="bagregar" class="btn btn-success btn-sm col-md-2" style="@if($agenda->proc_consul=='1') display: none; @endif"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                @endif
                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                    <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">
                                        
                                    </table>
                                </div> 

                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">

                                    <a class="btn btn-primary" href="{{route('formato007.imprimir',['id' => $evolucion->id])}}" target="_blank">Formato 007</a>

                                </div>    
                                
                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">

                                    <a class="btn btn-primary" href="{{route('formulario007.formulario007_unificado',['id' => $evolucion->historiaclinica->hcid])}}" target="_blank">Nuevo Formato 007</a>

                                </div>    
                               
                            </div> 

                             
                    </div>
                </div>    
            </div>    
        </div>
          
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha_formato').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
            }).on('dp.change', function (e) { 
                guardar_cardio()});
    });

    $('#dt1').on('click', function(){
            $('#fecha_formato').datetimepicker('show');
    });


    function carga_evolucion(id){

        $.ajax({
            url:"{{url('cardio/evolucion')}}/"+id+"/{{$cardio->id}}",
            dataType: "json",
            type: 'get',
            success: function(data){
                let orden_ant = $('#id_evo_orden').val();
                $('#btn'+ orden_ant).removeClass('btn-danger');
                $('#btn'+ orden_ant).addClass('btn-success');
                console.log(data);
                $('#id_evo_orden').val(data.evolucion.id);
                $('#btn'+ data.evolucion.id).removeClass('btn-success');
                $('#btn'+ data.evolucion.id).addClass('btn-danger');
                console.log($('#id_doctor_examinador'));
                $('#id_doctor_examinador').val(data.historia.id_doctor1).change();
                console.log($('#id_doctor_examinador'));
                /*anterior = tinyMCE.get('thistoria_clinica_ev').getContent();*/
                anterior = '';
                tinyMCE.get('thistoria_clinica_ev').setContent(anterior+'\n'+data.evolucion.cuadro_clinico);
                $("#historia_clinica_ev").val(anterior+'\n'+data.evolucion.cuadro_clinico);

                /*anterior = tinyMCE.get('tresultado_ev').getContent();*/
                if(data.evolucion.resultado != null){
                    tinyMCE.get('tresultado_ev').setContent(anterior+'\n'+data.evolucion.resultado);
                    $("#resultado_ev").val(anterior+'\n'+data.evolucion.resultado);
                }    

                if(data.historia.examenes_realizar != null){
                    /*anterior = $('#examenes_realizar').val();*/
                    nuevo = anterior+'\n'+data.historia.examenes_realizar;

                    $('#examenes_realizar').val(nuevo);
                }    
                guardar_cardiologia(); 

                cargar_cie10_cardio();


            }
        })

    
    }

    function guardar_cardiologia(){
        
        $.ajax({
          type: 'post',
          url:"{{route('evolucion.actu_cardio')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol1").serialize(),
          success: function(data){
            console.log(data);
            
          },
          error: function(data){
             console.log(data);
          }
        });


    }
    
    function datos_child_pugh(){
        dato1 = parseInt($('#ascitis').val());
        dato2 = parseInt($('#albumina').val());
        dato3 = parseInt($('#encefalopatia').val());
        dato4 = parseInt($('#bilirrubina').val());
        dato5 = parseInt($('#inr').val());
        cantidad = dato1+ dato2+dato3+dato4+dato5;
        $('#puntaje').val(cantidad);
        if(cantidad >= 5 && cantidad<=6){
            $('#clase').val('A');
            $('#sv1').val('100%');
            $('#sv2').val('85%');
        }else if(cantidad >= 7 && cantidad<=9){
            $('#clase').val('B');
            $('#sv1').val('80%');
            $('#sv2').val('60%');
        }else if(cantidad >= 10 && cantidad<=15){
            $('#clase').val('C');
            $('#sv1').val('45%');
            $('#sv2').val('35%');
        }
    }

    function guardar_protocolo(){
        calcular_indice();
        datos_child_pugh();
        $.ajax({
          type: 'post',
          url:"{{route('consulta.actualizar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function guardar_ev(){

        $.ajax({
          type: 'post',
          url:"{{route('cardio.actualizar_evolucion')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol2").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });

    }

    function guardar_obs(){
        $.ajax({
          type: 'post',
          url:"{{route('visita.actualiza2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    tinymce.init({
        selector: '#hallazgos',
        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
              ajaxSave();  
            });
          }
      });

    function ajaxSave() {
        var ed = tinyMCE.get('hallazgos');
        $("#hallazgos").val(ed.getContent());
        guardar();
    }

    function guardar2(){
        $.ajax({
          type: 'post',
          url:"{{route('visita.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function calcular_indice(){
        var peso =  document.getElementById('peso').value;
        var estatura = document.getElementById('estatura').value;
        var sexo = @if($agenda->sexo == 1){{$agenda->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$agenda->fecha_nacimiento}}');
        estatura2 = Math.pow((estatura/100), 2);
        peso_ideal = 21.45 * (estatura2);
        imc = peso/estatura2;
        gct = ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4);
        var texto = "";
        if(imc < 16){
            texto = "Desnutrición";
        }
        else if(imc < 18){
            texto = "Bajo de Peso";
        }
        else if(imc < 25){
            texto = "Normal";
        } 
        else if(imc < 27){
            texto = "Sobrepeso";
        }
        else if(imc < 30){
            texto = "Obesidad Tipo 1";
        }
        else if(imc < 40){
            texto = "Obesidad Clinica";
        }
        else{
            texto = "Obesidad Mordida";
        }
        $('#cimc').val(texto);
        $('#gct').val(gct.toFixed(2));
        $('#imc').val(imc.toFixed(2));
        $('#peso_ideal').val(peso_ideal.toFixed(2));
    }

    $(document).ready(function() {
        var edad;
        //index();
        datos_child_pugh();
        edad = calcularEdad('');
        $('#edad').val( edad );
        //cargar_historia();
        //calcular_indice();
        @if(!is_null($evolucion))
        cargar_tabla();
        cargar_cie10_cardio();

            
        @endif 

        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');
    });

    
    function cargar_cie10_cardio(){
        
        $.ajax({
            url:"{{route('cardio.cargar_cie10',['id' => $cardio->id])}}",
            dataType: "json",
            type: 'get',
            success: function(data){
                //console.log(data);
                var table = document.getElementById("tdiagnostico_ev");
                $('#tdiagnostico_ev tr').remove();
                $.each(data, function (iterador, value) {
                    
                    var row = table.insertRow(0);
                    row.id = 'tdiag_ev'+iterador;
                    //alert(value.cie10);
                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>'+value.cie10+'</b>';

                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = value.descripcion;

                    var vpre_def = '';
                    if(value.pre_def!=null){
                        vpre_def = value.pre_def;
                    }
                    //alert(vpre_def);
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = vpre_def;

                    var cell4 = row.insertCell(3);
                    cell4.innerHTML = '<a href="javascript:eliminar_ev('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
                                          
                });

            }
        })    
    }

     @if(!is_null($evolucion))
        function cargar_tabla(){
            $.ajax({
                url:"{{route('epicrisis.cargar',['id' => $evolucion->hc_id_procedimiento])}}",
                dataType: "json",
                type: 'get',
                success: function(data){
                    
                    var table = document.getElementById("tdiagnostico");

                    $.each(data, function (index, value) {
                        
                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;
                       
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+value.cie10+'</b>';

                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.descripcion;

                        var vpre_def = '';
                        if(value.pre_def!=null){
                            vpre_def = value.pre_def;
                        }
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = vpre_def;

                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                                           
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

    function eliminar_ev(id_h){

        var i = document.getElementById('tdiag_ev'+id_h).rowIndex;
        
        document.getElementById("tdiagnostico_ev").deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cardio/cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
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
                    
                    
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });

     $("#cie10_ev").autocomplete({
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
        select: function( request, response ) {
            /*console.log("res");console.log(response);  */  
            $.ajax({
                type: 'post',
                url:"{{route('epicrisis.cie10_nombre2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: { 'cie10': response.item.value },
                success: function(data){
                    //console.log(data);
                    if(data!='0'){
                        $('#codigo_ev').val(data.id);
                        
                        
                    }
                    
                },
                error: function(data){
                        //console.log(data);
                    }
            })
        },
    } );

    /*$("#cie10_ev").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo_ev').val(data.id);
                    
                    
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });*/


    $('#bagregar_ev').click( function(e){
         e.preventDefault();
        if($('#cie10_ev').val()!='' ){
            if($('#pre_def_ev').val()!='' ){
                //alert("guardar");
                guardar_cie10_ev();  
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }      
        }else{
            alert("Seleccione CIE10");     
        }    
        $('#codigo_ev').val('');
        $('#cie10_ev').val('');
        $('#pre_def_ev').val(''); 

    });

    

    $('#bagregar').click( function(e){
         e.preventDefault();
        if($('#cie10').val()!='' ){
            if($('#pre_def').val()!='' ){
                //alert("guardar");
                guardar_cie10(); 
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }      
        }else{
            alert("Seleccione CIE10");     
        }    
        $('#codigo').val('');
        $('#cie10').val('');
        $('#pre_def').val(''); 

    });

    @if(!is_null($evolucion))
    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': {{$evolucion->hcid}}, 'hc_id_procedimiento': {{$evolucion->hc_id_procedimiento}}, 'in_eg': null },
            success: function(data){
                

                var indexr = data.count-1 
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;

                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';

                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;

                var vpre_def = '';
                if(data.pre_def!=null){
                    vpre_def = data.pre_def;
                }
                var cell3 = row.insertCell(2);
                cell3.innerHTML = vpre_def;

                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                
               
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }
    @endif

    function guardar_cie10_ev(){

        $.ajax({
            type: 'post',
            url:"{{route('cardio.agregar_cie10_ev')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo_ev").val(), 'pre_def': $("#pre_def_ev").val(), 'cardio': {{$cardio->id}}, 'in_eg': null },
            success: function(data){
                //console.log(data);
                var indexr = data.count-1 
                var table = document.getElementById("tdiagnostico_ev");
                var row = table.insertRow(indexr);
                row.id = 'tdiag_ev'+data.id;

                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';

                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;

                var vpre_def = '';
                if(data.pre_def!=null){
                    vpre_def = data.pre_def;
                }
                var cell3 = row.insertCell(2);
                cell3.innerHTML = vpre_def;

                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar_ev('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }

    tinymce.init({
        selector: '#thistoria_clinica',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thistoria_clinica');
               //alert(ed.getContent());
                $("#historia_clinica").val(ed.getContent());

            });
        },


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica');
                $("#historia_clinica").val(ed.getContent());
                guardar_ev(); 
              
            });
          }
    });

    tinymce.init({
        selector: '#tresultado_ev',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        @if($agenda->estado_cita!='4') 
        readonly: 1,
        @else
        
        
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tresultado_ev');
               //alert(ed.getContent());
                $("#resultado_ev").val(ed.getContent());

            });
        },

        @endif

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tresultado_ev');
                $("#resultado_ev").val(ed.getContent());
                guardar_cardiologia(); 
              
            });
          }
    });

    tinymce.init({
        selector: '#thistoria_clinica_ev',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        
        
        
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thistoria_clinica_ev');
               //alert(ed.getContent());
                $("#historia_clinica_ev").val(ed.getContent());

            });
        },

    

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica_ev');
                $("#historia_clinica_ev").val(ed.getContent());
                guardar_cardiologia(); 
              
            });
          }
    });


    @if($agenda->proc_consul == 1)
        tinymce.init({
            selector: '#tindicaciones',
            inline: true,
            menubar: false,
            content_style: ".mce-content-body {font-size:14px;}",

            @if($agenda->estado_cita!='4') 
            readonly: 1,
            @else
            
            
            setup: function (editor) {
                editor.on('init', function (e) {
                   var ed = tinyMCE.get('tindicaciones');
                   //alert(ed.getContent());
                    $("#indicaciones").val(ed.getContent());

                });
            },

            @endif

            init_instance_callback: function (editor) {
                editor.on('Change', function (e) {
                    var ed = tinyMCE.get('tindicaciones');
                    $("#indicaciones").val(ed.getContent());
                    guardar_protocolo(); 
                  
                });
              }
        });
    @endif

    function guardar_cardio(){


        $.ajax({
          type: 'post',
          url:"{{route('cardio.actualizar_cardio2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_evol2").serialize(),
          success: function(data){
            console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){
            
          }
        });
    }
</script>

<script>
    /*$("#limpiar").click( function(){
        $('#nombre_generico').val(''); 
    });*/
    $("#nombre_generico").autocomplete({
        source: function( request, response ) {
                
            $.ajax({
                url:"{{route('receta.buscar_nombre')}}",
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
    
    $("#nombre_generico").change( function(){
        var variable1; 
        var variable2;
        $.ajax({
            type: 'post',
            url:"{{route('receta.buscar_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#nombre_generico"),
            success: function(data){
                if(data!='0'){
                    //console.log(data);
                    if(data.dieta == 1 ){
                        //anterior = $('#prescripcion').val();
                        anterior = tinyMCE.get('tprescripcion').getContent();
                        //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                        tinyMCE.get('tprescripcion').setContent(anterior+ data.value +': \n' +data.dosis);
                        $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                        cambiar_receta_2();
                    }
                    if(data.dieta == 0){
                        Crear_detalle(data);
                    }
                    $('#nombre_generico').val('');
                }
                    
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });
    $("#prescripcion").change( function(){
       cambiar_receta_2();
    });
    $("#rp").change( function(){
       cambiar_receta_2();
    });


    function cambiar_receta_2(){

        $.ajax({
            type: 'post',
            url:"{{route('receta.update_receta_2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#final_receta").serialize(),
            success: function(data){
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    }
    tinymce.init({
        selector: '#trp',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        
        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('trp');
                $("#rp").val(ed.getContent());
            });
        },
        @endif
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('trp');
                $("#rp").val(ed.getContent());
                cambiar_receta_2(); 
              
            });
          }
    });

    tinymce.init({
        selector: '#tprescripcion',
        inline: true,
        menubar: false,

        content_style: ".mce-content-body {font-size:14px;}",

        
        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tprescripcion');
                $("#prescripcion").val(ed.getContent());
            });
        },
        @endif
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tprescripcion');
                $("#prescripcion").val(ed.getContent());
                cambiar_receta_2(); 
              
            });
          }
    });

    


    function guardar3(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    function guardar2(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.guardar2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form2").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }
    var vartiempo = setInterval(function(){ location.reload(); }, 7201000)
</script>                     
</section>

@include('sweet::alert')
@endsection
