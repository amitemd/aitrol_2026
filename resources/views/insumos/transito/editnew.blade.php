@extends('insumos.producto.base')

@section('action-content')
<style>
    .titulo { 
      background-color: lightblue;   
    }
</style>
<section class="content">

    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-8">
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:120px;height:80px;" id="logo_empresa"></dd>
                </div>
                <div class="col-md-4" style="text-align: right;">
                    <a class="btn btn-primary" href="{{route('transito.index_transito')}}"> <i class="fa fa-arrow-left"></i> &nbsp; Regresar </a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <form action="{{route('transito.storenew')}}" id="aform" method="POST">
                <div class="col-md-12">
                    <div class="row">
                        <input type="hidden" name="id_inv_cab_movimiento" id="id_inv_cab_movimiento" value="{{ $cabecera->id }}">
                        <div class="form-group col-md-3">
                            <label> Bodega Saliente </label>
                        </div>
                        <div class="form-group col-md-3"> @if(isset($cabecera->bodega_origen)) {{$cabecera->bodega_origen->nombre}} @endif
                            <input type="hidden" name="bodega_saliente" id="bodega_saliente" value="{{ $cabecera->id_bodega_origen }}"/>
                        </div>
                        <div class="form-group col-md-3">
                            <label> Bodega Entrante</label>
                        </div>
                        <div class="form-group col-md-3">  @if(isset($cabecera->bodega_destino)) {{$cabecera->bodega_destino->nombre}} @endif
                            <input type="hidden" name="bodega_entrante" id="bodega_entrante" value="{{ $cabecera->id_bodega_origen }}"/>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Observaciones</label>
                        </div>
                        <div class="form-group col-md-12">
                            <textarea class="form-control" name="observaciones" id="observaciones" cols="3" rows="3">{!! $cabecera->observacion !!}</textarea>
                        </div>
                        <div class="form-group col-md-12">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-6">
                            <label> Ingrese Plantilla</label>
                            <select name="plantilla" id="plantilla" class="form-control">
                                <option value="">Seleccione ...</option>
                                @foreach($plantilla as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label></label>
                            <button class="btn btn-primary" type="button" onclick="obtener_plantilla()"> <i class="fa fa-search"></i> </button>
                        </div>
                        <div class="form-group col-md-12">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Eliga el producto: </label>
                                </div>
                                <div class="col-md-1" style="left: 30px;">
                                    <i class="fa fa-barcode"></i>
                                </div>
                                <div class="col-md-11">
                                    <input type="text" id="inputserie" name="inputserie" class="form-control" onkeydown="return (event.keyCode!=13);" onchange="add(this)" autofocus placeholder="Ingrese codigo Producto">
                                </div>

                            </div>

                        </div> 

                        <div class="form-group col-md-12">
                            &nbsp;
                        </div>

                        <div class="form-group col-md-12">
                            <label>Detalles</label>
                        </div>
                        <div class="form-group col-md-12" id="heading">
                        
                        @foreach($cabecera->detalles as $detalle)
                        <div class="panel panel-default details">
                            <div class="panel-heading">
                                <div class="row titulo">
                                    <div class="col-sm-1" style="text-align: left;"><label class="numdetalle"></label></div>
                                    <div class="col-md-9" style="text-align: left;">
                                        <label class="col-md-9"> @if(isset($detalle->inventario)) {{$detalle->inventario->producto->codigo}} | {{$detalle->inventario->producto->nombre}} @else {{$id}} @endif </label>
                                    </div>
                                    <div class="col-md-2" style="text-align: right;">
                                        <button type="button" class="btn btn-danger des">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" class="product" value="{{$detalle->serie}}">
                        
                                </div>
                            </div>
                            <div class="panel-body" style="padding:0;">
                                <div class="col-md-12 table-responsive " style="padding:0 !important;">
                                    <table class="table table-bordered table-hover dataTable noacti"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                                        <thead>
                                            <tr>
                                               
                                                <th tabindex="0">Cantidad</th>
                                                <th tabindex="0">Serie</th>
                                                <th tabindex="0">Lote</th>
                                                <th tabindex="0">Fecha Vence</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td> <input type="hidden" name="id[]" value="{{$detalle->id_producto}}" >  <input type="number" style="width: 80%;height:20px; text-align:center;" class="form-control cneto" name="cantidad[]"  required value="{{$detalle->cantidad}}" onchange="existenciax(this)"> <input type="hidden" name="existencia" class="existencia" value="{{$detalle->existencia}}"> </td>
                                                    <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="serie[]" value="{{$detalle->serie}}" readonly> </td>
                                                    <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="lote[]" value="{{$detalle->lote}}" onchange="lote(this)" readonly> </td>
                                                    <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="vence[]" value="{{$detalle->fecha_vence}}" readonly> 
                                                        <input type="hidden" name="precio[]" value="{{$detalle->valor_unitario}}"> </td>
                                                    <td style="text-align: right;"> <button class="btn btn-danger" type="button" onclick="return $(this).parent().parent().remove()"> <i class="fa fa-trash"></i></button> </td>
                                                </tr>
                                        </tbody>
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                        @endforeach

                        </div>
                        <div class="form-group col-md-12" style="text-align: center;">
                            <button class="btn btn-primary dead" type="button" onclick="update(this)" id="buttonSave"> <i class="fa fa-save"></i> &nbsp; </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>

@include('insumos.transito.partial')

@endsection