
@extends('historiaclinica.base')

@section('action-content')
<br>
 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
        
    </div>
  </div>
</div>
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">  
                    @php $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td><h4><b>Paciente:</b></h4></td>
                            <td colspan="3"><h4>{{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</h4></td>
                            <td><h4><b>Cédula:</b></h4></td>
                            <td><h4>{{$agenda->id_paciente}}</h4></td>
                            <td><h4><b>Edad:</b> </h4></td>
                            <td><h4><span id="edad"></span></h4></td>
                            <td><h4><b>Estado Civil:</b></h4></td>
                            <td><h4>@if($paciente->estadocivil==1) {{"SOLTERO(A)"}} @elseif($paciente->estadocivil==2) 
                        {{"CASADO(A)"}} @elseif($paciente->estadocivil==3) {{"VIUDO(A)"}} @elseif($paciente->estadocivil==4) {{"DIVORCIADO(A)"}} @elseif($paciente->estadocivil==5) 
                        {{"UNION LIBRE"}} @elseif($paciente->estadocivil==6) {{"UNION DE HECHO"}} @endif</h4></td>
                        </tr>
                        <tr>
                            <td><b>Seguro:</b></td>
                            <td colspan="3">{{$seguro->nombre}} - {{$seguro->descripcion}}</td>
                            @if($hca[0]->id_subseguro!="")@php $subseguro=Sis_medico\Subseguro::find($hca[0]->id_subseguro) @endphp
                            <td><b>Sub-Seguro:</b></td>
                            <td>{{$subseguro->nombre}}</td>@endif
                            @if($hca[0]->verificar==1)@if($archivo_vrf->ruta!="")@php $archivopdf=$archivo_vrf->ruta.$archivo_vrf->archivo @endphp
                            <td colspan="2"><a target="_blank" href="{{asset($archivopdf)}}"  alt="pdf"  style="width:120px;height:120px;" id="pdf" > Consulta Cobertura Salud </a></td>
                            @else
                            <td colspan="2"><p style="color: red;">**Error al subir archivo</p></td> 
                            @endif @endif 
                            @if($hca[0]->codigo!="")
                            <td><b>Código:</b></td>
                            <td>{{$hca[0]->codigo}}</td>
                            <td><b>Caducidad Código:</b></td>
                            <td>{{$hca[0]->fecha_codigo}}</td> 
                            @endif
                            @if($hca[0]->copago!="")
                            <td><b>Copago:</b></td>
                            <td>{{$hca[0]->copago}} %</td>
                            @endif   
                        </tr>
                        <tr>
                            <td><b>Fecha Cita:</b></td>
                            <td>{{ substr($agenda->fechaini, 0, 10)}}</td>
                            <td><b>Hora:</b></td>
                            <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                            <td><b>Cortesia:</b></td>
                            <td>@if($agenda->cortesia=='NO') NO @else SI @endif</td>
                            <td><b>Ingreso:</b></td>
                            <td>@if($agenda->est_amb_hos=='0'){{'Ambulatorio'}}@else{{'Hospitalizado'}}@endif</td>
                            <td><b>Especialidad:</b></td>
                            <td>{{$especialidad->nombre}}</td>
                        </tr>
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h4>Historial Clínico Ingresada en la Agenda</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>   
                </div>
                <div class="box-body" >
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <table id="example2" class="table table-bordered table-hover">
                                    <tbody>
                                    @foreach($hcagenda as $value)
                                        <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.hcagenda', ['id' => $value->id])}}">Mostrar</a></td>
                                    @endforeach
                                    </tbody>
                                </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    
        <div class="col-md-12">
            <div class="box box collapsed-box">
                <div class="box-header with-border">
                    <h4>Historial Clínico del Paciente</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                      <tr role="row">
                                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Fecha</th>
                                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Especialidad</th>
                                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo</th>
                                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Doctor </th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($hcp as $value)
                                        @if($value->tipo_cita == 0)
                                        <tr role="row" class="odd">
                                            <td class="sorting_1"><a  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}" data-toggle="modal" data-target="#favoritesModal2" >{{ substr($value->fechainicio, 0, -3)}}</a></td>
                                            <td ><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->especialidad}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}"> Consulta</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">Dr(a). {{ $value->dnombre1 }} {{ $value->dapellido1 }} </a></td>  
                                        </tr>
                                        @elseif($value->tipo_cita == 1)
                                        <tr role="row" class="odd">
                                            <td class="sorting_1"><a  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}" data-toggle="modal" data-target="#favoritesModal2" >{{ substr($value->fechainicio, 0, -3)}}</a></td>
                                            <td ><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}">{{ $value->especialidad}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}"> Procedimientos</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}">Dr(a). {{ $value->dnombre1 }} {{ $value->dapellido1 }} </a></td>  
                                        </tr>
                                        @endif 
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
        <div class="col-md-6">
            <div class="box ">
                <div class="box-header with-border">
                    <h4>Admisión del Paciente</h4>
                    
                </div>
                <div class="box-body">
                    <div class="form-group col-md-12"> 
                        <form class="form-vertical" role="form" method="POST" action="{{ route('admisiones.update_doctor', ['id' => $paciente->id, 'id_cita' => $hca[0]->id_agenda, 'id_historia' => $hca[0]->hcid ]) }}" >
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">   
                             <!--hijos_vivos-->
                            <div class="form-group col-md-3{{ $errors->has('hijos_vivos') ? ' has-error' : '' }}">
                                <label for="hijos_vivos" class="col-md-12 control-label">Hijos Vivos</label>
                                <input id="hijos_vivos" min=0 type="number" class="form-control" name="hijos_vivos" value=@if(old('hijos_vivos')!='')"{{old('hijos_vivos')}}"@elseif($paciente->hijos_vivos!="")"{{ $paciente->hijos_vivos }}" 
                                @else "{{0}}" @endif required >
                                @if ($errors->has('hijos_vivos'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('hijos_vivos') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--hijos_muertos-->
                            <div class="form-group col-md-3{{ $errors->has('hijos_muertos') ? ' has-error' : '' }}">
                                <label for="hijos_muertos" class="col-md-12 control-label">H. fallecidos</label>
                                <input id="hijos_muertos" min=0 type="number" class="form-control" name="hijos_muertos" value=@if(old('hijos_muertos')!='')"{{old('hijos_muertos')}}"
                                @elseif($paciente->hijos_muertos!="")"{{ $paciente->hijos_muertos }}"@else"{{0}}" @endif required >
                                @if ($errors->has('hijos_muertos'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('hijos_muertos') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--Grupo Sanguineo-->
                            <div class="form-group col-md-3{{ $errors->has('gruposanguineo') ? ' has-error' : '' }}">
                                <label for="gruposanguineo" class="col-md-12 control-label">G.Sanguíneo</label>
                                <select id="gruposanguineo" class="form-control" name="gruposanguineo"  required>
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
                            <div class="form-group col-md-3{{ $errors->has('transfusion') ? ' has-error' : '' }}" >
                                <label for="transfusion" class="col-md-8 control-label">Transfusiones</label>
                                <select id="transfusion" name="transfusion" class="form-control"  required >
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
                            <div class="form-group col-md-6{{ $errors->has('alcohol') ? ' has-error' : '' }}">
                                <label for="alcohol" class="col-md-10 control-label">Consumo Alcohol</label>
                                <select id="alcohol" class="form-control" name="alcohol"  required >
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
                            <div class="form-group col-md-6{{ $errors->has('anticonceptivos') ? ' has-error' : '' }}">
                                <label for="anticonceptivos" class="col-md-8 control-label">Anticonceptivos</label>
                                <input id="anticonceptivos" type="text" class="form-control" name="anticonceptivos" value=@if(old('anticonceptivos')!='')"{{old('anticonceptivos')}}"@else"{{ $paciente->anticonceptivos }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('anticonceptivos'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('anticonceptivos') }}</strong>
                                </span>
                                @endif
                             </div>
                            <!--alergias-->
                            <div class="form-group col-md-6{{ $errors->has('alergias') ? ' has-error' : '' }}">
                                <label for="alergias" class="col-md-8 control-label">Alergias</label>
                                <textarea rows="2" cols="50" maxlength="255" id="alergias" class="form-control" name="alergias" required >@if(old('alergias')!=''){{old('alergias')}}@else{{ $paciente->alergias }}@endif</textarea>
                                @if ($errors->has('alergias'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('alergias') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--vacuna-->
                            <div class="form-group col-md-6{{ $errors->has('vacuna') ? ' has-error' : '' }}">
                                <label for="vacuna" class="col-md-8 control-label">Vacunas</label>
                                <textarea rows="2" cols="50" maxlength="255" id="vacuna" class="form-control" name="vacuna" required >@if(old('vacuna')!=''){{old('vacuna')}}@else{{ $paciente->vacuna }}@endif</textarea>
                                @if ($errors->has('vacuna'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('vacuna') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--antecedentes_pat-->
                            <div class="form-group col-md-6{{ $errors->has('antecedentes_pat') ? ' has-error' : '' }}"> 
                                <label for="antecedentes_pat" class="col-md-12 control-label">Antecedentes Patológicos</label>
                                <textarea rows="4" cols="50" maxlength="300" id="antecedentes_pat" class="form-control" name="antecedentes_pat" required >@if(old('antecedentes_pat')!=''){{old('antecedentes_pat')}}@else{{ $paciente->antecedentes_pat }}@endif</textarea>
                                @if ($errors->has('antecedentes_pat'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('antecedentes_pat') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!--antecedentes_fam-->
                            <div class="form-group col-md-6{{ $errors->has('antecedentes_fam') ? ' has-error' : '' }}">                                   
                                <label for="antecedentes_fam" class="col-md-12 control-label">Antecedentes Familiares</label>
                                <textarea rows="4" cols="50" maxlength="300" id="antecedentes_fam" class="form-control" name="antecedentes_fam" required >@if(old('antecedentes_fam')!=''){{old('antecedentes_fam')}}@else{{ $paciente->antecedentes_fam }}@endif</textarea>
                                @if ($errors->has('antecedentes_fam'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('antecedentes_fam') }}</strong>
                                </span>
                                @endif    
                            </div>
                            <!--antecedentes_quir-->
                            <div class="form-group col-md-12{{ $errors->has('antecedentes_quir') ? ' has-error' : '' }}">  
                                <label for="antecedentes_quir" class="col-md-12 control-label">Antecedentes Quirúrgicos</label>
                                <textarea rows="2" cols="50" maxlength="300" id="antecedentes_quir" class="form-control" name="antecedentes_quir" required >@if(old('antecedentes_quir')!=''){{old('antecedentes_quir')}}@else{{ $paciente->antecedentes_quir }}@endif</textarea>
                                @if ($errors->has('antecedentes_quir'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('antecedentes_quir') }}</strong>
                                </span>
                                 @endif  
                            </div>

                            @if($paciente->sexo==2)
                            <!--primera_mens-->
                            <div class="form-group col-md-3{{ $errors->has('primera_mens') ? ' has-error' : '' }}"> 
                                <label for="primera_mens" class="col-md-12 control-label">E.Menstruación</label>
                                <input min=0 id="primera_mens" type="number" class="form-control" name="primera_mens" required value=@if(old('primera_mens')!='')"{{old('primera_mens')}}"@elseif($paciente->primera_mens!="")"{{$paciente->primera_mens}}"@else "{{0}}" @endif >
                                @if ($errors->has('primera_mens'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('primera_mens') }}</strong>
                                </span>
                                @endif    
                            </div>
                            <!--menopausia-->
                            <div class="form-group col-md-3{{ $errors->has('menopausia') ? ' has-error' : '' }}">   
                                <label for="menopausia" class="col-md-12 control-label">E.Menopausia</label>
                                <input min=0 id="menopausia" type="number" class="form-control" name="menopausia" required value=@if(old('menopausia')!='')"{{old('menopausia')}}"@elseif($paciente->menopausia!="")"{{ $paciente->menopausia }}" @else "{{0}}" @endif  >
                                @if ($errors->has('menopausia'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('menopausia') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--parto_cesarea-->
                            <div class="form-group col-md-3{{ $errors->has('parto_cesarea') ? ' has-error' : '' }}">   
                                <label for="parto_cesarea" class="col-md-12 control-label">P. Cesáreas</label>
                                <input min=0 id="parto_cesarea" type="number" class="form-control" name="parto_cesarea" required value=@if(old('parto_cesarea')!='')"{{old('parto_cesarea')}}"@elseif($paciente->parto_cesarea!="")"{{ $paciente->parto_cesarea }}" @else "{{0}}" @endif >
                                @if ($errors->has('parto_cesarea'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parto_cesarea') }}</strong>
                                </span>
                                @endif    
                            </div>

                            <!--parto_normal-->
                            <div class="form-group col-md-3{{ $errors->has('parto_normal') ? ' has-error' : '' }}">
                                <label for="parto_normal" class="col-md-12 control-label">P. Normal</label>
                                <input min=0 id="parto_normal" type="number" class="form-control" name="parto_normal" required value=@if(old('parto_normal')!='')"{{old('parto_normal')}}"@elseif($paciente->parto_normal!="")"{{ $paciente->parto_normal }}" @else "{{0}}" @endif >
                                @if ($errors->has('parto_normal'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parto_normal') }}</strong>
                                </span>
                                @endif 
                            </div>
                            <!--aborto-->
                            <div class="form-group col-md-3{{ $errors->has('aborto') ? ' has-error' : '' }}">  
                                <label for="aborto" class="col-md-12 control-label">Abortos</label>
                                <input min=0 id="aborto" type="number" class="form-control" name="aborto" required value=@if(old('aborto')!='')"{{old('aborto')}}"@elseif($paciente->aborto!="")"{{ $paciente->aborto }}" @else "{{0}}" @endif >
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
        </div>
        <div class="col-md-6">
            <div class="box box">
                <div class="box-header with-border">
                    <h4>Atención del Paciente</h4>
                </div>
                <div class="box-body">
                    <div class="form-group col-md-12">
                        <form class="form-group" role="form" method="POST" action="{{ route('historiaclinica.guardar') }}" >
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="id" value="{{ $hca[0]->hcid }}">
                            <div class="form-inline col-md-12"> 
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

                            <!--peso-->
                            <div class="col-md-6{{ $errors->has('peso') ? ' has-error' : '' }}">
                                <label for="peso" class="col-md-6 control-label">Peso (Kg)</label>
                                <input id="peso" min=0 type="number" step="any" class="form-control" name="peso" value=@if(old('peso')!='')"{{old('peso')}}"@elseif($paciente->peso!="")"{{ $paciente->peso }}" @else "{{0}}" @endif required autofocus >
                                @if ($errors->has('peso'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('peso') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--altura-->
                            <div class="col-md-6{{ $errors->has('altura') ? ' has-error' : '' }}">
                                <label for="altura" class="col-md-6 control-label">Altura (cm)</label>
                                <input id="altura" min=0 type="number" class="form-control" name="altura" value=@if(old('altura')!='')"{{old('altura')}}"@elseif($paciente->altura!="")"{{ $paciente->altura }}" @else "{{0}}" @endif required autofocus >
                                    @if ($errors->has('altura'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('altura') }}</strong>
                                </span>
                                @endif
                            </div>
                            </div>
                            <div>&nbsp</div>
             
                            <div class="form-inline col-md-12"> 
                            <!--temperatura-->
                            <div class="col-md-6{{ $errors->has('temperatura') ? ' has-error' : '' }}">
                                <label for="temperatura" class="col-md-8 control-label">Temperatura (ºC)</label>
                                <input id="temperatura" min=0 type="number" step="any" class="form-control" name="temperatura" value=@if(old('temperatura')!='')"{{old('temperatura')}}"@elseif($paciente->temperatura!="")"{{ $paciente->temperatura }}" @else "{{0}}" @endif required autofocus >
                                    @if ($errors->has('temperatura'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('temperatura') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--presion-->
                            <div class="col-md-6{{ $errors->has('presion') ? ' has-error' : '' }}">
                                <label for="presion" class="col-md-8 control-label">Presión (mm Hg)</label>
                                <input id="presion" min=0 type="number" step="any" class="form-control" name="presion" value=@if(old('presion')!='')"{{old('presion')}}"@elseif($paciente->presion!="")"{{ $paciente->presion }}" @else "{{0}}" @endif >
                                    @if ($errors->has('presion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('presion') }}</strong>
                                </span>
                                @endif
                            </div>
                            </div>
                            <div>&nbsp;</div>

                            
                            <!--evolucion-->
                            <div class="form-group col-md-12 {{ $errors->has('evolucion') ? ' has-error' : '' }}">
                                <label for="evolucion" class="col-md-12 control-label">Evolucion</label>
                                <textarea rows="3" cols="50" maxlength="300" id="evolucion" class="form-control" name="evolucion" required autofocus >@if(old('evolucion')!=''){{old('evolucion')}}@else{{$hca[0]->evolucion}}@endif</textarea>
                                @if ($errors->has('evolucion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('evolucion') }}</strong>
                                </span>
                                @endif   
                            </div>
                            <!--observaciones--> 
                            <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                                <textarea rows="3" cols="50" maxlength="250" id="observaciones" class="form-control" name="observaciones" required autofocus >@if(old('observaciones')!=''){{old('observaciones')}}@else{{$hca[0]->observaciones}}@endif</textarea>
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--receta-->   
                            <div class="form-group col-md-12 {{ $errors->has('receta') ? ' has-error' : '' }}">
                                <label for="receta" class="col-md-2 control-label">Receta</label>
                                <textarea rows="3" cols="50" maxlength="300" id="receta" class="form-control" name="receta" required autofocus >@if(old('receta')!=''){{old('receta')}}@else{{$hca[0]->receta}}@endif</textarea>
                                @if ($errors->has('receta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('receta') }}</strong>
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
            var fecha = '<?php echo date("Y-m-d")?>';
fecha = fecha.replace(/-/g,',');
var myDate=new Date(fecha);
myDate.setDate(myDate.getDate()+5);


                $('#favoritesModal2').on('hidden.bs.modal', function(){
                    $(this).removeData('bs.modal');
                });

                var edad;
                edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>')+ "años";
                $('#edad').text(edad);

                
                
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

                


            })
 
        </script>

@include('sweet::alert')
@endsection
