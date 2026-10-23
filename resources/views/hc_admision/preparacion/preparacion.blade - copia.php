
@extends('hc_admision.preparacion.base')

@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>


 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
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
                        <button class="w3-bar-item w3-button tablink w3-red" onclick="location.href = '{{route('preparacion.mostrar',['id' => $agenda->id])}}'">PREPARACIÓN</button>
                        <button class="w3-bar-item w3-button tablink"onclick="location.href = '{{route('anestesiologia.mostrar',['id' => $agenda->id])}}' ">ANESTESIOLOGÍA</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('protocolo.mostrar',['id' => $agenda->id])}}'">PROTOCOLO</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('evolucion.evolucion',['id' => $agenda->id])}}'">EVOLUCIÓN</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('epicrisis.mostrar',['id' => $agenda->id])}}'">EPICRISIS</button>
                    </div>
  
                    <div id="tab1" class="w3-container w3-border city">     
                        <div class="box "> 
                            <div class="box-header with-border">
                                <h4>PREPARACIÓN DEL PACIENTE</h4>
                            </div>
                            <div class="box-body">
                                <form class="form-vertical" role="form" method="POST" action="{{ route('admisiones.update_doctor', ['id' => $paciente->id, 'id_cita' => $hca->id_agenda, 'id_historia' => $hca->hcid ]) }}" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">  
                            
                                    <!--peso-->
                                    <div class="col-md-2{{ $errors->has('peso') ? ' has-error' : '' }}">
                                        <label for="peso" class="col-md-12 control-label">Peso (Kg)</label>
                                        <input id="peso" min=0 type="number" step="any" class="form-control input-sm" name="peso" value=@if(old('peso')!='')"{{old('peso')}}"@elseif($hca->peso!="")"{{ $hca->peso }}" @else "{{0}}" @endif >
                                        @if ($errors->has('peso'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('peso') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <!--altura-->
                                    <div class="col-md-2{{ $errors->has('altura') ? ' has-error' : '' }}">
                                        <label for="altura" class="col-md-12 control-label">Altura (cm)</label>
                                        <input id="altura" min=0 type="number" class="form-control input-sm" name="altura" value=@if(old('altura')!='')"{{old('altura')}}"@elseif($hca->altura!="")"{{ $hca->altura }}" @else "{{0}}" @endif >
                                        @if ($errors->has('altura'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('altura') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <!--temperatura-->
                                    <div class="col-md-2{{ $errors->has('temperatura') ? ' has-error' : '' }}">
                                        <label for="temperatura" class="col-md-12 control-label">Temp. (ºC)</label>
                                        <input id="temperatura" min=0 type="number" step="any" class="form-control input-sm" name="temperatura" value=@if(old('temperatura')!='')"{{old('temperatura')}}"@elseif($hca->temperatura!="")"{{ $hca->temperatura }}" @else "{{0}}" @endif >
                                        @if ($errors->has('temperatura'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('temperatura') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--presion-->
                                    <div class="col-md-2{{ $errors->has('presion') ? ' has-error' : '' }}">
                                        <label for="presion" class="col-md-12 control-label" style="padding: 0px;">Pres.(mm Hg)</label>
                                        <input id="presion" min=0 type="number" step="any" class="form-control input-sm" name="presion" value=@if(old('presion')!='')"{{old('presion')}}"@elseif($hca->presion!="")"{{ $hca->presion }}" @else "{{0}}" @endif >
                                        @if ($errors->has('presion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('presion') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--hijos_vivos-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('hijos_vivos') ? ' has-error' : '' }}">
                                        <label for="hijos_vivos" class="col-md-12 control-label"">Hijos Vivos</label>
                                        <input id="hijos_vivos" min=0 type="number" class="form-control input-sm" name="hijos_vivos" value=@if(old('hijos_vivos')!='')"{{old('hijos_vivos')}}"@elseif($paciente->hijos_vivos!="")"{{ $paciente->hijos_vivos }}" 
                                        @else "{{0}}" @endif required >
                                        @if ($errors->has('hijos_vivos'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('hijos_vivos') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--hijos_muertos-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('hijos_muertos') ? ' has-error' : '' }}">
                                        <label for="hijos_muertos" class="col-md-12 control-label" style="padding: 0;">H. fallecidos</label>
                                        <input id="hijos_muertos" min=0 type="number" class="form-control input-sm" name="hijos_muertos" value=@if(old('hijos_muertos')!='')"{{old('hijos_muertos')}}"
                                        @elseif($paciente->hijos_muertos!="")"{{ $paciente->hijos_muertos }}"@else"{{0}}" @endif required >
                                        @if ($errors->has('hijos_muertos'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('hijos_muertos') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--Grupo Sanguineo-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('gruposanguineo') ? ' has-error' : '' }}">
                                        <label for="gruposanguineo" class="col-md-12 control-label" >G.Sanguíneo</label>
                                        <select id="gruposanguineo" class="form-control input-sm" name="gruposanguineo"  required>
                                            <option value="">Seleccionar ..</option>
                                            <option @if(old('gruposanguineo')=="AB-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "AB-"){{"selected"}}@endif value="AB-">AB-</option>
                                            <option @if(old('gruposanguineo')=="AB+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "AB+"){{"selected"}}@endif value="AB+">AB+</option>
                                            <option @if(old('gruposanguineo')=="A-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "A-"){{"selected"}}@endif value="A-">A-</option>
                                            <option @if(old('gruposanguineo')=="A+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "A+"){{"selected"}}@endif value="A+">A+</option>
                                            <option @if(old('gruposanguineo')=="B-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "B-"){{"selected"}}@endif value="B-">B-</option>
                                            <option @if(old('gruposanguineo')=="B+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "B+"){{"selected"}}@endif value="B+">B+</option>
                                            <option @if(old('gruposanguineo')=="O-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "O-"){{"selected"}}@endif value="O-">O-</option>
                                            <option @if(old('gruposanguineo')=="O+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $paciente->gruposanguineo == "O+"){{"selected"}}@endif value="O+">O+</option>
                                        </select>   
                                        @if ($errors->has('gruposanguineo'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('gruposanguineo') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--transfusion-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('transfusion') ? ' has-error' : '' }}" >
                                        <label for="transfusion" class="col-md-8 control-label">Transfusiones</label>
                                        <select id="transfusion" name="transfusion" class="form-control input-sm"  required >
                                            <option @if(old('transfusion')=="NO"){{"selected"}}@elseif(old('transfusion')=="" && $paciente->transfusion == "NO"){{"selected"}}@endif value="NO">NO</option> 
                                            <option @if(old('transfusion')=="SI"){{"selected"}}@elseif(old('transfusion')=="" && $paciente->transfusion == "SI"){{"selected"}}@endif value="SI">SI</option>
                                        </select>    
                                        @if ($errors->has('transfusion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('transfusion') }}</strong>
                                        </span>
                                        @endif   
                                    </div>
                                    <!--alcohol-->
                                    
                                    <div class="form-group col-md-4{{ $errors->has('alcohol') ? ' has-error' : '' }}">
                                        <label for="alcohol" class="col-md-10 control-label">Consumo Alcohol</label>
                                        <select id="alcohol" class="form-control input-sm" name="alcohol"  required >
                                            <option @if(old('alcohol')=="Nunca"){{"selected"}}@elseif(old('alcohol')=="" && $paciente->alcohol == "Nunca"){{"selected"}}@endif value="Nunca">Nunca</option>
                                            <option @if(old('alcohol')=="1 o menos veces al mes"){{"selected"}}@elseif(old('alcohol')=="" && $paciente->alcohol == "1 o menos veces al mes"){{"selected"}}@endif value="1 o menos veces al mes">1 o menos veces al mes</option>
                                            <option @if(old('alcohol')=="2 o 4 veces al mes"){{"selected"}}@elseif(old('alcohol')=="" && $paciente->alcohol == "2 o 4 veces al mes"){{"selected"}}@endif value="2 o 4 veces al mes">2 o 4 veces al mes</option>
                                            <option @if(old('alcohol')=="2 o 3 veces a la semana"){{"selected"}}@elseif(old('alcohol')=="" && $paciente->alcohol == "2 o 3 veces a la semana"){{"selected"}}@endif value="2 o 3 veces a la semana">2 o 3 veces a la semana</option>
                                            <option @if(old('alcohol')=="4 o más veces a la semana"){{"selected"}}@elseif(old('alcohol')=="" && $paciente->alcohol == "4 o más veces a la semana"){{"selected"}}@endif value="4 o más veces a la semana">4 o más veces a la semana</option>
                                        </select>         
                                        @if ($errors->has('alcohol'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('alcohol') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                           
                                            <!--anticonceptivos-->
                                    
                                    <div class="form-group col-md-4{{ $errors->has('anticonceptivos') ? ' has-error' : '' }}">
                                        <label for="anticonceptivos" class="col-md-8 control-label">Anticonceptivos</label>
                                        <input id="anticonceptivos" type="text" class="form-control input-sm" name="anticonceptivos" value=@if(old('anticonceptivos')!='')"{{old('anticonceptivos')}}"@else"{{ $paciente->anticonceptivos }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                        @if ($errors->has('anticonceptivos'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('anticonceptivos') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                            <!--alergias-->
                                    
                                    <div class="form-group col-md-6{{ $errors->has('alergias') ? ' has-error' : '' }}">
                                        <label for="alergias" class="col-md-8 control-label">Alergias</label>
                                        <textarea rows="2" cols="50" maxlength="255" id="alergias" class="form-control input-sm" name="alergias" >@if(old('alergias')!=''){{old('alergias')}}@else{{ $paciente->alergias }}@endif</textarea>
                                        @if ($errors->has('alergias'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('alergias') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                            <!--vacuna-->
                                    
                                    <div class="form-group col-md-6{{ $errors->has('vacuna') ? ' has-error' : '' }}">
                                        <label for="vacuna" class="col-md-8 control-label">vacuna</label>
                                        <textarea rows="2" cols="50" maxlength="255" id="vacuna" class="form-control input-sm" name="vacuna" >@if(old('vacuna')!=''){{old('vacuna')}}@else{{ $paciente->vacuna }}@endif</textarea>
                                        @if ($errors->has('vacuna'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('vacuna') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                            <!--antecedentes_pat-->
                                    
                                    <div class="form-group col-md-6{{ $errors->has('antecedentes_pat') ? ' has-error' : '' }}"> 
                                        <label for="antecedentes_pat" class="col-md-8 control-label">Antecedentes Patológicos</label>
                                        <textarea rows="4" cols="50" maxlength="300" id="antecedentes_pat" class="form-control input-sm" name="antecedentes_pat" >@if(old('antecedentes_pat')!=''){{old('antecedentes_pat')}}@else{{ $paciente->antecedentes_pat }}@endif</textarea>
                                        @if ($errors->has('antecedentes_pat'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('antecedentes_pat') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                            <!--antecedentes_fam-->
                                    
                                    <div class="form-group col-md-6{{ $errors->has('antecedentes_fam') ? ' has-error' : '' }}">                                   
                                        <label for="antecedentes_fam" class="col-md-8 control-label">Antecedentes Familiares</label>
                                            <textarea rows="4" cols="50" maxlength="300" id="antecedentes_fam" class="form-control input-sm" name="antecedentes_fam" >@if(old('antecedentes_fam')!=''){{old('antecedentes_fam')}}@else{{ $paciente->antecedentes_fam }}@endif</textarea>
                                            @if ($errors->has('antecedentes_fam'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('antecedentes_fam') }}</strong>
                                            </span>
                                            @endif    
                                    </div>
                                            <!--antecedentes_quir-->
                                    
                                    <div class="form-group col-md-12{{ $errors->has('antecedentes_quir') ? ' has-error' : '' }}">  
                                        <label for="antecedentes_quir" class="col-md-8 control-label">Antecedentes Quirúrgicos</label>
                                        <textarea rows="2" cols="50" maxlength="300" id="antecedentes_quir" class="form-control input-sm" name="antecedentes_quir"  >@if(old('antecedentes_quir')!=''){{old('antecedentes_quir')}}@else{{ $paciente->antecedentes_quir }}@endif</textarea>
                                        @if ($errors->has('antecedentes_quir'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('antecedentes_quir') }}</strong>
                                        </span>
                                        @endif  
                                    </div>

                                            @if($paciente->sexo==2)
                                            <!--primera_mens-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('primera_mens') ? ' has-error' : '' }}"> 
                                        <label for="primera_mens" class="col-md-12 control-label">E.Menstruación</label>
                                        <input min=0 id="primera_mens" type="number" class="form-control input-sm" name="primera_mens" required value=@if(old('primera_mens')!='')"{{old('primera_mens')}}"@elseif($paciente->primera_mens!="")"{{$paciente->primera_mens}}"@else "{{0}}" @endif >
                                        @if ($errors->has('primera_mens'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('primera_mens') }}</strong>
                                        </span>
                                        @endif    
                                    </div>
                                            <!--menopausia-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('menopausia') ? ' has-error' : '' }}">   
                                        <label for="menopausia" class="col-md-12 control-label">E.Menopausia</label>
                                        <input min=0 id="menopausia" type="number" class="form-control input-sm" name="menopausia" required value=@if(old('menopausia')!='')"{{old('menopausia')}}"@elseif($paciente->menopausia!="")"{{ $paciente->menopausia }}" @else "{{0}}" @endif  >
                                        @if ($errors->has('menopausia'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('menopausia') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                            <!--parto_cesarea-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('parto_cesarea') ? ' has-error' : '' }}">   
                                        <label for="parto_cesarea" class="col-md-12 control-label">P. Cesáreas</label>
                                        <input min=0 id="parto_cesarea" type="number" class="form-control input-sm" name="parto_cesarea" required value=@if(old('parto_cesarea')!='')"{{old('parto_cesarea')}}"@elseif($paciente->parto_cesarea!="")"{{ $paciente->parto_cesarea }}" @else "{{0}}" @endif >
                                        @if ($errors->has('parto_cesarea'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('parto_cesarea') }}</strong>
                                        </span>
                                        @endif    
                                    </div>
                                            <!--parto_normal-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('parto_normal') ? ' has-error' : '' }}">
                                        <label for="parto_normal" class="col-md-12 control-label">P. Normal</label>
                                        <input min=0 id="parto_normal" type="number" class="form-control input-sm" name="parto_normal" required value=@if(old('parto_normal')!='')"{{old('parto_normal')}}"@elseif($paciente->parto_normal!="")"{{ $paciente->parto_normal }}" @else "{{0}}" @endif >
                                            @if ($errors->has('parto_normal'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('parto_normal') }}</strong>
                                        </span>
                                        @endif 
                                    </div>
                                            <!--aborto-->
                                    
                                    <div class="form-group col-md-2{{ $errors->has('aborto') ? ' has-error' : '' }}">  
                                        <label for="aborto" class="col-md-12 control-label">Abortos</label>
                                        <input min=0 id="aborto" type="number" class="form-control input-sm" name="aborto" required value=@if(old('aborto')!='')"{{old('aborto')}}"@elseif($paciente->aborto!="")"{{ $paciente->aborto }}" @else "{{0}}" @endif >
                                                @if ($errors->has('aborto'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('aborto') }}</strong>
                                        </span>
                                        @endif 
                                    </div>
                                            @endif
                                    
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

        
<?php /*
        <!--div class="col-md-12">
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h4>Fotos del Procedimiento</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-10">
                        <div class="panel panel-default">
                            <div class="panel-heading">Listado de Fotos</div>
                            <div class="panel-body">
                                <div class="row">
                                @foreach($fotos as $thumbnail)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="thumbnail">
                                            <a href="{{ route('procedimiento.imagen', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                <img src="{{asset($thumbnail->ruta.$thumbnail->archivo)}}" style="width: 120px; height: 120px;" alt="{{$thumbnail->tipo_documento}}">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form method="POST" action="{{route('historiaclinica.fotos')}}" class="dropzone" id="addimage"> 
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="id" value="{{ $hca->hcid }}"> 
                            <input type="hidden" name="paciente" value="{{$agenda->id_paciente}}"> 
                        </form>
                    </div>                   
                </div>

            </div>
        </div--> 

 */ ?>              

        
         
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

</script>

@include('sweet::alert')
@endsection

