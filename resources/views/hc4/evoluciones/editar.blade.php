
<style type="text/css"> 

    .parent{
        overflow-y:auto;
        overflow-x: hidden;
        /*height: 600px;*/
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
</style>
                            <!-- DIV DE LAS EVOLUCIONES -->
<div id="editar_evol" style=" border-radius: 10px; background-color: white; font-size: 13px; ">
    
    <form id="act_evol" >
        
        <input type="hidden" name="id_paciente" value="{{$evoluciones->id_paciente}}">
        <input type="hidden" name="id"  value="{{$id}}">
        <input type="hidden" name="id_agenda" value="{{$agenda->id}}">
        <div class="col-12">    
            
        <div class="row" style="margin-top: 15px">
            <div class="col-6" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';"> Fecha 
                </span>
            </div>
            <div class="col-6" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';"> Hora 
                </span>
            </div>
            <div class="col-6" style="margin-bottom: 10px">
                @if(!is_null($evoluciones))
                    @php $dia =  Date('N',strtotime($evoluciones->fechaini)); $mes =  Date('n',strtotime($evoluciones->fechaini)); 
                    @endphp
                    <b>
                        @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($evoluciones->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($evoluciones->fechaini,0,4)}}
                    </b> 
                @endif
            </div>
            <div class="col-6" style="margin-bottom: 10px">
                @if(!is_null($evoluciones)) 
                    {{substr($evoluciones->fechaini,10,10)}} 
                @endif
            </div>
            
            <div class="col-12" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">Procedimiento
                </span>
            </div>
            @php
                $procedimiento = \Sis_medico\hc_procedimientos::find($evoluciones->hc_id_procedimiento);
                $nprocedimiento = null;
                $texto = null;
                if(!is_null($procedimiento->id_procedimiento_completo)){
                    $nprocedimiento = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                    $texto =  $nprocedimiento->nombre_general;
                }else{
                    $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
                    $mas = true; 

                    foreach($adicionales as $value2)
                    {
                        if($mas == true){
                         $texto = $texto.$value2->procedimiento->nombre  ;
                         $mas = false; 
                         }
                        else{
                         $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                         }                                          
                    }
                }
            @endphp
            <div class="col-12" style="margin-bottom: 10px">
                <span >{{$texto}}
                </span>
            </div>
            <div class="col-12" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">Motivo
                </span>
            </div>
            <div  class="col-12" style="margin-bottom: 10px">

                <input  style="width: 100%; border: 2px solid #004AC1;" type="text" name="motivo" value="@if(!is_null($evoluciones)){{$evoluciones->motivo}}@endif" >
                
            </div>
            <div class="col-6" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador
                </span>
            </div>
            <div class="col-6" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">Seguro
                </span>
            </div>
            
            @php
                $historia = \Sis_medico\Historiaclinica::find($evoluciones->hcid);
            @endphp
            <div class="col-6" style="margin-bottom: 10px">
                <select style="width: 100%; border: 2px solid #004AC1;" name="med_examinador">
                @if(!is_null($procedimientos))
                    @foreach($doctores as $value)
                        <option @if($procedimientos->id_doctor_examinador == $value->id || $value->id ==  $historia->id_doctor1) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif
                        </option>
                    @endforeach
                @endif  
                </select>
            </div>
            <div class="col-6" style="margin-bottom: 10px">
                <select style="width: 100%; border: 2px solid #004AC1;" name="seguro">
                @if(!is_null($procedimientos))
                    @foreach($seguros as $value)
                        <option @if($procedimientos->id_seguro == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombre}}
                        </option>
                    @endforeach
                @endif
                </select>
            </div>
            <div  class="col-12" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">Observaci&oacute;n
                </span>
            </div>
            
             <div  class="col-12" style="margin-bottom: 10px">
                <input  style="width: 100%; border: 2px solid #004AC1;" type="text" name="observacion" value="@if(!is_null($procedimientos)){{strip_tags($procedimientos->observaciones)}} @endif" id="observacion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?> " >          
            </div>

            <div class="col-12" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">Evoluci&oacute;n
                </span>
            </div>
            <div  class="col-12" style="margin-bottom: 10px">
                <div id="tevolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>" style="width: 100%; border: 2px solid #004AC1; ">
                    @if(!is_null($evoluciones)){{strip_tags($evoluciones->cuadro_clinico)}}
                    @endif
                </div>
                <input type="hidden" name="evolucion" id="evolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>">
            </div> 


            <div class="col-12" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general';">Resultados de Exámenes y Procedimientos Diagnósticos
                </span>
            </div>
            <div  class="col-12" style="margin-bottom: 10px">
                <div id="tresultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>" style="width: 100%; border: 2px solid #004AC1; ">
                    @if(!is_null($evoluciones)){{strip_tags($evoluciones->resultado)}}
                    @endif
                </div>
                <input type="hidden" name="resultado_exam" id="resultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>">
            </div> 


            <div class="col-12" style="margin-bottom: 10px">
                <span style="font-family: 'Helvetica general'; @if($agenda->proc_consul=='1') display: none; @endif ">Diagn&oacute;stico
                </span>
            </div>
            <input type="hidden" name="codigo" id="codigo{{$evoluciones->id}}<?php echo e(date('his')); ?>">
            
                <div class="form-group col-md-7 col-sm-11 col-11" style="padding: 1px;margin-left: 15px;margin-right: 15px; @if($agenda->proc_consul=='1') display: none; @endif ">
                    <input id="cie10{{$evoluciones->id}}<?php echo e(date('his')); ?>" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif >
                </div>

                 <div class="form-group col-md-2" style="padding: 1px; @if($agenda->proc_consul=='1') display: none; @endif">
                    <select id="pre_def{{$evoluciones->id}}<?php echo e(date('his')); ?>" name="pre_def" class="form-control input-sm" >
                        <option value="">Seleccione ...</option>
                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                        <option value="DEFINITIVO">DEFINITIVO</option>   
                    </select> 
                </div>

                @if($agenda->estado_cita=='4') 
                <button id="bagregar{{$evoluciones->id}}<?php echo e(date('his')); ?>" class="btn btn-info btn_ordenes col-md-2 col-sm-11 col-11" style=" margin-left: 10px; margin-right: 10px; width: 100%; height: 100%; font-size: 14px;margin-bottom: 15px; margin-top: 5px; @if($agenda->proc_consul=='1') display: none; @endif">
                    <label class="glyphicon glyphicon-plus"> Agregar
                    </label>
                </button>
                @endif
            
            <div class="form-group col-11" style="padding: 1px;margin-bottom: 0px; margin: 10px">
                <table id="tdiagnostico{{$evoluciones->id}}<?php echo e(date('his')); ?>" class="table table-striped" style="font-size: 12px; ">
                </table>
            </div>
            </div>
            <center>
                <div class="col-4" style="margin-bottom: 15px; text-align: center;">
                    <a class="btn btn-info btn_ordenes"  style="color: white; width: 100%; height: 100%; font-size: 14px" onclick="guardar_evol({{$evoluciones->id}});"> <span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
                    </a>
                </div> 
            </center>   
            
    </form>
