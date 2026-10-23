@extends('contable.rubros_clientes.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('rubros_cliente.index')}}">Rubros Clientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
      </ol>
    </nav>
        @php
            $codigo = $rubro->codigo;
            $codigo = str_replace("/", "_", $codigo);
            //dd($codigo);
        @endphp
    <form class="form-vertical" role="form" method="POST" action="{{ route('rubros_cliente.update', ['id' => $codigo]) }}">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="id" value="{{$rubro->codigo}}">
        <div class="box">
            <div class="box-header color_cab">
                <div class="col-md-9">
                  <h5><b>DETALLE RUBRO CLIENTES</b></h5>
                </div>
                <div class="col-md-3" style="text-align: right;">
                    <button onclick="goBack()" class="btn btn-danger btn-gray">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra">
                <!--Código del Rubro-->
                <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                    <label for="codigo" class="col-md-4 control-label">Código</label>
                    <div class="col-md-7">
                        <input id="codigo" type="text" maxlength="13" class="form-control" name="codigo" value="{{$rubro->codigo}}" style="text-transform:uppercase;"  maxlength="13"  autocomplete="off" required autofocus >
                        @if ($errors->has('codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('codigo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--Nombre o descripción del Rubro-->
                <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                    <div class="col-md-7">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{$rubro->nombre}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('nombre'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Cuenta de Débito que estará relacionada con el Rubro-->
                <div class="form-group col-xs-6">
                    <label for="cuenta_debe" class="col-md-4 control-label">Cuenta Debe</label>
                    <div class="col-md-7">
                        <select id="cuenta_debe" name="cuenta_debe" class="form-control select2_cuentas" style="width: 100%;" required autofocus>
                            <option value="">Seleccione...</option> 
                                @foreach($cuentas as $value)    
                                        <option {{$rubro->debe == $value->id ? 'selected' : ''}} value="{{$value->id}}"> {{$value->id}} - {{$value->nombre}}</option>
                                @endforeach   
                        </select>
                    </div>
                </div>
                <!--Cuenta de Crédito que estará relacionada con el Rubro-->
                <div class="form-group col-xs-6">
                    <label for="cuenta_haber" class="col-md-4 control-label">Cuenta Haber</label>
                    <div class="col-md-7">
                        <select id="cuenta_haber" name="cuenta_haber" class="form-control select2_cuentas" style="width: 100%;" required autofocus>
                            <option value="">Seleccione...</option> 
                                @foreach($cuentas as $value)    
                                        <option {{$rubro->haber == $value->id ? 'selected' : ''}} value="{{$value->id}}"> {{$value->id}} - {{$value->nombre}}</option>
                                @endforeach   
                        </select>
                    </div>
                </div>
                <!--Si el rubro es de Débito o Crédito-->
                <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                    <label for="tipo" class="col-md-4 control-label">Tipo</label>
                    <div class="col-md-7">
                            <select id="tipo" name="tipo" class="form-control" required>
                            <option {{$rubro->tipo == 1 ? 'selected' : ''}} value="1">INGRESO</option>
                            <option {{$rubro->tipo == 0 ? 'selected' : ''}} value="0">EGRESO</option>
                        </select>
                        @if ($errors->has('tipo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Establece si el Rubro está Activo. -->
                <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                    <label for="estado" class="col-md-4 control-label">Estado</label>
                    <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control" required>
                            <option  {{$rubro->estado == '1' ? 'selected' : ''}} value="1" >ACTIVO</option>
                            <option  {{$rubro->estado == '0' ? 'selected' : ''}} value="0" >INACTIVO</option>
                        </select>
                        @if ($errors->has('estado'))
                        <span class="help-block">
                            <strong>{{ $errors->first('estado') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <br><br>
                <!--Button Actualizar-->
                <div class="form-group col-xs-10" style="text-align: center;">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary btn-gray">
                        Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script type="text/javascript">
       
       $(document).ready(function(){
            $('.select2_cuentas').select2({
                tags: false
            });
        });

    function goBack() {
      window.history.back();
    }

</script>
@endsection




