@extends('contable.porcentaje_retencion.base')
@section('action-content')
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="{{route('retenciones.index')}}">Retenciones</a></li>
      <li class="breadcrumb-item active" aria-current="page">Editar</li>
    </ol>
  </nav>
  <form id="enviar_tipo_ambiente" class="form-vertical" role="form" method="POST" action="{{ route('porcentaje_update', ['id' => $retenciones->id]) }}">
    {{ csrf_field() }}
    <div class="box">
      <div class="box-header color_cab">
        <div class="col-md-9">
           <h5><b>DETALLE PORCENTAJE RETENCIONES</b></h5>
        </div>
        <div class="col-md-1 text-right">
          <a href="{{route('retenciones.index')}}" class="btn btn-default btn-gray">
            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
          </a>
        </div>
      </div>
      <div class="separator"></div>
      <div class="box-body dobra">
          <div class="form-group  col-xs-6">
            <label for="codigo" class="col-md-4 texto {{ $errors->has('codigo') ? ' has-error' : '' }}">Código:</label>
            <div class="col-md-7">
              <input id="codigo" name="codigo" type="text" class="form-control" placeholder="Codigo" value="{{ $retenciones->codigo }}"  autocomplete="off" autofocus>
              @if ($errors->has('codigo'))
              <span class="help-block">
                <strong>{{ $errors->first('codigo') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="codigo" class="col-md-4 texto {{ $errors->has('codigo') ? ' has-error' : '' }}">Código Interno:</label>
            <div class="col-md-7">
              <input id="codigo" name="codigo_interno" type="text" class="form-control" placeholder="Codigo" value="{{ $retenciones->codigo_interno }}"  autocomplete="off" autofocus>
              @if ($errors->has('codigo_interno'))
              <span class="help-block">
                <strong>{{ $errors->first('codigo_interno') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="nombre" class="col-md-4 texto {{ $errors->has('nombre') ? ' has-error' : '' }}">Nombre:</label>
            <div class="col-md-7">
              <input id="nombre" name="nombre" type="text"  value="{{ $retenciones->nombre }}" class="form-control" placeholder="nombre" autocomplete="off" autofocus>
            </div>
          </div>
          <div class="form-group col-xs-6">
            <label for="tipo" class="col-md-4 texto">Tipo:</label>
            <div class="col-md-7">
              <select id="tipo" name="tipo" class="form-control"  >
                <option>Seleccionar...</option>
                <option @if(($retenciones->tipo)==1) Selected @endif value="1">Iva</option>
                <option @if(($retenciones->tipo)==2) Selected @endif value="2">Fuente</option>
              </select>
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="valor" class="col-md-4 texto {{ $errors->has('valor') ? ' has-error' : '' }}">Valor:</label>
            <div class="col-md-7">
              <input id="valor" name="valor" value="{{ $retenciones->valor }}"  type="text"  autocomplete="off" class="form-control">
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="cuenta_clientes" class="col-md-4 texto {{ $errors->has('cuenta_clientes') ? ' has-error' : '' }}">Cuenta Cliente:</label>
            <div class="col-md-7">
              <select id="cuenta_clientes" name="cuenta_clientes" class="form-control select2" required>
                @foreach($tipo as $value)
                    <option value="{{$value->id}}" {{$retenciones->cuenta_clientes == $value->id ? 'selected' : ''}}>{{$value->id}} ({{$value->nombre}})</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-xs-6 {{ $errors->has('estados') ? ' has-error' : '' }}">
            <label for="tipo" class="col-md-4 texto">Estado:</label>
            <div class="col-md-7">
              <select id="estados" name="estados" class="form-control" value="{{ old('menu') }}" required >
                <option>Selccionar...</option>
                <option @if(($retenciones->estado)==1) Selected @endif value="1">Activo</option>
                <option @if(($retenciones->estado)==0) Selected @endif value="2">Inactivo</option>
              </select>
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="cuenta_clientes" class="col-md-4 texto {{ $errors->has('cuenta_clientes') ? ' has-error' : '' }}">Cuenta Acreedores:</label>
            <div class="col-md-7">
              <select id="cuenta_acreedores" name="cuenta_acreedores" class="form-control select2" required>
                @foreach($tipo as $value)
                    <option value="{{$value->id}}" {{$retenciones->cuenta_acreedores == $value->id ? 'selected' : ''}}>{{$value->plan}} ({{$value->nombre}})</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="nota" class="col-md-4 texto">Nota:</label>
            <div class="col-md-7">
              <textarea id="nota" name="nota" type="text" class="form-control" autofocus>{{$retenciones->nota}}</textarea>
            </div>
          </div>
          <div class="form-group  col-xs-6">
            <label for="cuenta_deudora" class="col-md-4 texto {{ $errors->has('cuenta_deudora') ? ' has-error' : '' }}">Cuenta Deudora:</label>
            <div class="col-md-7">
              <select id="cuenta_deudora" name="cuenta_deudora" class="form-control select2" required>
                @foreach($tipo as $value)
                    <option value="{{$value->id}}" {{$retenciones->cuenta_deudora == $value->id ? 'selected' : ''}}>{{$value->plan}} ({{$value->nombre}})</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-xs-10 text-center">
            <div class="col-md-6 col-md-offset-4 ">
              <button type="submit" class="btn btn-default btn-gray btn_add">
                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
              </button>
            </div>
          </div>
      </div>
    </div>
  </form>
</section>
<script type="text/javascript">
 $('.select2').select2({
            tags: false
        });
</script>
@endsection
