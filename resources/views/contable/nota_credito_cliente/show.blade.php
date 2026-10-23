@extends('contable.nota_credito_cliente.base')
@section('action-content')
<style type="text/css">
 .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 160px;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    *border-right-width: 2px;
    *border-bottom-width: 2px;
   }
</style>
<script type="text/javascript">
    function goBack() {
        location.href="{{ route('nota_credito_cliente.index') }}";
    }
</script>
<!--<script type="text/javascript">
    function goBack() {
        location.href="{{ route('nota_cliente_debito.index') }}";
    }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">-->
    <section class="content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Contable</a></li>
                <li class="breadcrumb-item"><a href="{{route('nota_credito_cliente.index')}}">Nota Crédito Cliente</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle</li>
            </ol>
        </nav>
        <form class="form-vertical " method="post" id="form_nota_credito_actual">
           {{ csrf_field() }}
            <div class="box">
                <div class="box-header">
                    <div class="col-md-12">
                    <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <!--<div class="box-title "><b>Ver Nota de Crédito Cliente</b></div>-->
                                <h5><b>DETALLE NOTA CRÉDITO CLIENTE</b></h5>
                            </div>
                            <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()"
                                style="margin-left: 10px;">
                                <i class="glyphicon glyphicon-arrow-left"
                                aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body dobra">
                    <div class="row header">
                        <div class="col-md-12">
                            <div class="form-row">
                            <div class="col-md-12">&nbsp;</div>
                                <div class=" col-md-1 px-1" >
                                    <label class="label_header">Estado</label>
                                    <input style=" @if(($nota_cred_client->estado)==1) background-color: green; @else background-color: red; @endif" class="form-control col-md-1">           
                                </div>  
                                <div class=" col-md-2 px-1">
                                    <label class="col-md-12 label_header" for="id_nota_credito">ID:</label>
                                    <input class="form-control" type="text" name="id_nota_credito" id="id_nota_credito" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id}} @endif" id="id_factura" readonly>    
                                </div>
                                <div class=" col-md-2 px-1">
                                    <label class="label_header" for="numero_secuencia">Número</label>
                                    <input class="form-control" type="text" id="numero_secuencia"  name="numero_secuencia" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->secuencia}} @endif" readonly>
                                </div>
                                <div class=" col-md-1 px-1">
                                    <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                    <input class="form-control" type="text" name="tipo" id="tipo" value="CLI-CR" readonly>    
                                </div>
                                <div class=" col-md-2 px-1">
                                    <label class="label_header" for="asiento">Asiento</label>
                                    <input class="form-control" type="text" id="asiento" readonly name="asiento" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id_asiento_cabecera}} @endif" readonly>
                                </div>
                                @php
                                    $fech  = substr($nota_cred_client->fecha, 0, 10);
                                    $fech_inver = date("d/m/Y",strtotime($fech));
                                @endphp
                                <div class=" col-md-2 px-1">
                                    <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                    <input class="form-control" type="text" name="fecha_hoy" readonly id="fecha_hoy" value="@if(!is_null($fech_inver)) {{$fech_inver}} @endif">
                                </div>
                                <div class=" col-md-2 px-1">
                                    <label class="col-md-12 label_header" for="fecha_hoy">No. Factura: </label>
                                    <input class="form-control" type="text" name="fecha_hoy" readonly id="fecha_hoy" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->numero_factura}} @endif">
                                </div>
                            </div>
                            <div class="col-md-8 px-1">
                                <input type="hidden" name="total_suma" id="total_suma">
                                <label class="label_header" for="concepto">Concepto:</label>
                                <input class="form-control" type="text" name="concepto" id="concepto" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->concepto}}@endif" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="cliente">Sucursal: </label>
                                <input type="text" id="sucursal" name="sucursal" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->sucursal}} @endif" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="cliente">Punto de Emision: </label>
                                <input type="text" id="punto_emision" name="punto_emision" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->punto_emision}} @endif" readonly>
                            </div>
                            <div class=" col-md-4 px-0">
                                <label class="col-md-12 label_header" for="cliente">Cliente: </label>
                                <input type="text" id="id_cliente" name="id_cliente" class="form-control" value="@if(!is_null($nota_cred_client)) {{$nota_cred_client->id_cliente}} @endif" readonly>
                            </div>
                            <div class="col-md-4 px-0">
                                <label class="col-md-12 label_header" for="nombre_cliente">&nbsp;</label>
                                <input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" value="@if(!is_null($nota_cred_client->cliente)){{$nota_cred_client->cliente->nombre}}@endif" readonly>
                            </div>
                            <div class="col-md-2 px-0" style="padding-top: 13px">
                                <span style="font-family: 'Helvetica general';font-size: 16px;color: black;padding-left: 15px;">Archivo del SRI</span>
                                <input style="width:17px;height:17px;" type="checkbox" id="check_archivo_sri" class="flat-green" name="check_archivo_sri" disabled  value="1"
                                @if($nota_cred_client->check_sri=='1')
                                    checked
                                @endif>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                      <label for="detalle_deuda">DETALLE DE PRODUCTOS</label>
                    </div>
                    <div class="col-md-12" style=" height: 400px; overflow-y: scroll;"> 
                        <table class="table table-responsive table-hover">
                            <thead>
                                <tr>
                                    <th >#</th>
                                    <th>Cantidad</th>
                                    <th >Codigo</th>
                                    <th >Nombre</th>
                                    <th >Valor</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $detalle_producto1=DB::table('ct_devolucion_productos')->where('id_nota_credito',$nota_cred_client->id)->where('estado','0')->get();
                                    $detalle_producto2=DB::table('ct_devolucion_productos')->where('id_nota_credito',$nota_cred_client->id)->where('estado','1')->get();
                                @endphp
                                <tr>
                                    <td colspan="5"> <b> Pendientes</b></td>
                                </tr>
                                @foreach($detalle_producto1 as $x)
                                    <tr>
                                        <td>#</td>
                                        <td>{{$x->cantidad}}</td>
                                        <td>{{$x->codigo}}</td>
                                        <td>{{$x->nombre}}</td>
                                        <td>{{$x->precio}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5">  <b>Enviados</b></td>
                                </tr>
                                @foreach($detalle_producto2 as $x)
                                    <tr>
                                        <td>#</td>
                                        <td>{{$x->cantidad}}</td>
                                        <td>{{$x->codigo}} <textarea class="form-control" disabled id="" cols="3" rows="3">{{$x->descripcion}}</textarea></td>
                                        <td>{{$x->nombre}}</td>
                                        <td>{{$x->precio}}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12" style="padding-left: 30px">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="col-md-12" name="observaciones" id="observaciones" cols="150" rows="3" readonly>@if(!is_null($nota_cred_client)){{$nota_cred_client->observacion}}@endif</textarea>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
