@extends('contable.ventas.base')
@section('action-content')
<style type="text/css">
    .separator {
        width: 100%;
        height: 30px;
        clear: both;
    }
</style>

<script type="text/javascript">
    //Valida que solo ingrese numeros
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    //Retorna a la pagina anterior
    function goBack() {
        window.history.back();
    }
</script>
<section class="content">
    <form id="enviar_nuevo_cliente" class="form-vertical" role="form" method="POST" action="{{ url('/') }}/ventas/updateClienteFactura">
        {{ csrf_field() }}
        <input type="hidden" name="id_venta" value="{{ $venta->id }}" />
        <div class="box">
            <div class="box-header color_cab">
                <div class="col-md-9">
                    <h5><b>Editar cliente</b></h5>
                </div>
                <div class="col-md-1 text-right">
                    <a onclick="goBack()" class="btn btn-default btn-gray">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                    </a>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra">
                <div class="form-group col-xs-8">
                    <label for="tipo_identificacion" class="col-md-2 texto" style="color: blue">{{trans('contableM.identificacion')}}</label>
                    <div class="col-md-3">
                        <select id="tipo_identificacion" name="tipo_identificacion" class="form-control" required autofocus>
                            <option value="">Seleccione.....</option>
                            <option {{$cliente->tipo == 4 ? 'selected' : ''}} value="4">RUC</option>
                            <option {{$cliente->tipo == 5 ? 'selected' : ''}} value="5">CEDULA</option>
                            <option {{$cliente->tipo == 6 ? 'selected' : ''}} value="6">PASAPORTE</option>
                            <option {{$cliente->tipo == 8 ? 'selected' : ''}} value="8">CEDULA/RUC EXTRANJERO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input id="identificacion" type="text" maxlength="13" class="form-control" name="identificacion" value="{{$cliente->identificacion !=''?$cliente->identificacion : ''}}" onchange="return tipo_id(this);" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" autofocus>
                        @if ($errors->has('identificacion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-8{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-2 texto" style="color: blue">{{trans('contableM.nombre')}}</label>
                    <div class="col-md-7">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{$cliente->nombre !=''  ? $cliente->nombre : ''}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" autofocus>
                        @if ($errors->has('nombre'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="separator"></div>
                <div class="form-group col-xs-10 text-center">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-default btn-gray btn_add">
                            <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection