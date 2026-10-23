@extends('sri_electronico.infotributaria.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">{{trans('infoTributaria.Crear Información Tributaria </h3>
        </div>
        <div class="box-body">
            <form id="crear_protocolo" method="post" action="{{route('deinfotributaria.store')}}">
                {{ csrf_field() }}
            
                             
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('numero_factura') ? ' has-error' : '' }}">
                        <label for="numero_factura" class="col-md-20 control-label"> Número Factura</label>
                        <input type="text" name="numero_factura" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('secuencial_nro') ? ' has-error' : '' }}">
                        <label for="secuencial_nro" class="col-md-20 control-label"> Número Secuencial</label>
                        <input type="text" name="secuencial_nro" size="20" class="form-control" value="" required maxlength="100">
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
</section>

@endsection