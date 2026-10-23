<?php /*
<p class="col-md-1"><b>Cédula:</b></p>
<p class="col-md-2">{{$user_aso->id}} </p>
<p class="col-md-1"><b>Nombres:</b></p>
<p class="col-md-4">{{$user_aso->nombre1}} {{$user_aso->nombre2}} {{$user_aso->apellido1}} {{$user_aso->apellido2}} </p>
<p class="col-md-2"><b>Fecha Nacimiento:</b></p>
<p class="col-md-2">{{$user_aso->fecha_nacimiento}}</p>
<p class="col-md-1"><b>Mail : </b></p>
<p class="col-md-3">{{$user_aso->email}}</p>
<input id="fecha_nacimiento_prin" type="hidden" name="fecha_nacimiento_prin" value="{{$user_aso->fecha_nacimiento}}" >
<input id="id_prin" type="hidden" name="id_prin" value="{{$paciente->id_usuario}}" >

@if($historia=='0')
<a href="{{route('admisiones.editar_principal',['id_paciente' => $paciente->id])}}" data-toggle="modal" data-target="#Editar_Principal" class="btn btn-success btn-xs col-md-offset-10">Actualizar</a>

<a href="{{route('admisiones.crear_principal',['id_paciente' => $paciente->id])}}" data-toggle="modal" data-target="#Crear_Principal" class="btn btn-success btn-xs ">Crear</a>
@endif*/ ?>
<div class="form-group col-md-2{{ $errors->has('cedula_principal') ? ' has-error' : '' }}">
    <label for="cedula_principal" class="col-md-12 control-label">Cédula</label>
    <input id="cedula_principal" type="text" class="form-control input-sm" name="cedula_principal" value=@if(old('cedula_principal')!='')"{{old('cedula_principal')}}"@else"{{$user_aso->id}}"@endif required autofocus onkeyup="validarCedula(this.value);" autocomplete="nope">
    @if ($errors->has('cedula_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('cedula_principal') }}</strong>
    </span>
    @endif
</div>
<div class="form-group col-md-2{{ $errors->has('apellido1_principal') ? ' has-error' : '' }}">
    <label for="apellido1_principal" class="col-md-12 control-label">Primer Apellido</label>
    <input id="apellido1_principal" type="text" class="form-control input-sm" name="apellido1_principal" value=@if(old('apellido1_principal')!='')"{{old('apellido1_principal')}}"@else"{{$user_aso->apellido1}}"@endif required autofocus autocomplete="nope">
    @if ($errors->has('apellido1_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('apellido1_principal') }}</strong>
    </span>
    @endif
</div>
<div class="form-group col-md-2{{ $errors->has('apellido2_principal') ? ' has-error' : '' }}">
    <label for="apellido2_principal" class="col-md-12 control-label">Segundo Apellido</label>
    <input id="apellido2_principal" type="text" class="form-control input-sm" name="apellido2_principal" value=@if(old('apellido2_principal')!='')"{{old('apellido2_principal')}}"@else"{{$user_aso->apellido2}}"@endif required autofocus autocomplete="nope">
    @if ($errors->has('apellido2_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('apellido2_principal') }}</strong>
    </span>
    @endif
</div>
<div class="form-group col-md-2{{ $errors->has('nombre1_principal') ? ' has-error' : '' }}">
    <label for="nombre1_principal" class="col-md-12 control-label">Primer Nombre</label>
    <input id="nombre1_principal" type="text" class="form-control input-sm" name="nombre1_principal" value=@if(old('nombre1_principal')!='')"{{old('nombre1_principal')}}"@else"{{$user_aso->nombre1}}"@endif required autofocus autocomplete="nope">
    @if ($errors->has('nombre1_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('nombre1_principal') }}</strong>
    </span>
    @endif
</div>
<div class="form-group col-md-2{{ $errors->has('nombre2_principal') ? ' has-error' : '' }}">
    <label for="nombre2_principal" class="col-md-12 control-label">Segundo Nombre</label>
    <input id="nombre2_principal" type="text" class="form-control input-sm" name="nombre2_principal" value=@if(old('nombre2_principal')!='')"{{old('nombre2_principal')}}"@else"{{$user_aso->nombre2}}"@endif required autofocus autocomplete="nope">
    @if ($errors->has('nombre2_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('nombre2_principal') }}</strong>
    </span>
    @endif
</div>
<div class="form-group col-md-4{{ $errors->has('fecha_nacimiento_principal') ? ' has-error' : '' }}">
    <label for="fecha_nacimiento_principal" class="col-md-12 control-label">Fecha Nacimiento</label>
    <div class="input-group date col-md-12">
        <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
        </div>
        <input id="fecha_nacimiento_principal" type="text" class="form-control input-sm" name="fecha_nacimiento_principal" value=@if(old('fecha_nacimiento_principal')!='')"{{old('fecha_nacimiento_principal')}}"@else"{{ $user_aso->fecha_nacimiento }}" @endif required autofocus >
    </div>    
    @if ($errors->has('fecha_nacimiento_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('fecha_nacimiento_principal') }}</strong>
    </span>
    @endif 
</div>
<div class="form-group col-md-8{{ $errors->has('email_principal') ? ' has-error' : '' }}">
    <label for="email_principal" class="col-md-8 control-label">E-Mail</label>
    <input id="email_principal" type="email_principal" class="form-control input-sm" name="email_principal" value=@if(old('email_principal')!='')"{{old('email_principal')}}"@else"{{ $user_aso->email_principal }}" @endif required autofocus  >
   	@if ($errors->has('email_principal'))
    <span class="help-block">
        <strong>{{ $errors->first('email_principal') }}</strong>
    </span>
    @endif                   
</div>  