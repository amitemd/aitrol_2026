@extends('sri_electronico.infotributaria.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('infoTributaria.Editar_Informacion')}}</h3>
        </div>
        <div class="box-body">
            <form id="editar_protocolo" method="post" action="{{route('deinfotributaria.update')}}">
                {{ csrf_field() }}

                <input type="hidden" name="id" id="id" value="{{$id}}">
                <?php

                use Sis_medico\De_Info_Tributaria;

                $de = De_Info_Tributaria::find($id);

                $obtener_doc = Sis_medico\De_Maestro_Documentos::find($de->id_maestro_documentos);
                ?>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('numero_factura') ? ' has-error' : '' }}">
                        <label for="numero_factura" class="col-md-20 control-label"> {{trans('infoTributaria.Numero_de')}} <?=ucwords(strtolower($obtener_doc->nombre))?> </label>
                        <input type="text" name="numero_factura" size="20" class="form-control" value="{{$de_info_tributaria->numero_factura}}" readonly>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('secuencial') ? ' has-error' : '' }}">
                        <label for="secuencial" class="col-md-20 control-label"> {{trans('infoTributaria.Secuencial')}}</label>
                        <input type="text" name="secuencial" size="20" class="form-control" value="{{$de_info_tributaria->secuencial_nro}}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                        {{trans('infoTributaria.Actualizar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection