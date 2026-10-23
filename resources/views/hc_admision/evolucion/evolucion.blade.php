
<?php function CalculaEdad( $fecha ) {
    list($Y,$m,$d) = explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}?>

<style type="text/css">
    
    .mce-branding{
        display: none;
    }    

</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">


<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;"><b>Notas de Evolución</b></h4>
</div>
<div class="modal-body"> 
    <div class="box-body">
    <div class="form-group col-md-7">
        <form method="POST" action="#" id="form" >
        
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="hcid" value="{{$hcid}}">
            <input type="hidden" name="hc_id_procedimiento" value="{{$id}}">
            <input type="hidden" name="secuencia" value="{{$evoluciones->count() + 1}}">
            <input type="hidden" id="id_evolucion2" name="id_evolucion" value="@if($evolucion!=null){{$evolucion->id}}@endif"> 

            <?php $sexo=''; $cuadro=''; 
                if($paciente->sexo=='1') {$sexo='MASCULINO';} elseif($paciente->sexo=='2') {$sexo='FEMENINO';}
                if($evolucion!='0' && $evoluciones->count()=='0' ){
                    $cuadro= "PACIENTE ".$sexo." DE ".CalculaEdad($paciente->fecha_nacimiento)." AÑOS DE EDAD ACUDE CON ORDEN DEL ".$seguro->nombre." PARA REALIZACIÓN DE: <br>AP: ".$paciente->antecedentes_pat."<br>AQX: ".$paciente->antecedentes_quir."<br>APF : ".$paciente->antecedentes_fam."<br>ALERGIA: ".$paciente->alergias."<br>DETALLE:";     
                }
                                                 
            ?>

            <div class="form-group col-md-12 cl_cuadro" style="color: orange;">***Primero ingrese el cuadro clinico y/o laboratorio, luego presione guardar</div> 

            <div class="form-group col-md-6 cl_inicio" >
                <label class="col-md-12 control-label">Ingreso</label>
                <div class="col-md-12">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="@if(old('inicio')!=''){{old('inicio')}}@elseif($evolucion!=null){{$evolucion->fecha_ingreso}}@endif" name="inicio" class="form-control pull-right input-sm" id="inicio" required onchange="incremento(event)">
                    </div>
                    
                    <span class="help-block" >
                        <strong id="str_inicio"></strong>
                    </span>
                       
                </div>
            </div>
        
            <div class="form-group col-md-12 cl_cuadro"><label class="col-md-12 control-label"><b>CUADRO CLÍNICO</b></label></div> 
            <div class="form-group col-md-12 cl_cuadro">    
                <textarea rows="4" cols="100" maxlength="1000" id="cuadro_clinico" class="form-control input-sm" name="cuadro_clinico">@if(old('cuadro_clinico')!=''){{old('cuadro_clinico')}}@elseif($evolucion!=null){{$evolucion->cuadro_clinico}}@else{{$cuadro}}@endif</textarea>
                <span class="help-block" >
                    <strong id="str_cuadro"></strong>
                </span>    
            </div>

            <div class="form-group col-md-12"><b>LABORATORIO</b></div>
            <div class="form-group col-md-12{{ $errors->has('laboratorio') ? ' has-error' : '' }}">    
                <textarea rows="4" cols="100" maxlength="1000" id="laboratorio" class="form-control input-sm" name="laboratorio"  >@if(old('laboratorio')!=''){{old('laboratorio')}}@elseif($evolucion!=null){{$evolucion->laboratorio}}@endif</textarea>
                @if ($errors->has('laboratorio'))
                <span class="help-block">
                    <strong>{{ $errors->first('laboratorio') }}</strong>
                </span>
                @endif    
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-9">
                    <a href="#" id="confirme" class="btn btn-primary">
                        <span class="glyphicon glyphicon-floppy-disk"> Guardar</span>
                    </a>
                </div>
            </div>
        </form>     
    </div>
    <div class="form-group col-md-5">
        <div class="form-group col-md-12"><b>FARMACOTERAPIA E INDICACIONES</b></div>
        <div class="form-group col-md-12" id="indicaciones">
            <form method="POST" action="#" id="form2" >
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="hcid" value="{{$hcid}}">
                <input type="hidden" id="id_evolucion" name="id_evolucion" value="@if($evolucion!=null){{$evolucion->id}}@endif">

                <div class="form-group col-md-12 cl_indicacion style="padding-left: 0px;">
                    <label for="indicacion" class="col-md-12 control-label">Agregar Indicación</label>
                    <textarea rows="2" cols="100" maxlength="300" id="indicacion" class="form-control input-sm" name="indicacion" >@if(old('indicacion')!=''){{old('indicacion')}}@else{{ $paciente->indicacion }}@endif</textarea>
                    <span class="help-block" >
                        <strong id="str_indicacion"></strong>
                    </span>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-8">
                        <a href="#" id="confirme2" class="btn btn-success">
                            <span class="glyphicon glyphicon-plus"> Agregar</span>
                        </a>
                    </div>
                </div>
            </form>    
        </div>
        <div class="table-responsive col-md-12" id="tabla_indicacion">       
        </div>
    </div>
    </div>
</div>

<div class="modal-footer">
  <button id="btn_cerrar" type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>    
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>

    $(document).ready(function() {

         $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
            });
         

        mostrar_indicaciones();
        var jevolucion = document.getElementById('id_evolucion2').value;
        if(jevolucion==''){
            //alert(jevolucion);
            $('#confirme2').attr("disabled", true);
        }

    });    

    tinymce.init({

        selector: '#cuadro_clinico',
        height: 120,
        menubar: false,
    
        setup:function(ed) {
            ed.on('change', function(e) {
                tinyMCE.triggerSave();
            
            });
            /*ed.on('init', function() 
            {
                this.execCommand("fontName", false, "tahoma");
                this.execCommand("fontSize", false, "12px");
            });*/
        }
    });
  tinymce.init({

    selector: '#laboratorio',
    height: 100,
    menubar: false,
    
    setup:function(ed) {
        ed.on('change', function(e) {
            tinyMCE.triggerSave();
            
        });
        /*ed.on('init', function() 
        {
            this.execCommand("fontName", false, "tahoma");
            this.execCommand("fontSize", false, "12px");
        });  */  


    }
  });

  $('#confirme').click(function(event){

        $.ajax({
            type: 'get',
            url:'{{route('evolucion.crea_actualiza')}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
                //console.log(data);
                $('#id_evolucion').val(data);
                $('#id_evolucion2').val(data);
                alert("Evolución Guardada, Ingrese las indicaciones");
                $('#confirme2').attr("disabled", false);
                
            },
            error: function(data){
                //alert('error');
                console.log(data);
                if(data.responseJSON.inicio!=null){
                    $(".cl_inicio").addClass("has-error");
                    $('#str_inicio').empty().html(data.responseJSON.inicio);
                }
                if(data.responseJSON.cuadro_clinico!=null){
                    $(".cl_cuadro").addClass("has-error");
                    $('#str_cuadro').empty().html(data.responseJSON.cuadro_clinico);
                }
            
            }
        })

    });

    $('#confirme2').click(function(event){

        $.ajax({
            type: 'get',
            url:'{{route('evolucion.crea_indicacion')}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
            datatype: 'json',
            data: $("#form2").serialize(),
            success: function(data){
                $('#indicacion').val('');
                $('#tabla_indicacion').empty().html(data);
                
            },
            error: function(data){
            
                if(data.responseJSON.indicacion!=null){
                    $(".cl_indicacion").addClass("has-error");
                    $('#str_indicacion').empty().html(data.responseJSON.indicacion);
                }
            
            }
        })

    });
    function mostrar_indicaciones(){

            $.ajax({
                type: 'get',
                url:'{{route('evolucion.indicaciones')}}',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
                datatype: 'json',
                data: $("#form2").serialize(),
                success: function(data){
                    if(data!='no'){
                        $('#tabla_indicacion').empty().html(data);    
                    }

                },
                error: function(data){
                }
            })
    }
  </script>
                           