</div>   
                        
    

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
cargar_tabla({{$evoluciones->id}});


function guardar_evol(id){
         $.ajax({
            type: 'post',
            url:"{{route('paciente_evolucion_act')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#act_evol").serialize(),
            success: function(data){
                //alert("!! EVOLUCION ACTUALIZADA !!");
                $("#evolucion"+id).html(data);
                //console.log(data);
            },
            error:  function(){
                alert('error al cargar');
            }
        }); 
    }


$("#cie10{{$evoluciones->id}}<?php echo e(date('his')); ?>").autocomplete({
    source: function( request, response ) 
    {
                
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
                
            }
        })
    },
        minLength: 2,
    }); 

$("#cie10{{$evoluciones->id}}<?php echo e(date('his')); ?>").change( function()
{
    $.ajax({
        type: 'post',
        url:"{{route('epicrisis.cie10_nombre2')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#cie10{{$evoluciones->id}}<?php echo e(date('his')); ?>"),
        success: function(data){
            if(data!='0'){
                $('#codigo{{$evoluciones->id}}<?php echo e(date('his')); ?>').val(data.id);
                 //guardar_cie10_evol({{$evoluciones->id}});

            }
        },
        error: function(data){    
            }
    })
});


$('#bagregar{{$evoluciones->id}}{{date("his")}}').click( function(){
    
        if($('#cie10{{$evoluciones->id}}{{date("his")}}').val()!='' ){
            if($('#pre_def{{$evoluciones->id}}{{date("his")}}').val()!='' ){
                //alert("guardar");
                guardar_cie10_evol({{$evoluciones->id}});    
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }      
        }else{
            alert("Seleccione CIE10");     
        }    
        $('#codigo{{$evoluciones->id}}{{date("his")}}').val('');
        $('#cie10{{$evoluciones->id}}{{date("his")}}').val('');
        $('#pre_def{{$evoluciones->id}}{{date("his")}}').val(''); 
  });

function cargar_tabla(id_evol)
{
    $.ajax({
            url:"{{route('epicrisis.cargar',['id' => $evoluciones->hc_id_procedimiento])}}",
            dataType: "json",
            type: 'get',
            success: function(data){
                //console.log(data);
                var table = document.getElementById("tdiagnostico"+id_evol+'{{date("his")}}');

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
                    cell4.innerHTML = '<a href="javascript:eliminar('+id_evol+','+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                                       
                });

            }
        })    
} 

function eliminar(id_evol, id_h)
{
    var i = document.getElementById('tdiag'+id_h).rowIndex;
    document.getElementById("tdiagnostico"+id_evol+'{{date("his")}}').deleteRow(i);

    $.ajax({
      type: 'get',
      url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
      datatype: 'json',
      
      success: function(data){
        
      },
      error: function(data){
         
      }
    });
}

@if(!is_null($evoluciones))
    function guardar_cie10_evol(id_evolucion)
    { 
        $.ajax({
            type: 'post',
            url:"{{route('hc4.evolucion_agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+id_evolucion+'{{date("his")}}').val(), 'pre_def': $("#pre_def"+id_evolucion+'{{date("his")}}').val(), 'hcid': {{$evoluciones->hcid}}, 'hc_id_procedimiento': {{$evoluciones->hc_id_procedimiento}}, 'in_eg': null, 'id_paciente': '{{$evoluciones->id_paciente}}' },
            
            success: function(data){
               //console.log(data);
                 if(data.count>0){

                    var indexr = data.count-1 
                    var table = document.getElementById("tdiagnostico"+id_evolucion+'{{date("his")}}');
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
                    cell4.innerHTML = '<a href="javascript:eliminar('+id_evolucion+','+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
             
                }
            },
            error: function(data){
                    
                }
        })
    }
@endif  


tinymce.init({
        selector: '#tevolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        <?php if(is_null($evoluciones)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tevolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>');
                $("#evolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
            });
        },
        <?php endif; ?>
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tevolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>');
                $("#evolucion<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
              
            });
          }
    });



tinymce.init({
        selector: '#tresultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        <?php if(is_null($evoluciones)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tresultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>');
                $("#resultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
            });
        },
        <?php endif; ?>
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tresultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>');
                $("#resultado_exam<?php echo e($evoluciones->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
              
            });
          }
    });


</script>

