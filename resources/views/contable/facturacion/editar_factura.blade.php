<div class="box box-warning box-solid">
    <div class="header box-header with-border" >
        <div class="box-title col-md-12" ><b style="font-size: 16px;">CREAR FACTURA</b></div>
    </div>
    
	<div class="box-body">

        <form class="form-vertical" role="form" method="POST" action="{{ route('empresa.update', ['id' => $empresa->id]) }}">
        
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 

            <div class="form-group col-xs-6{{ $errors->has('detalle') ? ' has-error' : '' }}">
                <label for="detalle" class="col-xs-6 control-label">Detalle</label>
                <input id="detalle" type="text" class="form-control" name="detalle" value="" required autofocus>
                    @if ($errors->has('detalle'))
                        <span class="help-block">
                            <strong>{{ $errors->first('detalle') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group col-xs-6{{ $errors->has('detalle') ? ' has-error' : '' }}">
                <label for="detalle" class="col-xs-6 control-label">Código</label>
                <input id="detalle" type="text" class="form-control" name="detalle" value="" required autofocus>
                    @if ($errors->has('detalle'))
                        <span class="help-block">
                            <strong>{{ $errors->first('detalle') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group col-xs-6{{ $errors->has('descuento') ? ' has-error' : '' }}">
                <label for="descuento" class="col-xs-6 control-label">Descuento</label>
                <input id="descuento" type="text" class="form-control" name="descuento" value="" required autofocus>
                    @if ($errors->has('descuento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('descuento') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group col-xs-6{{ $errors->has('cantidad') ? ' has-error' : '' }}">
                <label for="cantidad" class="col-xs-6 control-label">Cantidad</label>
                <input id="cantidad" type="text" class="form-control" name="cantidad" value="" required autofocus>
                    @if ($errors->has('cantidad'))
                        <span class="help-block">
                            <strong>{{ $errors->first('cantidad') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group col-xs-6{{ $errors->has('valor') ? ' has-error' : '' }}">
                <label for="valor" class="col-xs-6 control-label">Valor</label>
                <input id="valor" type="text" class="form-control" name="valor" value="" required autofocus>
                    @if ($errors->has('valor'))
                        <span class="help-block">
                            <strong>{{ $errors->first('valor') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group col-xs-6">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                    Agregar Item
                    </button>
                </div>
            </div>

        </form>
    </div>        

                    
</div>