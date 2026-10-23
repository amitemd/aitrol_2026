
@extends('hc_admision.cardiologia.base')

@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>
<style type="text/css">
    
    .mce-branding{
        display: none;
    }    

</style>


 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p></p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                     
                    @php  $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                   
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>

                    @php $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td><b>Paciente:</b></td>
                            <td colspan="3">{{$agenda->id_paciente}} - {{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                            <td><b>Edad:</b></td>
                            <td><span id="edad"></span></td>
                            <td><b>Seguro:</b></td>
                            <td>{{$seguro->nombre}}</td>
                        </tr>                        
                        </tbody>
                    </table>
                    <div class="w3-bar w3-blue">
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}'">PROCEDIMIENTOS</button>
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('preparacion.mostrar',['id' => $agenda->id])}}'">PREPARACIÓN</button>
                        <button class="w3-bar-item w3-button tablink w3-red" onclick="location.href = '{{route('cardiologia.mostrar',['id' => $agenda->id])}}'">CARDIOLOGIA</button>
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('receta.mostrar',['id' => $agenda->id])}}'">RECETA</button>
                    </div>
  
                    <div id="tab1" class="w3-container w3-border city">     
                        <div class="box "> 
                            <div class="box-header with-border">
                                <h4>CARDIOLOGIA</h4>
                            </div>
                            <div class="box-body">
                                <form class="form-vertical" role="form" method="POST" action="{{ route('cardiologia.crea_actualiza')}}" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">  
                            
                                    
                                    <input type="hidden" name="idcardio" value="@if(!is_null($cardio)){{ $cardio->id}}@endif">
                                    <input type="hidden" name="hcid" value="{{ $hca->hcid}}">

                                    <div class="form-group col-md-4{{ $errors->has('id_especialista') ? ' has-error' : '' }}">
                                        <label for="id_especialista" class="col-md-12 control-label" >Cardiologo</label>
                                        <select id="id_especialista" class="form-control input-sm" name="id_especialista"  required >
                                            @foreach($cardiologos as $cardiologo)
                                            <option @if(!is_null($cardio))@if($cardiologo->id==$cardio->id_especialista) selected  @endif @endif value="{{$cardiologo->id}}">Dr. {{$cardiologo->nombre1}} {{$cardiologo->apellido1}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('cardiologo'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('cardiologo') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-12 cl_cuadro"><label class="col-md-12 control-label"><b>CUADRO CLÍNICO</b></label></div>
                                    <div class="form-group col-md-12{{ $errors->has('cuadro_clinico') ? ' has-error' : '' }}">
                                        <textarea rows="4" cols="100" maxlength="1000" id="cuadro_clinico" class="form-control input-sm" name="cuadro_clinico">@if(old('cuadro_clinico')!=''){{old('cuadro_clinico')}}@elseif(!is_null($cardio)){{ $cardio->cuadro_clinico }}@endif</textarea>
                                        @if ($errors->has('cuadro_clinico'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cuadro_clinico') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-12 cl_cuadro"><label class="col-md-12 control-label"><b>RESUMEN</b></label></div>
                                    <div class="form-group col-md-12{{ $errors->has('resumen') ? ' has-error' : '' }}">
                                        <textarea rows="2" cols="50" maxlength="255" id="resumen" class="form-control input-sm" name="resumen" >@if(old('resumen')!=''){{old('resumen')}}@elseif(!is_null($cardio)){{ $cardio->resumen }}@endif</textarea>
                                        @if ($errors->has('resumen'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('resumen') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-12 cl_cuadro"><label class="col-md-12 control-label"><b>PLAN</b></label></div>
                                    <div class="form-group col-md-12{{ $errors->has('plan') ? ' has-error' : '' }}">
                                        <textarea rows="2" cols="50" maxlength="255" id="plan" class="form-control input-sm" name="plan" >@if(old('plan')!=''){{old('plan')}}@elseif(!is_null($cardio)){{ $cardio->plan }}@endif</textarea>
                                        @if ($errors->has('plan'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('plan') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-12 cl_cuadro"><label class="col-md-12 control-label"><b>MOTIVO</b></label></div>
                                    <div class="form-group col-md-12{{ $errors->has('motivo') ? ' has-error' : '' }}">
                                        <textarea rows="2" cols="50" maxlength="255" id="motivo" class="form-control input-sm" name="motivo" >@if(old('motivo')!=''){{old('motivo')}}@elseif(!is_null($cardio)){{ $cardio->motivo }}@endif</textarea>
                                        @if ($errors->has('motivo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('motivo') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    

                                    
                                    
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-9">
                                            <button type="submit" class="btn btn-primary">
                                                    Guardar
                                            </button>
                                        </div>
                                    </div>
                            
                                </form>              
                            </div>
                        </div>
                    </div>

<script>

</script> 
                </div>
            </div>
        </div>  
              

        
         
    </div>
</div>
<script>
 
    $(document).ready(function() {

        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle2',['id' => $agenda->id]) }}"></i> Historia</a></li>');
        $(".breadcrumb").append('<li class="active">Atención</li>');    

        $('#favoritesModal2').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
                
        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>')+ "años";
                
        $('#edad').text( edad );

        $("#hijos_vivos2").val(document.getElementById("hijos_vivos").value);
        $("#hijos_muertos2").val(document.getElementById("hijos_muertos").value);
        $("#gruposanguineo2").val(document.getElementById("gruposanguineo").value);
        $("#transfusion2").val(document.getElementById("transfusion").value);
        $("#alcohol2").val(document.getElementById("alcohol").value);
        $("#alergias2").val(document.getElementById("alergias").value);
        $("#vacuna2").val(document.getElementById("vacuna").value);
        $("#antecedentes_pat2").val(document.getElementById("antecedentes_pat").value);
        $("#antecedentes_fam2").val(document.getElementById("antecedentes_fam").value);
        $("#antecedentes_quir2").val(document.getElementById("antecedentes_quir").value);

        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });


    });

    function openCity(evt, cityName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("city");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " w3-red";
    }

    var drogasadministradas = function ()
    {

        var id_record = document.getElementById('id_record').value;    
        
        //console.log(unix);
        $.ajax({
            type: 'get',
            url:'{{ url('historia/drogasadministradas')}}/'+id_record,//historia.drogasadministradas
            success: function(data){
                console.log(data);
                $('#drogas').empty().html(data);
            }
        })
    
    }

    tinymce.init({

        selector: '#cuadro_clinico',
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
            });*/
        }
    });

    tinymce.init({

        selector: '#resumen',
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
            });*/
        }
    });


    tinymce.init({

        selector: '#plan',
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
            });*/
        }
    });

    tinymce.init({

        selector: '#motivo',
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
            });*/
        }
    });


</script>

@include('sweet::alert')
@endsection

