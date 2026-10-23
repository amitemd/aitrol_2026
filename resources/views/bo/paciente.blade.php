@extends('bo.base_agenda')

@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 >Agregar nuevo Paciente</h3>
                    <h3 class="box-title">Datos del Asegurado</h3>
                </div>
                <div class="box-body">
                    <div class="alert alert-warning m1 oculto">
                        <strong>Atencion!</strong> <span id="alertms"></span>
                    </div>
                    <div class="alert alert-warning m2 oculto">
                        <strong>Atencion!</strong> <span id="alertms2"></span>
                    </div>
                    <span class="help-block">@if($user != Array()){{'**Usuario ya registrado en el sistema'}}@endif</span>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('solicitud.guarda_paciente') }}" id="form">
                        {{ csrf_field() }}
                        <input id="doctor"  type="hidden"  name="doctor" value="{{$id}}" " >
                        <input id="fecha"  type="hidden"  name="fecha" value="{{$fecha}}" " >
                     
                        <!--cedula-->
                        <div class="form-group col-md-6 {{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Cédula</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control" name="id" value="@if($user != Array()) {{$user[0]->id}} @elseif($i != '' && $i != '0'){{$i}} @else{{old('id')}}@endif" required autofocus onchange="usuario();" @if($user != Array()){{'readonly'}}@endif >
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        

                        <!--parentesco-->
                        <div class="form-group col-md-6{{ $errors->has('parentesco') ? ' has-error' : '' }}">
                            <label for="parentesco" class="col-md-4 control-label">Parentesco</label>
                            <div class="col-md-7">
                                <select id="parentesco" name="parentesco" class="form-control" required >

                                    <option value="">Seleccione .. </option>
                                    <option @if(old('parentesco')== 'Principal') selected @endif value="Principal">Principal</option>
                                    <option @if(old('parentesco')== 'Padre/Madre') selected @endif value="Padre/Madre">Padre/Madre</option> 
                                    <option @if(old('parentesco')== 'Conyugue') selected @endif value="Conyugue">Conyugue</option>
                                    <option @if(old('parentesco')== 'Hijo(a)') selected @endif value="Hijo(a)">Hijo(a)</option>
                                    <option @if(old('parentesco')== 'Hermano(a)') selected @endif value="Hermano(a)">Hermano(a)</option>
                                    <option @if(old('parentesco')== 'Sobrino(a)') selected @endif value="Sobrino(a)">Sobrino(a)</option>
                                    <option @if(old('parentesco')== 'Nieto(a)') selected @endif value="Nieto(a)">Nieto(a)</option>
                                    <option @if(old('parentesco')== 'Primo(a)') selected @endif value="Primo(a)">Primo(a)</option>
                                    <option @if(old('parentesco')== 'Familiar') selected @endif value="Familiar">Familiar</option>
                                     
                                </select>  
                                @if ($errors->has('parentesco'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parentesco') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 

                        <!--id_tipo_seguro-->
                        <div class="form-group col-md-6{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-md-4 control-label">Tipo seguro</label>
                            <div class="col-md-7">
                                <select id="id_tipo_seguro" name="id_seguro" class="form-control" required>
                                        <option value="">Seleccione ..</option>
                                    @foreach($seguros as $seguro) 
                                        <option @if(old('id_seguro')== $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>   
                                    @endforeach
                                </select>  
                                
                                @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        

                        <!--Primer nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-4 control-label">Primer Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre1" type="text" class="form-control" name="nombre1" value="@if($user != Array()){{$user[0]->nombre1}}@else{{ old('nombre1') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="busca_usuario_nombre();">
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-md-4 control-label">Segundo Nombre</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="nombre2" type="text" class="form-control nombrecode dropdown-toggle" name="nombre2" value="@if($user != Array()){{$user[0]->nombre2}}@else{{ old('nombre2') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus @if($user != Array()){{'readonly'}}@endif required onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        
           
                        <!--primer apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-4 control-label">Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido1" type="text" class="form-control" name="apellido1" value="@if($user != Array()){{$user[0]->apellido1}}@else{{ old('apellido1') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="busca_usuario_nombre();">
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-6 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-4 control-label">Segundo Apellido</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="apellido2" type="text" class="form-control nombrecode dropdown-toggle" name="apellido2" value="@if($user != Array()){{$user[0]->apellido2}}@else{{ old('apellido2') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus @if($user != Array()){{'readonly'}}@endif required onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                      
                                             
                        <!--telefono1-->
                        <div class="form-group col-md-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">Telefono Domicilio</label>

                            <div class="col-md-7">
                                <input id="telefono1" type="text" class="form-control" name="telefono1" value="@if($user != Array()){{$user[0]->telefono1}}@else{{ old('telefono1') }}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>

                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-md-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-4 control-label">Telefono Celular</label>

                            <div class="col-md-7">
                                <input id="telefono2" type="text" class="form-control" name="telefono2" value="@if($user != Array()){{$user[0]->telefono2}}@else{{ old('telefono2')}}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>

                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Pais-->
                        <div class="form-group col-md-6{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            <label for="id_pais2" class="col-md-4 control-label">Pais</label>
                            <div class="col-md-7">
                            @if($user != Array())
                                <input id="id_pais" type="hidden" class="form-control" name="id_pais" value="@if($user != Array()){{$user[0]->id_pais}}@else{{old('id_pais')}}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>
                                <input id="npais" type="text" class="form-control" name="npais" value=@if($user!=Array())@foreach($pais as $pais2)@if($user[0]->id_pais==$pais2->id)"{{$pais2->nombre}}"@endif
                                @endforeach
                                @else"{{old('id_pais')}}"
                                @endif" required autofocus readonly="readonly">
                            @else    
                                <select onchange="pais();" id="id_pais" name="id_pais" class="form-control" " >
                                    @foreach($pais as $pais2)
                                    <option id="op_pais" name="op_pais" @if(old('id_pais') == $pais2->id) selected @elseif ($user != Array()) @if ($user[0]->id_pais == $pais2->id) selected  @endif @endif value="{{$pais2->id}}">{{$pais2->nombre}}</option>
                                    @endforeach
                                </select>

                            @endif      
                                @if ($errors->has('id_pais'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right"  name="fecha_nacimiento" id="fecha_nacimiento" value="@if($user != Array()) {{$user[0]->fecha_nacimiento}} @elseif(old('fecha_nacimiento')!='') {{ old('fecha_nacimiento') }} @endif" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="copiafecha(event);"  placeholder="1980/01/01">
                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif    
                            </div>
                        </div>


                        <!--email-->                        
                        <div class="form-group col-md-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control" name="email" value="@if($user != Array()){{$user[0]->email}}@elseif(old('email')!=''){{ old('email') }}@else @ @endif" required @if($user != Array()){{'readonly'}}@endif>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                            
                        <div class="form-group col-md-12">
                            <h4 class="box-title">Datos del paciente</h4>
                        </div>
                        

                        <!--cedula-->
                        <div class="form-group col-md-6 {{ $errors->has('id2') ? ' has-error' : '' }}">
                            <label for="id2" class="col-md-4 control-label">Cédula</label>
                            <div class="col-md-7">
                                <input id="id2" maxlength="10" type="text" class="form-control" name="id2" value="{{ old('id2') }}" required autofocus onkeyup="validarCedula(this.value);">
                                @if ($errors->has('id2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>                  
                        
                        <!--PRIMER NOMBRE-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre12') ? ' has-error' : '' }}">
                            <label for="nombre12" class="col-md-4 control-label">Primer Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre12" type="text" class="form-control" name="nombre12" value="{{ old('nombre12') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="busca_usuario_nombre1();">
                                @if ($errors->has('nombre12'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre12') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre22') ? ' has-error' : '' }}">
                            <label for="nombre22" class="col-md-4 control-label">Segundo Nombre</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="nombre22" type="text" class="form-control nombrecode dropdown-toggle" name="nombre22" value="{{ old('nombre22') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="busca_usuario_nombre1();">
                                    <ul class="dropdown-menu usuario3" id="dnombre22">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div> 
                                @if ($errors->has('nombre22'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre22') }}</strong>
                                </span>
                                @endif

                                       
                             </div>
                        </div>
                        
           
                        <!--primer apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido12') ? ' has-error' : '' }}">
                            <label for="apellido12" class="col-md-4 control-label">Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido12" type="text" class="form-control" name="apellido12" value="{{ old('apellido12') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="busca_usuario_nombre1();">
                                @if ($errors->has('apellido12'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido12') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-6 {{ $errors->has('apellido22') ? ' has-error' : '' }}">
                            <label for="apellido22" class="col-md-4 control-label">Segundo Apellido</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="apellido22" type="text" class="form-control nombrecode dropdown-toggle" name="apellido22" value="{{ old('apellido22') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="busca_usuario_nombre1();">
                                    <ul class="dropdown-menu usuario3">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div> 
                                @if ($errors->has('apellido22'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido22') }}</strong>
                                </span>
                                @endif

                                       
                             </div>
                        </div>
                      
                                             
                        <!--telefono1-->
                        <div class="form-group col-md-6{{ $errors->has('telefono12') ? ' has-error' : '' }}">
                            <label for="telefono12" class="col-md-4 control-label">Telefono Domicilio</label>

                            <div class="col-md-7">
                                <input id="telefono12" type="text" class="form-control" name="telefono12" value="{{ old('telefono12') }}" required autofocus>

                                @if ($errors->has('telefono12'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono12') }}</strong>
                                    </span>
                                @endif
                                
                            </div>
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-md-6{{ $errors->has('telefono22') ? ' has-error' : '' }}">
                            <label for="telefono22" class="col-md-4 control-label">Telefono Celular</label>

                            <div class="col-md-7">
                                <input id="telefono22" type="text" class="form-control" name="telefono22" value="{{ old('telefono22') }}" required autofocus>

                                @if ($errors->has('telefono22'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono22') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Pais-->
                        <div class="form-group col-md-6{{ $errors->has('id_pais2') ? ' has-error' : '' }}">
                            <label for="id_pais2" class="col-md-4 control-label">Pais</label>
                            <div class="col-md-7">
                                
                                <select id="id_pais2" name="id_pais2" class="form-control">
                                    @foreach($pais as $pvalor)
                                    <option @if(old('id_pais2') == $pvalor->id) selected @endif value="{{$pvalor->id}}">{{$pvalor->nombre}}</option>
                                    @endforeach
                                </select>  
                                @if ($errors->has('id_pais2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-6{{ $errors->has('fecha_nacimiento2') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento2" class="col-md-4 control-label">Fecha Nacimiento</label>

                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input onchange="edad();" id="fecha_nacimiento2" type="text" class="form-control" name="fecha_nacimiento2" value="{{ old('fecha_nacimiento2') }}" required autofocus placeholder="1980/01/01">
                                    @if ($errors->has('fecha_nacimiento2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento2') }}</strong>
                                    </span>
                                    @endif
                                </div>        
                            </div>
                        </div>
                        
                        <!-- Div de edad -->
                        <div class="form-group col-md-6{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                            <label for="Xedad" class="col-md-4 control-label">Edad</label>

                            <div class="col-md-7">
                                <input id="Xedad" type="text" class="form-control" name="Xedad" readonly="readonly">

                                @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--menoredad-->
                        <div class="form-group col-md-6{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="menoredad" class="col-md-4 control-label">Menor de Edad</label>

                            <div class="col-md-7">
                                <input id="tmenoredad" type="text" class="form-control" name="tmenoredad" required autofocus  readonly="readonly">
                                <input id="menoredad" type="hidden" class="form-control" name="menoredad" >

                                @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--terceraedad-->
                        <div class="form-group col-md-6" >
                            <label for="terceraedad" class="col-md-4 control-label">Tercera Edad</label>

                            <div class="col-md-7">
                                <input id="terceraedad" type="text" class="form-control" name="terceraedad" required autofocus  readonly="readonly">
                             </div>
                        </div>

                        
                        

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        </div>  
                    </form>
                </div>    
                
            </div>
        </div>
    </div>    
</section>



<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>

<script type="text/javascript">


$(function () {
        $('#fecha_nacimiento2').datetimepicker({
            format: 'YYYY/MM/DD'


            });
        
        $('#fecha_nacimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
        <?php if($user == Array()) {?>
 <?php } ?>       
    });

$("#fecha_nacimiento").on("dp.change", function(e) {
     copiafecha(e);
     
});




$(document).ready(function () {

    edad();
    var valor2 = document.getElementById("id").value;
        var nombre1 = document.getElementById("nombre1").value;
        var nombre2 = document.getElementById("nombre2").value;
        var apellido1 = document.getElementById("apellido1").value;
        var apellido2 = document.getElementById("apellido2").value;
        var telefono1 = document.getElementById("telefono1").value;
        var telefono2 = document.getElementById("telefono2").value;
        var fecha = document.getElementById("fecha_nacimiento").value;
        var pais = document.getElementById("id_pais").value;
        var relacion = document.getElementById("parentesco").value;

     if (relacion == "Principal") {
         
            $("#id2").val(valor2);
            $("#nombre12").val(nombre1); 
            $("#nombre22").val(nombre2);
            $("#apellido12").val(apellido1);
            $("#apellido22").val(apellido2);
            $("#telefono12").val(telefono1);
            $("#telefono22").val(telefono2);
            $("#fecha_nacimiento2").val(fecha);
            $("#id_pais2").val(pais);
            $("#id2").attr("readonly","readonly");
            $("#nombre12").attr("readonly","readonly"); 
            $("#nombre22").attr("readonly","readonly");
            $("#apellido12").attr("readonly","readonly");
            $("#apellido22").attr("readonly","readonly");
            $("#telefono12").attr("readonly","readonly");
            $("#telefono22").attr("readonly","readonly");
            $("#fecha_nacimiento2").attr("readonly","readonly");
            $("#id_pais2").attr("readonly","readonly");
            

        }else{
            $("#id2").removeAttr("readonly");
            $("#nombre12").removeAttr("readonly"); 
            $("#nombre22").removeAttr("readonly");
            $("#apellido12").removeAttr("readonly");
            $("#apellido22").removeAttr("readonly");
            $("#telefono12").removeAttr("readonly");
            $("#telefono22").removeAttr("readonly");
            $("#fecha_nacimiento2").removeAttr("readonly");
            $("#id_pais2").removeAttr("readonly");

        }
    

    $("#parentesco").click(function () {
        var valor2 = document.getElementById("id").value;
        var nombre1 = document.getElementById("nombre1").value;
        var nombre2 = document.getElementById("nombre2").value;
        var apellido1 = document.getElementById("apellido1").value;
        var apellido2 = document.getElementById("apellido2").value;
        var telefono1 = document.getElementById("telefono1").value;
        var telefono2 = document.getElementById("telefono2").value;
        var fecha = document.getElementById("fecha_nacimiento").value;
        var pais = document.getElementById("id_pais").value;
        var relacion = document.getElementById("parentesco").value;
        

        if (relacion == "Principal") {
            $("#id2").val(valor2);
            $("#nombre12").val(nombre1); 
            $("#nombre22").val(nombre2);
            $("#apellido12").val(apellido1);
            $("#apellido22").val(apellido2);
            $("#telefono12").val(telefono1);
            $("#telefono22").val(telefono2);
            $("#fecha_nacimiento2").val(fecha);
            $("#id_pais2").val(pais);
            edad();
             
            $("#id2").attr("readonly","readonly");
            $("#nombre12").attr("readonly","readonly"); 
            $("#nombre22").attr("readonly","readonly");
            $("#apellido12").attr("readonly","readonly");
            $("#apellido22").attr("readonly","readonly");
            $("#telefono12").attr("readonly","readonly");
            $("#telefono22").attr("readonly","readonly");
            $("#fecha_nacimiento2").attr("readonly","readonly");
            $("#id_pais2").attr("readonly","readonly");
            

        }else{
            $("#id2").removeAttr("readonly");
            $("#nombre12").removeAttr("readonly"); 
            $("#nombre22").removeAttr("readonly");
            $("#apellido12").removeAttr("readonly");
            $("#apellido22").removeAttr("readonly");
            $("#telefono12").removeAttr("readonly");
            $("#telefono22").removeAttr("readonly");
            $("#fecha_nacimiento2").removeAttr("readonly");
            $("#id_pais2").removeAttr("readonly");

        }
    }); 

    <?php if($user == Array()) {?> 

    $("#id").keyup(function () {
        var relacion = document.getElementById("parentesco").value;    
        var value = $(this).val();
        validarCedula(value);

        
        if (relacion == "Principal") {
           $("#id2").val(value); 
        } 
    });



    $("#nombre1").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#nombre12").val(value);
      }
    });

    $("#nombre2").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
      
        if (relacion == "Principal") {
        $("#nombre22").val(value);}
    });

    $("#apellido1").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
      
        if (relacion == "Principal") {
        $("#apellido12").val(value);}
    });

    $("#apellido2").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#apellido22").val(value);}
    });

    $("#telefono1").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#telefono12").val(value); }
    });

    $("#telefono2").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
       
        if (relacion == "Principal") {
        $("#telefono22").val(value);}
    });
    


    $(function() {
    $('.usuario1 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;

        if (relacion == "Principal") {
        $("#nombre22").val('(' + $(this).attr('data-value') + ')');

        } 

        busca_usuario_nombre();
    });})

    $(function() {
    $('.usuario2 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;
 
        if (relacion == "Principal") {
        $("#apellido22").val('(' + $(this).attr('data-value') + ')');} 

        busca_usuario_nombre();
    });})


    <?php } ?>

    $(function() {
    $('.usuario3 a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode').val('(' + $(this).attr('data-value') + ')');
        busca_usuario_nombre1(); 

    });})



    




});

function copiafecha(e)
{
     
    var relacion = document.getElementById("parentesco").value;
    if (relacion == "Principal") {  
        var valor = document.getElementById("fecha_nacimiento").value;
        $("#fecha_nacimiento2").val(valor)
        edad();
    }
      
}
function usuario()
{
        vcedula = document.getElementById("id").value;
        
        vcedula =  vcedula.trim();
       
        if (vcedula != ""){
            location.href = "{{ url('privados/agendar/paciente')}}/{{$id}}/"+vcedula+"/{{$fecha}}";
        }
}

var busca_usuario_nombre = function ()
{
    
    var jnombre1 = document.getElementById('nombre1').value;
    var jnombre2 = document.getElementById('nombre2').value;
    var japellido1 = document.getElementById('apellido1').value;
    var japellido2 = document.getElementById('apellido2').value;

    if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
    {
        
        $('#alertms').empty().html('');
        $(".m1").addClass("oculto"); 
        $.ajax({
        type: 'get',
        url:'{{ route('paciente.pacientexnombre')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
        success: function(data){
            
                if(data!='0'){
                    $('#alertms').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                    $(".m1").removeClass("oculto");    
                }    
                
            },
        
        })
    
    }    
    
    
   

    
    
}

var busca_usuario_nombre1 = function ()
{
    
    var jnombre1 = document.getElementById('nombre12').value;
    var jnombre2 = document.getElementById('nombre22').value;
    var japellido1 = document.getElementById('apellido12').value;
    var japellido2 = document.getElementById('apellido22').value;

    if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
    {
        
        $('#alertms2').empty().html('');
        $(".m2").addClass("oculto"); 
        $.ajax({
        type: 'get',
        url:'{{ route('paciente.pacientexnombre')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
        success: function(data){
            
                if(data!='0'){
                    $('#alertms2').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                    $(".m2").removeClass("oculto");    
                }    
                
            },
        
        })
    
    }    
    
    
   

    
    
}
    
</script>

@endsection

