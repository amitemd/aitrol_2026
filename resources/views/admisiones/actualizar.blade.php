
@extends('admisiones.base')

@section('action-content')

<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://aplicaciones.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" ></iframe> 
    </div>
  </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-header with-border"><h3 class="box-title">Actualizar Admisión</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('admisiones.update', ['id' => $paciente->id]) }}" enctype="multipart/form-data" >
                    <div class="box-body">
                        @if($paciente->parentesco!="Principal")
                        <p><b>Asociado Principal: </b>{{ $user_aso->id }} {{$user_aso->nombre1}} {{$user_aso->nombre2}} {{$user_aso->apellido1}} {{$user_aso->apellido2}}</p>
                        @endif 
                        
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <input type="hidden" name="cita" value="{{$cita}}">

                        <!--seguro-->
                        @php $tipo=""; $tienecodigo=""; @endphp
                        <div class="form-group col-md-4{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-md-8 control-label">Seguro</label>
                            <select id="id_seguro" name="id_seguro" class="form-control" onchange="subseguro();" required>
                                @foreach ($seguros as $seguro)
                                    <option @if(old('id_seguro')==$seguro->id) {{"selected"}}@php $tipo=$seguro->tipo; $tienecodigo=$seguro->codigo_validacion; $cantidad2=Sis_medico\Subseguro::where('id_seguro',$seguro->id)->count(); @endphp @elseif($i == $seguro->id) {{"selected"}} @php $tipo=$seguro->tipo; $tienecodigo=$seguro->codigo_validacion; $cantidad2=Sis_medico\Subseguro::where('id_seguro',$seguro->id)->count(); @endphp @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                            </select>      
                            @if ($errors->has('id_seguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_seguro') }}</strong>
                                </span>
                            @endif
                        </div>

                        @if($cantidad2>0)
                        <!--subseguro-->
                        <div class="form-group col-md-4 {{ $errors->has('id_subseguro') ? ' has-error' : '' }}">
                            
                            <input id="id_subseguro" type="hidden" class="form-control" name="id_subseguro" value={{$historia->id_subseguro}}>
                            <p><b>Sub-Seguro: </b>@foreach($subseguros as $subseguro) @if($historia->id_subseguro==$subseguro->id) {{$subseguro->nombre}} @endif @endforeach </p>
                            @else
                            <label for="id_subseguro" class="col-md-8 control-label">Sub-Seguro</label>
                            <select id="id_subseguro" name="id_subseguro" class="form-control" required>
                                @foreach ($subseguros as $subseguro)
                                    <option @if(old('id_subseguro')==$subseguro->id){{"selected"}}@elseif(old('id_subseguro')=="" && $paciente->id_subseguro==$subseguro->id){{"selected"}}@endif value="{{$subseguro->id}}">{{$subseguro->nombre}}</option>
                                @endforeach
                            </select>      
                            @if ($errors->has('id_subseguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_subseguro') }}</strong>
                                </span>
                            @endif
                           
                        </div>
                        @endif

                        @if($tipo==1)
                        <!--copago-->
                        <div class="form-group col-md-4{{ $errors->has('copago') ? ' has-error' : '' }}">
                           
                            <p><b>Co-Pago(%) : </b>{{$historia[0]->copago}} </p>
                            @else
                            <label for="copago" class="col-md-8 control-label">Co-Pago(%)</label>
                            <input id="copago" type="number" class="form-control" name="copago" value=@if(old('copago')!='') "{{old('copago')}}" @else "0" @endif required autofocus min="0" max="100">     
                            @if ($errors->has('copago'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('copago') }}</strong>
                                </span>
                            @endif
                            
                        </div> 

                        @endif 

                        @if($tipo==0)
                        <!--verificar-->
                        <div class="checkbox col-md-4{{ $errors->has('verificar') ? ' has-error' : '' }}">
                           
                            <label><input type="checkbox" name="verificar" value="1" required autofocus> Verificar Seguro</label>  
                            @if ($errors->has('verificar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('verificar') }}</strong>
                                </span>
                            @endif
                            
                        </div> 

                        <!--archivo--> 
                        @php $archivopdf=$archivo_vrf->ruta.$archivo_vrf->archivo @endphp
                        <div class="form-group col-md-4{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <a target="_blank" href="{{asset($archivopdf)}}"  alt="pdf"  style="width:120px;height:120px;" id="pdf" > Consulta Cobertura Salud </a>
                            @else
                            <label for="archivo" class="col-md-8 control-label">Agregar Archivo</label>
                            <input name="archivo" id="archivo" type="file"   class="archivo form-control"  required/><br /><br />
                            @if ($errors->has('archivo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('archivo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>  


                        @endif

                        <input type="hidden" name="codigo_validacion" value="{{$tienecodigo}}">
                        <input type="hidden" name="xtipo" value="{{$tipo}}">
                        @if($tienecodigo=="SI")
                        <!--codigo-->
                        <div class="form-group col-md-4{{ $errors->has('codigo') || $errors->has('fecha_codigo')? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Codigo : </b>{{$historia[0]->codigo}} </p>
                            @else
                            <label for="codigo" class="col-md-8 control-label">Código Validación</label>
                            <input id="codigo" type="text" class="form-control" name="codigo" value=@if(old('codigo')!='') "{{old('codigo')}}" @else "{{$paciente->codigo}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if(!$historia->isEmpty()){{"readonly"}}@endif>     
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('fecha_codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_codigo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div> 

                        <!--fecha_codigo-->
                        <div class="form-group col-md-4{{ $errors->has('fecha_codigo') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Caducidad Código : </b> {{substr($historia[0]->fecha_codigo,0,11)}} </p>
                            @else
                            <label for="fecha_codigo" class="col-md-8 control-label">Caducidad Código</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_codigo" type="text" class="form-control" name="fecha_codigo" value=@if(old('fecha_codigo')!='') "{{old('fecha_codigo')}}" @else "{{$paciente->fecha_codigo}}" @endif required autofocus >
                            </div>         
                            @if ($errors->has('fecha_codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_codigo') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div>    
                        @endif 
                        
                        <fieldset class="col-md-12"><legend>Datos Básicos: </legend>

                            <!--parentesco-->
                            <div class="form-group col-md-2{{ $errors->has('parentesco') ? ' has-error' : '' }}" >
                                @if(!$historia->isEmpty())
                                <p><b>Parentesco : </b> {{$paciente->parentesco}} </p>
                                @elseif($paciente->id!=$paciente->id_usuario && $historia->isEmpty())
                                <label for="parentesco" class="col-md-8 control-label">Parentesco</label>    
                                <select id="parentesco" name="parentesco" class="form-control">
                                    <option @if(old('parentesco')=="Padre/Madre"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Padre/Madre"){{"selected"}}@endif value="Padre/Madre">Padre/Madre</option>
                                    <option @if(old('parentesco')=="Conyugue"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Conyugue"){{"selected"}}@endif value="Conyugue">Conyugue</option>
                                    <option @if(old('parentesco')=="Hijo(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Hijo(a)"){{"selected"}}@endif value="Hijo(a)">Hijo(a)</option>
                                </select>    
                                @else
                                <label for="parentesco" class="col-md-8 control-label">Parentesco</label> 
                                <input id="parentesco" type="text" class="form-control" name="parentesco" value="{{ $paciente->parentesco }}" required readonly >
                                @endif              
                                @if ($errors->has('parentesco'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parentesco') }}</strong>
                                </span>
                                @endif
                            </div> 

                            <!--cedula-->
                            <div class="form-group col-md-2{{ $errors->has('id') || $errors->has('cita')? ' has-error' : '' }}">
                                @if(!$historia->isEmpty())
                                <p><b>Cédula : </b> {{$paciente->id}} </p>
                                @else
                                <label for="id" class="col-md-8 control-label">Cédula</label>
                                <input id="id" maxlength="10" type="text" class="form-control" name="id" value=@if(old('id')!='')"{{old('id')}}"@else"{{$paciente->id}}"@endif required autofocus onkeyup="validarCedula(this.value);" >
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cita') }}</strong>
                                    </span>
                                @endif
                                @endif
                            </div>
                        
                            <!--primer nombre-->
                            <div class="form-group col-md-4{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                                @if(!$historia->isEmpty())
                                <p><b>Nombres  : </b>  {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}} </p>
                                @else
                                <label for="nombre1" class="col-md-8 control-label">Primer Nombre</label>
                                <input id="nombre1" type="text" class="form-control" name="nombre1" value=@if(old('nombre1')!='')"{{old('nombre1')}}"@else"{{$paciente->nombre1}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus  >
                                
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                                @endif
                            </div>

                            @if($historia->isEmpty())                
                            <!--segundo nombre-->
                            <div class="form-group col-md-4{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                                <label for="nombre2" class="col-md-8 control-label">Segundo Nombre</label>
                                <div class="input-group dropdown col-md-12">
                                  <input id="nombre2" type="text" class="form-control nombrecode dropdown-toggle" name="nombre2" value=@if(old('nombre2')!='')"{{old('nombre2')}}"@else"{{ $paciente->nombre2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                    @if($historia->isEmpty())     
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>     
                                @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        
                            <!--primer apellido-->
                            <div class="form-group col-md-4{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                                <label for="apellido1" class="col-md-8 control-label">Primer Apellido</label>
                                <input id="apellido1" type="text" class="form-control" name="apellido1" value=@if(old('apellido1')!='')"{{old('apellido1')}}"@else"{{ $paciente->apellido1 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                @if ($errors->has('apellido1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        
                            <!--segundo apellido-->
                            <div class="form-group col-md-4{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                                <label for="apellido2" class="col-md-8 control-label">Segundo Apellido</label>
                                <div class="input-group dropdown col-md-12">
                                  <input id="apellido2" type="text" class="form-control nombrecode dropdown-toggle" name="apellido2" value=@if(old('apellido2')!='')"{{old('apellido2')}}"@else"{{ $paciente->apellido2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus  >
                                  @if($historia->isEmpty())
                                 <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                  @endif  
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>    
                                @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                @endif
                            </div>

                        @endif

                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-4{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                                <p><b>Fecha Nacimiento : </b> {{$paciente->fecha_nacimiento}}</p>
                            @else
                            <label for="fecha_nacimiento" class="col-md-8 control-label">Fecha Nacimiento</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_nacimiento" onchange="edad2();" type="text" class="form-control" name="fecha_nacimiento" value=@if(old('fecha_nacimiento')!='')"{{old('fecha_nacimiento')}}"@else"{{ $paciente->fecha_nacimiento }}" @endif required autofocus @if(!$historia->isEmpty()){{"readonly"}}@endif >
                            </div>    
                            @if ($errors->has('fecha_nacimiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                </span>
                            @endif
                            @endif    
                        </div>

                        @if($historia->isEmpty())    
                        <!-- Div de edad -->
                        <div class="form-group col-md-2{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                            <label for="Xedad" class="col-md-8 control-label">Edad</label>
                                <input id="Xedad" type="text" class="form-control" name="Xedad" readonly="readonly">

                                @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                @endif
                        </div>
                        

                        <!--menor edad-->
                        <div class="form-group col-md-2{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="menoredad" class="col-md-8 control-label">Menor de Edad</label>
                            <input id="tmenoredad" type="text" class="form-control" name="tmenoredad" value=@if($paciente->menoredad==0)"{{'NO'}}"@else"{{'SI'}}"@endif required readonly="readonly">
                            <input id="menoredad" type="hidden" class="form-control" name="menoredad" value="{{$paciente->menoredad}}" >
                                @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif
                        </div>
                        @endif
                        
                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-2{{ $errors->has('sexo') ? ' has-error' : '' }}" >
                            @if(!$historia->isEmpty())
                            <input id="sexo" type="hidden" class="form-control" name="sexo" value={{$paciente->sexo}}>
                            <p><b>Sexo :</b>@if($paciente->sexo==1) {{"HOMBRE"}} @elseif($paciente->sexo==2) {{"MUJER"}} @endif </p>
                            @else
                            <label for="sexo" class="col-md-8 control-label">Sexo</label>
                            <select id="sexo" name="sexo" class="form-control" required>
                                <option value="">Seleccionar ..</option>
                                <option @if(old('sexo')==1){{"selected"}}@elseif(old('sexo')=="" && $paciente->sexo == 1){{"selected"}}@endif value="1">HOMBRE</option>
                                <option @if(old('sexo')==2){{"selected"}}@elseif(old('sexo')=="" && $paciente->sexo == 2){{"selected"}}@endif value="2">MUJER</option>
                            </select>      
                            @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>

                        <!--estado civil 1=SOLTERO(A) 2=CASADO(A) 3=VIUDO(A) 4=DIVORCIADO(A) 5=UNION LIBRE-->
                        <div class="form-group col-md-2{{ $errors->has('estadocivil') ? ' has-error' : '' }}" >
                            @if(!$historia->isEmpty())
                            <input id="estadocivil" type="hidden" class="form-control" name="estadocivil" value={{$paciente->estadocivil}}>
                            <p><b>Estado Civil :</b>@if($paciente->estadocivil==1) {{"SOLTERO(A)"}} @elseif($paciente->estadocivil==2) {{"CASADO(A)"}} @elseif($paciente->estadocivil==3) {{"VIUDO(A)"}} @elseif($paciente->estadocivil==5) {{"DIVORCIADO(A)"}} @elseif($paciente->estadocivil==5) {{"UNION LIBRE"}} @elseif($paciente->estadocivil==6) {{"UNION DE HECHO"}} @endif </p>
                            @else
                            <label for="estadocivil" class="col-md-8 control-label">Estado Civil</label>
                            <select id="estadocivil" name="estadocivil" class="form-control" required>
                                <option value="">Seleccionar ..</option>
                                <option @if(old('estadocivil')==1){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 1){{"selected"}}@endif value="1">SOLTERO(A)</option>
                                <option @if(old('estadocivil')==2){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 2){{"selected"}}@endif value="2">CASADO(A)</option>
                                <option @if(old('estadocivil')==3){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 3){{"selected"}}@endif value="3">VIUDO(A)</option>
                                <option @if(old('estadocivil')==4){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 4){{"selected"}}@endif value="4">DIVORCIADO(A)</option>
                                <option @if(old('estadocivil')==5){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 5){{"selected"}}@endif value="5">UNION LIBRE</option>
                                <option @if(old('estadocivil')==6){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 6){{"selected"}}@endif value="6">UNION DE HECHO</option>      
                            </select>
                            @if ($errors->has('estadocivil'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estadocivil') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>                     

                        <!--ocupacion-->
                        <div class="form-group col-md-4{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Ocupación :</b>{{$paciente->ocupacion}} </p>
                            @else
                            <label for="ocupacion" class="col-md-8 control-label">Ocupación</label>
                            <input id="ocupacion" type="text" class="form-control" name="ocupacion" value=@if(old('ocupacion')!='')"{{old('ocupacion')}}"@else"{{ $paciente->ocupacion }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                            @if ($errors->has('ocupacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ocupacion') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>                             

                        </fieldset>

                        <fieldset class="col-md-12"><legend>Datos de ubicación: </legend> 
                            
                        <!--pais-->
                        <div class="form-group col-md-2{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <input id="id_pais" type="hidden" class="form-control" name="id_pais" value={{$paciente->id_pais}}>
                            <p><b>País :</b>@foreach($paises as $pais) @if($paciente->id_pais==$pais->id) {{$pais->nombre}} @endif @endforeach </p>
                            @else
                            <label for="id_pais" class="col-md-8 control-label">Pais</label>
                            <select id="id_pais" name="id_pais" class="form-control" required >
                                    @foreach ($paises as $pais)
                                        <option @if(old('id_pais')==$pais->id){{"selected"}}@elseif(old('id_pais')=="" && $paciente->id_pais == $pais->id){{"selected"}}@endif value="{{$pais->id}}">{{$pais->nombre}}</option>
                                    @endforeach
                            </select>       
                            @if ($errors->has('id_pais'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_pais') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>
                            
                        <!--ciudad-->
                        <div class="form-group col-md-2{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Ciudad :</b> {{$paciente->ciudad}} </p>
                            @else
                            <label for="ciudad" class="col-md-8 control-label">Ciudad</label>
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value=@if(old('ciudad')!='')"{{old('ciudad')}}"@else"{{$paciente->ciudad}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                            @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>
                           
                        <!--direccion-->
                        <div class="form-group col-md-4{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Dirección :</b> {{$paciente->direccion}} </p>
                            @else
                            <label for="direccion" class="col-md-8 control-label">Direccion</label>
                            <input id="direccion" type="text" class="form-control" name="direccion" value=@if(old('direccion')!='')"{{old('direccion')}}"@else"{{ $paciente->direccion }}" @endif required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('direccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div>

                        <!--email-->                        
                        <div class="form-group col-md-4{{ $errors->has('email') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>E-Mail :</b> {{$user_aso->email}} </p>
                            @else
                            <label for="email" class="col-md-8 control-label">E-Mail</label>
                               <input id="email" type="email" class="form-control" name="email" value=@if(old('email')!='')"{{old('email')}}"@else"{{ $user_aso->email }}" @endif required autofocus @if($paciente->id!=$paciente->id_usuario){{'readonly'}}@endif >
                               @if ($errors->has('email'))
                               <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                                @endif
                        </div> 

                        <!--telefono1-->
                        <div class="form-group col-md-2{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Teléfono Domicilio :</b> {{$paciente->telefono1}} </p>
                            @else
                            <label for="telefono1" class="col-md-10 control-label">Teléfono Domicilio</label>
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value=@if(old('telefono1')!='')"{{old('telefono1')}}"@else"{{ $paciente->telefono1 }}" @endif required autofocus >
                            @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div>
                           
                        <!--telefono2-->
                        <div class="form-group col-md-2{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Teléfono Celular :</b> {{$paciente->telefono2}} </p>
                            @else
                            <label for="telefono2" class="col-md-10 control-label">Teléfono Celular</label>
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value=@if(old('telefono2')!='')"{{old('telefono2')}}"@else"{{ $paciente->telefono2 }}" @endif required autofocus >
                            @if ($errors->has('telefono2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono2') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div>

                        <!--LUGAR NACIMIENTO-->
                        <div class="form-group col-md-4{{ $errors->has('lugar_nacimiento') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Lugar de Nacimiento :</b> {{$paciente->lugar_nacimiento}} </p>
                            @else
                            <label for="lugar_nacimiento" class="col-md-8 control-label">Lugar de Nacimiento</label>
                            <input id="lugar_nacimiento" type="text" class="form-control" name="lugar_nacimiento" value=@if(old('lugar_nacimiento')!='')"{{old('lugar_nacimiento')}}"@else"{{ $paciente->lugar_nacimiento }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                            @if ($errors->has('lugar_nacimiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('lugar_nacimiento') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div> 
                       
                        <!--REFERIDO-->
                        <div class="form-group col-md-4{{ $errors->has('referido') ? ' has-error' : '' }}">
                            @if(!$historia->isEmpty())
                            <p><b>Referido :</b> {{$paciente->referido}} </p>
                            @else
                            <label for="referido" class="col-md-8 control-label">Referido</label>
                            <input id="referido" type="text" class="form-control" name="referido" value=@if(old('referido')!='')"{{old('referido')}}"@else"{{ $paciente->referido }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('referido'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('referido') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>

                        </fieldset>

                        <fieldset class="col-md-12"><legend>Datos Médicos:</legend>
                            
                            <!--Grupo Sanguineo-->
                            <div class="form-group col-md-4{{ $errors->has('gruposanguineo') ? ' has-error' : '' }}">
                                @if(!$historia->isEmpty())
                                <p><b>Grupo Sanguíneo :</b> {{$paciente->gruposanguineo}} </p>
                                @else
                                <label for="gruposanguineo" class="col-md-8 control-label">Grupo Sanguíneo</label>
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
                                @endif 
                            </div>

                            <!--alergias-->
                            <div class="form-group col-md-4{{ $errors->has('alergias') ? ' has-error' : '' }}">
                                @if(!$historia->isEmpty())
                                <p><b>Alergias :</b> {{$paciente->alergias}} </p>
                                @else
                                <label for="alergias" class="col-md-8 control-label">Alergias</label>
                                <input id="alergias" type="text" class="form-control" name="alergias" value=@if(old('alergias')!='')"{{old('alergias')}}"@else"{{ $paciente->alergias }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('alergias'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('alergias') }}</strong>
                                    </span>
                                @endif
                                @endif
                            </div>

                            <!--vacunas-->
                            <div class="form-group col-md-4{{ $errors->has('vacuna') ? ' has-error' : '' }}">
                                @if(!$historia->isEmpty())
                                <p><b>Vacunas :</b> {{$paciente->vacuna}} </p>
                                @else
                                <label for="vacuna" class="col-md-8 control-label">Vacunas</label>
                                <input id="vacuna" type="text" class="form-control" name="vacuna" value=@if(old('vacuna')!='')"{{old('vacuna')}}"@else"{{ $paciente->vacuna }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('vacuna'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('vacuna') }}</strong>
                                    </span>
                                @endif
                                @endif
                            </div>

                        </fieldset>

                        <div class="col-md-12">
                                <div class="box box-default collapsed-box Antecedentes">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Antecedentes :</h3>

                                        <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                            <div class="box-body">
                                <!--alcohol-->
                                <div class="form-group col-md-4{{ $errors->has('alcohol') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Consumo Alcohol :</b> {{$paciente->alcohol}} </p>
                                    @else
                                    <label for="alcohol" class="col-md-10 control-label">Consumo Alcohol</label>
                                    <select id="alcohol" class="form-control" name="alcohol"   >
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
                                    @endif
                                </div>

                                <!--hijos_vivos-->
                                <div class="form-group col-md-2{{ $errors->has('hijos_vivos') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Hijos Vivos :</b> {{$paciente->hijos_vivos}} </p>
                                    @else
                                    <label for="hijos_vivos" class="col-md-12 control-label">Hijos Vivos</label>
                                    <input id="hijos_vivos" min=0 type="number" class="form-control" name="hijos_vivos" value=@if(old('hijos_vivos')!='')"{{old('hijos_vivos')}}"@elseif($paciente->hijos_vivos!="")"{{ $paciente->hijos_vivos }}" @else "{{0}}" @endif >
                                    @if ($errors->has('hijos_vivos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hijos_vivos') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--hijos_muertos-->
                                <div class="form-group col-md-2{{ $errors->has('hijos_muertos') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Hijos Muertos :</b> {{$paciente->hijos_muertos}} </p>
                                    @else
                                    <label for="hijos_muertos" class="col-md-12 control-label">Hijos Muertos</label>
                                    <input id="hijos_muertos" min=0 type="number" class="form-control" name="hijos_muertos" value=@if(old('hijos_muertos')!='')"{{old('hijos_muertos')}}"@elseif($paciente->hijos_muertos!="")"{{ $paciente->hijos_muertos }}"@else"{{0}}" @endif >
                                    @if ($errors->has('hijos_muertos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hijos_muertos') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--anticonceptivos-->
                                <div class="form-group col-md-4{{ $errors->has('anticonceptivos') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Anticonceptivos :</b> {{$paciente->anticonceptivos}} </p>
                                    @else
                                    <label for="anticonceptivos" class="col-md-8 control-label">Anticonceptivos</label>
                                    <input id="anticonceptivos" type="text" class="form-control" name="anticonceptivos" value=@if(old('anticonceptivos')!='')"{{old('anticonceptivos')}}"@else"{{ $paciente->anticonceptivos }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                    @if ($errors->has('anticonceptivos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('anticonceptivos') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--antecedentes_pat-->
                                <div class="form-group col-md-4{{ $errors->has('antecedentes_pat') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Antecedentes Patológicos :</b> {{$paciente->antecedentes_pat}} </p>
                                    @else
                                    <label for="antecedentes_pat" class="col-md-8 control-label">Antecedentes Patológicos</label>
                                    <textarea rows="5" cols="50" maxlength="300" id="antecedentes_pat" class="form-control" name="antecedentes_pat" >@if(old('antecedentes_pat')!=''){{old('antecedentes_pat')}}@else{{ $paciente->antecedentes_pat }}@endif</textarea>
                                    @if ($errors->has('antecedentes_pat'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('antecedentes_pat') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--antecedentes_fam-->
                                <div class="form-group col-md-4{{ $errors->has('antecedentes_fam') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Antecedentes Familiares :</b> {{$paciente->antecedentes_fam}} </p>
                                    @else
                                    <label for="antecedentes_fam" class="col-md-8 control-label">Antecedentes Familiares</label>
                                    <textarea rows="5" cols="50" maxlength="300" id="antecedentes_fam" class="form-control" name="antecedentes_fam" >@if(old('antecedentes_fam')!=''){{old('antecedentes_fam')}}@else{{ $paciente->antecedentes_fam }}@endif</textarea>
                                    @if ($errors->has('antecedentes_fam'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('antecedentes_fam') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--antecedentes_quir-->
                                <div class="form-group col-md-4{{ $errors->has('antecedentes_quir') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    <p><b>Antecedentes Quirúrgicos :</b> {{$paciente->antecedentes_quir}} </p>
                                    @else
                                    <label for="antecedentes_quir" class="col-md-8 control-label">Antecedentes Quirúrgicos</label>
                                    <textarea rows="5" cols="50" maxlength="300" id="antecedentes_quir" class="form-control" name="antecedentes_quir" @if(!$historia->isEmpty()){{"readonly"}}@endif >@if(old('antecedentes_quir')!=''){{old('antecedentes_quir')}}@else{{ $paciente->antecedentes_quir }}@endif</textarea>
                                    @if ($errors->has('antecedentes_quir'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('antecedentes_quir') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--transfusion-->
                                <div class="form-group col-md-2{{ $errors->has('transfusion') ? ' has-error' : '' }}" >
                                    @if(!$historia->isEmpty())
                                    <p><b>Transfusión :</b> {{$paciente->transfusion}}</p>
                                    @else
                                    <label for="transfusion" class="col-md-8 control-label">Transfusión</label>
                                    <select id="transfusion" name="transfusion" class="form-control"  >
                                        <option @if(old('transfusion')=="NO"){{"selected"}}@elseif(old('transfusion')=="" && $paciente->transfusion == "NO"){{"selected"}}@endif value="NO">NO</option> 
                                        <option @if(old('transfusion')=="SI"){{"selected"}}@elseif(old('transfusion')=="" && $paciente->transfusion == "SI"){{"selected"}}@endif value="SI">SI</option>
                                    </select>    
                                    @if ($errors->has('transfusion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('transfusion') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--primera_mens-->
                                <div class="form-group col-md-2{{ $errors->has('primera_mens') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    @if($paciente->sexo==2)<p><b>Edad 1ra Menstruación :</b> {{$paciente->primera_mens}} años</p>@endif
                                    @else
                                    <label for="primera_mens" class="col-md-12 control-label">Edad 1ra Menstruación</label>
                                    <input min=0 id="primera_mens" type="number" class="form-control" name="primera_mens" value=@if(old('primera_mens')!='')"{{old('primera_mens')}}"@elseif($paciente->primera_mens!="")"{{$paciente->primera_mens}}"@else "{{0}}" @endif >
                                    @if ($errors->has('primera_mens'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('primera_mens') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--menopausia-->
                                <div class="form-group col-md-2{{ $errors->has('menopausia') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    @if($paciente->sexo==2)<p><b>Edad Menopausia :</b> {{$paciente->menopausia}} años</p>@endif
                                    @else
                                    <label for="menopausia" class="col-md-12 control-label">Edad Menopausia</label>
                                    <input min=0 id="menopausia" type="number" class="form-control" name="menopausia" value=@if(old('menopausia')!='')"{{old('menopausia')}}"@elseif($paciente->menopausia!="")"{{ $paciente->menopausia }}" @else "{{0}}" @endif @if(!$historia->isEmpty()){{"readonly"}}@endif >
                                    @if ($errors->has('menopausia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menopausia') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--parto_cesarea-->
                                <div class="form-group col-md-2{{ $errors->has('parto_cesarea') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    @if($paciente->sexo==2)<p><b>Cantidad Parto Cesárea :</b> {{$paciente->parto_cesarea}} </p>@endif
                                    @else
                                    <label for="parto_cesarea" class="col-md-12 control-label">Cantidad Parto Cesárea</label>
                                    <input min=0 id="parto_cesarea" type="number" class="form-control" name="parto_cesarea" value=@if(old('parto_cesarea')!='')"{{old('parto_cesarea')}}"@elseif($paciente->parto_cesarea!="")"{{ $paciente->parto_cesarea }}" @else "{{0}}" @endif >
                                    @if ($errors->has('parto_cesarea'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parto_cesarea') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--parto_normal-->
                                <div class="form-group col-md-2{{ $errors->has('parto_normal') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    @if($paciente->sexo==2)<p><b>Cantidad Parto Normal :</b> {{$paciente->parto_normal}} </p>@endif
                                    @else
                                    <label for="parto_normal" class="col-md-12 control-label">Cantidad Parto Normal</label>
                                    <input min=0 id="parto_normal" type="number" class="form-control" name="parto_normal" value=@if(old('parto_normal')!='')"{{old('parto_normal')}}"@elseif($paciente->parto_normal!="")"{{ $paciente->parto_normal }}" @else "{{0}}" @endif >
                                    @if ($errors->has('parto_normal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parto_normal') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <!--aborto-->
                                <div class="form-group col-md-2{{ $errors->has('aborto') ? ' has-error' : '' }}">
                                    @if(!$historia->isEmpty())
                                    @if($paciente->sexo==2)<p><b>Cantidad Aborto :</b> {{$paciente->aborto}} </p>@endif
                                    @else
                                    <label for="aborto" class="col-md-12 control-label">Cantidad Aborto</label>
                                    <input min=0 id="aborto" type="number" class="form-control" name="aborto" value=@if(old('aborto')!='')"{{old('aborto')}}"@elseif($paciente->aborto!="")"{{ $paciente->aborto }}" @else "{{0}}" @endif >
                                    @if ($errors->has('aborto'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('aborto') }}</strong>
                                    </span>
                                    @endif
                                    @endif
                                </div>


                            </div>
                                <!-- /.box-body -->
                                </div>
                            <!-- /.box -->
                            </div>    

                        <div class="col-md-12">
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                <h3 class="box-title">Familiar de Contacto: </h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool ban" data-widget="collapse"><i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                            
                                    <!--nombre1familiar-->
                                    <div class="form-group col-md-4{{ $errors->has('nombre1familiar') ? ' has-error' : '' }}">
                                        @if(!$historia->isEmpty())
                                        <p><b>Nombres :</b> {{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}} </p>
                                        @else
                                        <label for="nombre1familiar" class="col-md-8 control-label">Primer Nombre</label>
                                        <input id="nombre1familiar" type="text" class="form-control" name="nombre1familiar" value="{{ $paciente->nombre1familiar }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                        @if ($errors->has('nombre1familiar'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('nombre1familiar') }}</strong>
                                        </span>
                                        @endif
                                        @endif
                                    </div>

                                    @if($historia->isEmpty())
                                    <!--nombre2familiar-->
                                    <div class="form-group col-md-4{{ $errors->has('nombre2familiar') ? ' has-error' : '' }}">
                                    <label for="nombre2familiar" class="col-md-8 control-label">Segundo Nombre</label>
                                    <div class="input-group dropdown col-md-12">
                                        <input id="nombre2familiar" type="text" class="form-control nombrecode dropdown-toggle" name="nombre2familiar" value=@if(old('nombre2familiar')!='')"{{old('nombre2familiar')}}"@else"{{ $paciente->nombre2familiar }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if(!$historia->isEmpty()){{"readonly"}}@endif >
                                        @if($historia->isEmpty())
                                        <ul class="dropdown-menu usuario3">
                                            <li><a data-value="N/A">N/A</a></li>
                                        </ul>
                                        @endif  
                                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                    </div>    
                                    @if ($errors->has('nombre2familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2familiar') }}</strong>
                                    </span>
                                    @endif
                                    </div>

                                    <!--apellido1familiar-->
                                    <div class="form-group col-md-4{{ $errors->has('apellido1familiar') ? ' has-error' : '' }}">
                                    <label for="apellido1familiar" class="col-md-8 control-label">Primer Apellido</label>
                                    <input id="apellido1familiar" type="text" class="form-control" name="apellido1familiar" value="{{ $paciente->apellido1familiar }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" @if(!$historia->isEmpty()){{"readonly"}}@endif >
                                    @if ($errors->has('apellido1familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1familiar') }}</strong>
                                    </span>
                                    @endif
                                    </div>

                                    <!--apellido2familiar-->
                                    <div class="form-group col-md-4{{ $errors->has('apellido2familiar') ? ' has-error' : '' }}">
                                    <label for="apellido2familiar" class="col-md-8 control-label">Segundo Apellido</label>
                                    <div class="input-group dropdown col-md-12">
                                        <input id="apellido2familiar" type="text" class="form-control nombrecode dropdown-toggle" name="apellido2familiar" value=@if(old('apellido2familiar')!='')"{{old('apellido2familiar')}}"@else"{{ $paciente->apellido2familiar }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if(!$historia->isEmpty()){{"readonly"}}@endif >
                                        @if($historia->isEmpty())
                                        <ul class="dropdown-menu usuario4">
                                            <li><a data-value="N/A">N/A</a></li>
                                        </ul>
                                        @endif  
                                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                    </div>    
                                    @if ($errors->has('apellido2familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2familiar') }}</strong>
                                    </span>
                                    @endif
                                    </div>

                                    @endif

                                    <!--parentescofamiliar-->
                                    <div class="form-group col-md-4{{ $errors->has('parentescofamiliar') ? ' has-error' : '' }}" >
                                        @if(!$historia->isEmpty())
                                        <p><b>Parentesco :</b> {{$paciente->parentescofamiliar}}</p>
                                        @else    
                                        <label for="parentescofamiliar" class="col-md-8 control-label">Parentesco</label>
                                        <select id="parentescofamiliar" name="parentescofamiliar" class="form-control">
                                            <option {{$paciente->parentescofamiliar == "Principal" ? 'selected' : ''}} value="Principal">Principal</option>
                                            <option {{$paciente->parentescofamiliar == "Padre/Madre" ? 'selected' : ''}} value="Padre/Madre">Padre/Madre</option>
                                            <option {{$paciente->parentescofamiliar == "Conyugue" ? 'selected' : ''}} value="Conyugue">Conyugue</option>
                                            <option {{$paciente->parentescofamiliar == "Hijo(a)" ? 'selected' : ''}} value="Hijo(a)">Hijo(a)</option>   
                                        </select>  
                                        @if ($errors->has('parentescofamiliar'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('parentescofamiliar') }}</strong>
                                        </span>
                                    @endif
                                    @endif
                                    </div>  

                                    <!--telefono3-->
                                    <div class="form-group col-md-4{{ $errors->has('telefono3') ? ' has-error' : '' }}">
                                        @if(!$historia->isEmpty())
                                        <p><b>Teléfono Familiar :</b> {{$paciente->telefono3}}</p>
                                        @else     
                                        <label for="telefono3" class="col-md-8 control-label">Teléfono Familiar</label>
                                        <input id="telefono3" type="text" class="form-control" name="telefono3" value="{{$paciente->telefono3}}" required >
                                        @if ($errors->has('telefono3'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('telefono3') }}</strong>
                                        </span>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        
                        <!--<fieldset class="col-md-12"><legend>Datos Familiar:</legend>

                        </fieldset> -->

                        <div class="form-group col-xs-6">
                            <div class="col-md-12 col-md-offset-8">
                                <button type="submit" class="btn btn-primary">
                                @if(!$historia->isEmpty()) Continuar @else Generar Historia Clinica @endif
                                </button>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>
        </div> 



    </div>
</div> 


<script src="{{ asset ("/js/paciente.js") }}"></script>


<script type="text/javascript">
@if($historia->isEmpty())
$('#fecha_nacimiento').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    time: false,
    format : 'YYYY-MM-DD',
    lang : 'es',
});


$('#fecha_codigo').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    time: false,
    format : 'YYYY-MM-DD',
    lang : 'es',
});
@endif

$(document).ready(function () {

    /*$('.Antecedentes').boxWidget('toggle');*/

   $('input[type="checkbox"]').on('change', function(e){
   if(e.target.checked){
     $('#favoritesModal2').modal();
   }
});
    
    var sexo = document.getElementById("sexo").value;
        
        if(sexo==2)
        {
          
           $("#primera_mens").show(); 
           $('label[for="primera_mens"]').show(); 

           $("#menopausia").show(); 
           $('label[for="menopausia"]').show();

           $("#parto_normal").show(); 
           $('label[for="parto_normal"]').show();

           $("#parto_cesarea").show(); 
           $('label[for="parto_cesarea"]').show();

           $("#aborto").show(); 
           $('label[for="aborto"]').show(); 


           

        }else{

           $("#primera_mens").hide(); 
           $('label[for="primera_mens"]').hide();
           
           $("#menopausia").hide(); 
           $('label[for="menopausia"]').hide();

           $("#parto_normal").hide(); 
           $('label[for="parto_normal"]').hide();

           $("#parto_cesarea").hide(); 
           $('label[for="parto_cesarea"]').hide();

           $("#aborto").hide(); 
           $('label[for="aborto"]').hide();

        }   

    <?php if($historia->isEmpty()) { ?>
    datos_edad();
    <?php } ?>

    $(function() {
    $('.usuario1 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    $(function() {
    $('.usuario2 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    $(function() {
    $('.usuario3 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    $(function() {
    $('.usuario4 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    $("#sexo").change(function () {
        var sexo = document.getElementById("sexo").value;
        
        if(sexo==2)
        {
          
           $("#primera_mens").show(); 
           $('label[for="primera_mens"]').show(); 

           $("#menopausia").show(); 
           $('label[for="menopausia"]').show();

           $("#parto_normal").show(); 
           $('label[for="parto_normal"]').show();

           $("#parto_cesarea").show(); 
           $('label[for="parto_cesarea"]').show();

           $("#aborto").show(); 
           $('label[for="aborto"]').show(); 


           

        }else{

           $("#primera_mens").hide(); 
           $('label[for="primera_mens"]').hide();
           
           $("#menopausia").hide(); 
           $('label[for="menopausia"]').hide();

           $("#parto_normal").hide(); 
           $('label[for="parto_normal"]').hide();

           $("#parto_cesarea").hide(); 
           $('label[for="parto_cesarea"]').hide();

           $("#aborto").hide(); 
           $('label[for="aborto"]').hide();

        }   

        
        
        
    });



});

function subseguro()
{
        vseguro = document.getElementById("id_seguro").value;

        vseguro =  vseguro.trim();
       
        if (vseguro != ""){
            location.href =" {{route('admisiones.admision2', ['id' => $paciente->id, 'cita' => $cita ])}}/"+vseguro;
        }
}

</script> 
@endsection
