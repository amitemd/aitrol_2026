@extends('insumos.producto.base')

@section('action-content')
<style>
    .titulo { 
      background-color: lightblue;   
    }
</style> 
<section class="content">

    <!--left-->
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-8">
                    @if(isset($empresa))
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:120px;height:80px;" id="logo_empresa"></dd>
                    @endif
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
                        <div class="form-group col-md-3">
                            <label> Bodega Saliente </label>
                        </div>
                        <div class="form-group col-md-3">
                            <select class="form-control" name="bodega_saliente" id="bodega_saliente">
                                <option value=""> Seleccione ... </option>
                                @foreach($bodega as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label> Bodega Entrante</label>
                        </div>
                        <div class="form-group col-md-3">
                            <select class="form-control" name="bodega_entrante" id="bodega_entrante">
                                <option value="">Seleccione ...</option>
                                @foreach($bodega as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Observaciones</label>
                        </div>
                        <div class="form-group col-md-12">
                            <textarea class="form-control" name="observaciones" id="observaciones" cols="3" rows="3"></textarea>
                        </div>
                        <div class="form-group col-md-12">
                            &nbsp;
                        </div>
                       <!-- <div class="form-group col-md-6">
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
                        </div>-->
                        <div class="form-group col-md-12">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Elija el producto: </label>
                                </div>
                                <div class="col-md-1" style="left: 30px;">
                                    <i class="fa fa-barcode"></i>
                                </div>
                                <div class="col-md-11">
                                    <input type="text" id="inputserie" name="inputserie" class="form-control" onkeydown="elemento_envio(inputserie)" onchange="add(this)" autofocus placeholder="Ingrese codigo Producto">
                                </div>

                            </div>

                        </div> 
                        <div class="form-group col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Elija el pedido: </label>
                                </div>
                                <div class="col-md-11">
                                    <input type="text" id="inputpedido" name="inputpedido" class="form-control" onkeydown="elemento_envio(inputpedido)" onchange="addPedido(this)" autofocus placeholder="Ingrese el numero del pedido">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Elija el lote: </label>
                                </div>
                                <div class="col-md-11">
                                    <input type="text" id="inputlote" name="inputlote" class="form-control" onkeydown="elemento_envio(inputserie)" onchange="add_lote(this)" autofocus placeholder="Ingrese el lote">
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

                        </div>
                        <div class="form-group col-md-12" style="text-align: center;">
                            <button class="btn btn-primary dead" type="button" onclick="saveData(this)" id="buttonSave"> <i class="fa fa-save"></i> &nbsp; Guardar </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


</section>
<script type="text/javascript">
    function elemento_envio(e){
        //alert('entra');
        if(event.keyCode == 13){
            //
            elemento = document.getElementById(e);
            elemento.blur();
        }
        return (event.keyCode!=13);
    }
</script>
@include('insumos.transito.partial')

@endsection