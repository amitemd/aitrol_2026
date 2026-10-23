
@extends('historiaclinica.base')

@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>


<br>
 
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
                        <button class="w3-bar-item w3-button tablink w3-red" onclick="openCity(event,'tab1')">PREPARACIÓN</button>
                        <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'tab2')">ANESTESIOLOGÍA</button>
                        <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'tab3')">KARDEX</button> 
                        <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'tab4')">EVOLUCIÓN</button>
                        <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'tab5')">TÉCNICAS</button>
                    </div>
  
                    <div id="tab1" class="w3-container w3-border city">     
                        <div class="box "> 
                            <div class="box-header with-border">
                                <h4>PREPARACIÓN DEL PACIENTE</h4>
                            </div>
                            <div class="box-body">
                                <form class="form-vertical" role="form" method="POST" action="{{ route('admisiones.update_doctor', ['id' => $paciente->id, 'id_cita' => $hca[0]->id_agenda, 'id_historia' => $hca[0]->hcid ]) }}" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">  
                            
                                    <!--peso-->
                                    <div class="col-md-2{{ $errors->has('peso') ? ' has-error' : '' }}">
                                        <label for="peso" class="col-md-12 control-label">Peso (Kg)</label>
                                        <input id="peso" min=0 type="number" step="any" class="form-control input-sm" name="peso" value=@if(old('peso')!='')"{{old('peso')}}"@elseif($hca[0]->peso!="")"{{ $hca[0]->peso }}" @else "{{0}}" @endif >
                                        @if ($errors->has('peso'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('peso') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <!--altura-->
                                    <div class="col-md-2{{ $errors->has('altura') ? ' has-error' : '' }}">
                                        <label for="altura" class="col-md-12 control-label">Altura (cm)</label>
                                        <input id="altura" min=0 type="number" class="form-control input-sm" name="altura" value=@if(old('altura')!='')"{{old('altura')}}"@elseif($hca[0]->altura!="")"{{ $hca[0]->altura }}" @else "{{0}}" @endif >
                                        @if ($errors->has('altura'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('altura') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <!--temperatura-->
                                    <div class="col-md-2{{ $errors->has('temperatura') ? ' has-error' : '' }}">
                                        <label for="temperatura" class="col-md-12 control-label">Temp. (ºC)</label>
                                        <input id="temperatura" min=0 type="number" step="any" class="form-control input-sm" name="temperatura" value=@if(old('temperatura')!='')"{{old('temperatura')}}"@elseif($hca[0]->temperatura!="")"{{ $hca[0]->temperatura }}" @else "{{0}}" @endif >
                                        @if ($errors->has('temperatura'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('temperatura') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--presion-->
                                    <div class="col-md-2{{ $errors->has('presion') ? ' has-error' : '' }}">
                                        <label for="presion" class="col-md-12 control-label" style="padding: 0px;">Pres.(mm Hg)</label>
                                        <input id="presion" min=0 type="number" step="any" class="form-control input-sm" name="presion" value=@if(old('presion')!='')"{{old('presion')}}"@elseif($hca[0]->presion!="")"{{ $hca[0]->presion }}" @else "{{0}}" @endif >
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

                    <div id="tab2" class="w3-container w3-border city" style="display:none">
                        <div class="box ">
                            <div class="box-header with-border">
                                <h4>RECORD ANESTÉSICO DEL PACIENTE</h4>                                                                                                                                                                                                                                                
                            </div>
                            <div class="box-body">
                                <form class="form-vertical" role="form" method="POST" action="{{ route('record.anestesiologo', ['hc_id' => $hca[0]->hcid ]) }}" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">  
            
                                    
                                    <!--anestesiologo-->
                                    
                                    <div class="form-group col-md-4{{ $errors->has('id_anestesiologo') ? ' has-error' : '' }}">
                                        <label for="id_anestesiologo" class="col-md-12 control-label" >Anestesiólogo</label>
                                        <select id="id_anestesiologo" class="form-control input-sm" name="id_anestesiologo"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('id_anestesiologo'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_anestesiologo') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('id_ayudante') ? ' has-error' : '' }}">
                                        <label for="id_ayudante" class="col-md-12 control-label" >Ayudante de Anestesiologia</label>
                                        <select id="id_ayudante" class="form-control input-sm" name="id_anestesiologo"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('id_ayudante'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_ayudante') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('id_instrumentista') ? ' has-error' : '' }}">
                                        <label for="id_instrumentista" class="col-md-12 control-label" >Instrumentistas</label>
                                        <select id="id_instrumentista" class="form-control input-sm" name="id_anestesiologo"  required >
                                            <option value="">Seleccione..</option>
                                            @foreach($enfermeros as $value)
                                            <option value="{{$value->id}}">Enf. {{$value->nombre1}} {{$value->apellido1}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('id_instrumentista'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_instrumentista') }}</strong>
                                        </span>
                                        @endif
                                    </div>       

                                    <!--record-->
                                    
                                    <div class="form-group col-md-4{{ $errors->has('id_tipoanesteciologia') ? ' has-error' : '' }}">
                                        <label for="id_tipoanesteciologia" class="col-md-12 control-label" >Tipo Anestesiologia</label>
                                        <select id="id_tipoanesteciologia" class="form-control input-sm" name="id_tipoanesteciologia"  required >
                                            <option value="">Seleccionar ..</option>
                                            @foreach($tipo_anesteciologia as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('id_tipoanesteciologia'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_tipoanesteciologia') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('diagnostico_preoperatorio') ? ' has-error' : '' }}">
                                        <label for="diagnostico_preoperatorio" class="col-md-12 control-label" >Diagnostico Preoperatorio</label>
                                        <input id="diagnostico_preoperatorio" class="form-control input-sm" name="diagnostico_preoperatorio" value=@if(old('diagnostico_preoperatorio')!='')"{{old('diagnostico_preoperatorio')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->diagnostico_preoperatorio }}@endif >
                                        @if ($errors->has('diagnostico_preoperatorio'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('diagnostico_preoperatorio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('diagnostico_postoperatorio') ? ' has-error' : '' }}">
                                        <label for="diagnostico_postoperatorio" class="col-md-12 control-label" >Diagnostico Preoperatorio</label>
                                        <input id="diagnostico_postoperatorio" class="form-control input-sm" name="diagnostico_postoperatorio" value=@if(old('diagnostico_postoperatorio')!='')"{{old('diagnostico_postoperatorio')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->diagnostico_postoperatorio }}@endif >
                                        @if ($errors->has('diagnostico_postoperatorio'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('diagnostico_postoperatorio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--dextrosa-->
                                    <div class="col-md-2{{ $errors->has('dextrosa') ? ' has-error' : '' }}">
                                        <label for="dextrosa" class="col-md-12 control-label">Dextrosa (cc)</label>
                                        <input id="dextrosa" min=0 type="number" step="any" class="form-control input-sm" name="dextrosa" value=@if(old('dextrosa')!='')"{{old('dextrosa')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->dextrosa }}" @else "{{0}}" @endif >
                                        @if ($errors->has('dextrosa'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('dextrosa') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--cloruro_sodio-->
                                    <div class="col-md-2{{ $errors->has('cloruro_sodio') ? ' has-error' : '' }}">
                                        <label for="cloruro_sodio" class="col-md-12 control-label">Cloruro de Sodio (cc)</label>
                                        <input id="cloruro_sodio" min=0 type="number" step="any" class="form-control input-sm" name="cloruro_sodio" value=@if(old('cloruro_sodio')!='')"{{old('cloruro_sodio')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->cloruro_sodio }}" @else "{{0}}" @endif > 
                                        @if ($errors->has('cloruro_sodio'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cloruro_sodio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--lactato_ringer-->
                                    <div class="col-md-2{{ $errors->has('lactato_ringer') ? ' has-error' : '' }}">
                                        <label for="lactato_ringer" class="col-md-12 control-label" style="font-size: 14px;">Lactato de Ringer (cc)</label>
                                        <input id="lactato_ringer" min=0 type="number" step="any" class="form-control input-sm" name="lactato_ringer" value=@if(old('lactato_ringer')!='')"{{old('lactato_ringer')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->lactato_ringer }}" @else "{{0}}" @endif >
                                        @if ($errors->has('lactato_ringer'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lactato_ringer') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--sangre_derivados-->
                                    <div class="col-md-2{{ $errors->has('sangre_derivados') ? ' has-error' : '' }}">
                                        <label for="sangre_derivados" class="col-md-12 control-label" style="font-size: 13px;">Sangre o Derivados (cc)</label>
                                        <input id="sangre_derivados" min=0 type="number" step="any" class="form-control input-sm" name="sangre_derivados" value=@if(old('sangre_derivados')!='')"{{old('sangre_derivados')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->sangre_derivados }}" @else "{{0}}" @endif >
                                        @if ($errors->has('sangre_derivados'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('sangre_derivados') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--expansores-->
                                    <div class="col-md-2{{ $errors->has('expansores') ? ' has-error' : '' }}">
                                        <label for="expansores" class="col-md-12 control-label"">Expansores (cc)</label>
                                        <input id="expansores" min=0 type="number" step="any" class="form-control input-sm" name="expansores" value=@if(old('expansores')!='')"{{old('expansores')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->expansores }}" @else "{{0}}" @endif >
                                        @if ($errors->has('expansores'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('expansores') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--tecnicas_especiales-->
                                    <div class="form-group col-md-4{{ $errors->has('tecnicas_especiales') ? ' has-error' : '' }}">
                                        <label for="tecnicas_especiales" class="col-md-12 control-label" >Tecnicas Especiales</label>
                                        <input id="tecnicas_especiales" class="form-control input-sm" name="tecnicas_especiales" value=@if(old('tecnicas_especiales')!='')"{{old('tecnicas_especiales')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->tecnicas_especiales }}@endif >
                                        @if ($errors->has('tecnicas_especiales'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('tecnicas_especiales') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--id_sala-->  
                                    <div class="form-group col-md-4{{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                        <label for="id_sala" class="col-md-12 control-label" >Traslado a Sala</label>
                                        <select id="id_sala" class="form-control input-sm" name="id_sala"  required >
                                            <option value="">Seleccione..</option>
                                            @foreach($salas as $value)
                                            <option value="{{$value->id}}">{{$value->nombre_sala}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('id_sala'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_sala') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--id_guiado-->  
                                    <div class="form-group col-md-4{{ $errors->has('id_guiado') ? ' has-error' : '' }}">
                                        <label for="id_guiado" class="col-md-12 control-label" >Guiado por</label>
                                        <select id="id_guiado" class="form-control input-sm" name="id_guiado"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('id_guiado'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_guiado') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--hora-->  
                                    <div class="form-group col-md-2{{ $errors->has('hora') ? ' has-error' : '' }}">
                                        <label for="hora" class="col-md-12 control-label" >Hora de Traslado</label>
                                        <input id="hora" type="time" class="form-control input-sm" name="hora" value=@if(old('hora')!='')"{{old('hora')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->hora }}@endif >
                                        @if ($errors->has('hora'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('hora') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--comentarios-->  
                                    <div class="form-group col-md-10{{ $errors->has('comentarios') ? ' has-error' : '' }}">
                                        <label for="comentarios" class="col-md-12 control-label" >Comentarios</label>
                                        <input id="comentarios"  class="form-control input-sm" name="comentarios" value=@if(old('comentarios')!='')"{{old('comentarios')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->comentarios }}@endif >
                                        @if ($errors->has('comentarios'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('comentarios') }}</strong>
                                        </span>
                                        @endif
                                    </div>
        
                                    <br><br>
                                    <div class="form-group col-md-12">
                                        <h2 class="box-title"></h2>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <h2 class="box-title">VALORACION POST-ANESTESICA</h2>
                                    </div>                                                             
                
                                    <br><br>    
                                    <!--sistema_circulatorio-->  
                                    <div class="form-group col-md-4{{ $errors->has('sistema_circulatorio') ? ' has-error' : '' }}">
                                        <label for="sistema_circulatorio" class="col-md-12 control-label" >Sistema Circulatorio</label>
                                        <select id="sistema_circulatorio" class="form-control input-sm" name="sistema_circulatorio"  required >
                                            <option value="">Seleccione..</option>
                                            <option value="2">PA 20% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option value="1">PA 20 - 49% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option value="0">PA 50% NIVEL PRE-ANEST&Eacute;SICO</option>
                                        </select>
                                        @if ($errors->has('sistema_circulatorio'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('sistema_circulatorio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--conciencia-->  
                                    <div class="form-group col-md-4{{ $errors->has('conciencia') ? ' has-error' : '' }}">
                                        <label for="conciencia" class="col-md-12 control-label" >Conciencia</label>
                                        <select id="conciencia" class="form-control input-sm" name="conciencia"  required >
                                            <option value="">Seleccione..</option>
                                            <option value="2">PA 20% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option value="1">PA 20 - 49% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option value="0">PA 50% NIVEL PRE-ANEST&Eacute;SICO</option>
                                        </select>
                                        @if ($errors->has('conciencia'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('conciencia') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--saturacion-->  
                                    <div class="form-group col-md-4{{ $errors->has('saturacion') ? ' has-error' : '' }}">
                                        <label for="saturacion" class="col-md-12 control-label" >Saturacion</label>
                                        <select id="saturacion" class="form-control input-sm" name="saturacion"  required >
                                            <option value="">Seleccione..</option>
                                            <option value="2">SAT 02 + 92% AIRE AMBIENTE</option>
                                            <option value="1">NECESITA 02 SAT&gt;90%</option>
                                            <option value="0">SAT 02&lt;90% con 02</option>
                                        </select>
                                        @if ($errors->has('saturacion'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('saturacion') }}</strong>
                                        </span>
                                        @endif
                                    </div> 
                                    <!--actividades-->  
                                    <div class="form-group col-md-6{{ $errors->has('actividades') ? ' has-error' : '' }}">
                                        <label for="actividades" class="col-md-12 control-label" >Actividades</label>
                                        <select id="actividades" class="form-control input-sm" name="actividades"  required >
                                            <option value="">Seleccione..</option>
                                            <option value="2">CAPAZ DE MOVER LAS CUATRO EXTREMIDADES VOLUNTARIO O BAJO ORDENES</option>
                                            <option value="1">CAPAZ DE MOVER LAS DOS EXTREMIDADES VOLUNTARIOS O BAJO ORDENES</option>
                                            <option value="0">CAPAZ DE MOVER UNA EXTREMIDAD VOLUNTARIO O BAJO ORDENES</option>
                                        </select>
                                        @if ($errors->has('actividades'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('actividades') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--respiracion-->  
                                    <div class="form-group col-md-6{{ $errors->has('respiracion') ? ' has-error' : '' }}">
                                        <label for="respiracion" class="col-md-12 control-label" >Respiracion</label>
                                        <select id="respiracion" class="form-control input-sm" name="respiracion"  required >
                                            <option value="">Seleccione..</option>
                                            <option value="2">CAPAZ DE RESPIRAR PROFUNDAMENTE Y TOSER</option>
                                            <option value="1">APNEA RESPIRACION LIMITADA O TAQUIPMEA</option>
                                            <option value="0">APNEICO O CON RESPIRADOR ARTIFICIAL</option>
                                        </select>
                                        @if ($errors->has('respiracion'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('respiracion') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--tipo_analgesia-->  
                                    <div class="form-group col-md-6{{ $errors->has('tipo_analgesia') ? ' has-error' : '' }}">
                                        <label for="tipo_analgesia" class="col-md-12 control-label" >Tipo de Analgesia</label>
                                        <select id="tipo_analgesia" class="form-control input-sm" name="tipo_analgesia"  required >
                                            <option value="">Seleccione..</option>
                                            <option value="2">ANALGESIA INTRAVENOSA</option>
                                            <option value="1">ANALGESIA PERIDURAL</option>
                                            <option value="0">ANALGESIA POR PRN</option>
                                        </select>
                                        @if ($errors->has('tipo_analgesia'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('tipo_analgesia') }}</strong>
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
                    <div id="tab4" class="w3-container w3-border city" style="display:none">
                        <div class="box ">
                            <div class="box-header with-border">
                                <h4>Evolucion del Paciente</h4>                                                                                                                                                                                                                                                
                            </div>
                            <div class="box-body">
                                <form class="form-vertical" role="form" method="POST" action="{{ route('admisiones.update_doctor', ['id' => $paciente->id, 'id_cita' => $hca[0]->id_agenda, 'id_historia' => $hca[0]->hcid ]) }}" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">  

                                    
                                    <!--anestesiologo-->
                                    
                                    <div class="form-group col-md-4{{ $errors->has('id_anestesiologo') ? ' has-error' : '' }}">
                                        <label for="id_anestesiologo" class="col-md-12 control-label" >Anestesiólogo</label>
                                        <select id="id_anestesiologo" class="form-control input-sm" name="id_anestesiologo"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('id_anestesiologo'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_anestesiologo') }}</strong>
                                        </span>
                                        @endif
                                    </div>    

                                    <!--record-->
                                    
                                    <div class="form-group col-md-8{{ $errors->has('id_record') ? ' has-error' : '' }}">
                                        <label for="id_record" class="col-md-12 control-label" >Formato Record</label>
                                        <select id="id_record" class="form-control input-sm" name="id_record"  required onchange="drogasadministradas();">
                                            <option value="">Seleccionar ..</option>
                                            @foreach($records as $record)
                                            <option value="{{$record->id}}">{{$record->descripcion}}</option>
                                            @endforeach
                                        </select>   
                                        @if ($errors->has('id_record'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_record') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="col-md-12" id="drogas"></div>    
                                    





                                    <!--altura-->
                                    <div class="col-md-2{{ $errors->has('altura') ? ' has-error' : '' }}">
                                        <label for="altura" class="col-md-12 control-label">Altura (cm)</label>
                                        <input id="altura" min=0 type="number" class="form-control input-sm" name="altura" value=@if(old('altura')!='')"{{old('altura')}}"@elseif($paciente->altura!="")"{{ $paciente->altura }}" @else "{{0}}" @endif >
                                        @if ($errors->has('altura'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('altura') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <!--temperatura-->
                                    <div class="col-md-2{{ $errors->has('temperatura') ? ' has-error' : '' }}">
                                        <label for="temperatura" class="col-md-12 control-label">Temp. (ºC)</label>
                                        <input id="temperatura" min=0 type="number" step="any" class="form-control input-sm" name="temperatura" value=@if(old('temperatura')!='')"{{old('temperatura')}}"@elseif($paciente->temperatura!="")"{{ $paciente->temperatura }}" @else "{{0}}" @endif >
                                        @if ($errors->has('temperatura'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('temperatura') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--presion-->
                                    <div class="col-md-2{{ $errors->has('presion') ? ' has-error' : '' }}">
                                        <label for="presion" class="col-md-12 control-label" style="padding: 0px;">Pres.(mm Hg)</label>
                                        <input id="presion" min=0 type="number" step="any" class="form-control input-sm" name="presion" value=@if(old('presion')!='')"{{old('presion')}}"@elseif($paciente->presion!="")"{{ $paciente->presion }}" @else "{{0}}" @endif >
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

                    <div id="Tokyo" class="w3-container w3-border city" style="display:none">
                        <h2>Tokyo</h2>
                        <p>Tokyo is the capital of Japan.</p>
                    </div>


<script>

</script> 
                </div>
            </div>
        </div>  

        

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
                            <input type="hidden" name="id" value="{{ $hca[0]->hcid }}"> 
                            <input type="hidden" name="paciente" value="{{$agenda->id_paciente}}"> 
                        </form>
                    </div>                   
                </div>

            </div>
        </div-->        

        
        
        
        <div class="col-md-12" id="tab2" style="display: none;" >
            <div class="box box">
                <div class="box-header with-border">
                    <h4>Atención del Paciente</h4>
                   
                </div>
                <div class="box-body">
                    <div class="form-group col-md-12">
                        <form class="form-group" role="form" method="POST" action="{{ route('historiaclinica.guardar') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="id" value="{{ $hca[0]->hcid }}">
                            <input id="hijos_vivos2" type="hidden" class="form-control" name="hijos_vivos" value="">
                                <input id="hijos_muertos2" type="hidden" class="form-control" name="hijos_muertos" value="">
                                <input id="gruposanguineo2" type="hidden" class="form-control" name="gruposanguineo" value="">
                                <input id="transfusion2" type="hidden" class="form-control" name="transfusion" value="">
                                <input id="alcohol2" type="hidden" class="form-control" name="alcohol" value="">
                                <input id="alergias2" type="hidden" class="form-control" name="alergias" value="">
                                <input id="vacuna2" type="hidden" class="form-control" name="vacuna" value="">
                                <input id="antecedentes_pat2" type="hidden" class="form-control" name="antecedentes_pat" value="">
                                <input id="antecedentes_fam2" type="hidden" class="form-control" name="antecedentes_fam" value="">
                                <input id="antecedentes_quir2" type="hidden" class="form-control" name="antecedentes_quir" value=""> 

                                <div class="form-inline col-md-12"> 
                            
                            </div>
                            <div>&nbsp</div>
                            <!--evolucion-->
                            <div class="form-group col-md-12 {{ $errors->has('evolucion') ? ' has-error' : '' }}">
                                <label for="evolucion" class="col-md-12 control-label">Hallazgos</label>
                                <textarea maxlength="300" rows="3" cols="50" id="evolucion" class="form-control" name="evolucion" required="required">@if(old('evolucion')!=''){{old('evolucion')}}@else{{$hca[0]->evolucion}}@endif</textarea>
                                @if ($errors->has('evolucion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('evolucion') }}</strong>
                                </span>
                                @endif   
                            </div>
                            <!--observaciones--> 
                            <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-2 control-label">Conclusiones</label>
                                <textarea maxlength="250" rows="3" cols="50" id="observaciones" class="form-control" name="observaciones" required="required">@if(old('observaciones')!=''){{old('observaciones')}}@else{{$hca[0]->observaciones}}@endif</textarea>
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
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

</script>

@include('sweet::alert')
@endsection

