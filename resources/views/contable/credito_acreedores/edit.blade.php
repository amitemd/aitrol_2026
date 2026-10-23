@extends('contable.credito_acreedores.base')
@section('action-content')
<style type="text/css">
    .ui-autocomplete
        {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
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
        location.href="{{ URL::previous() }}";
    }
</script>

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page"{{trans('contableM.nuevo')}} {{trans('contableM.notacredito')}}</li>
      </ol>
    </nav> 
<form class="form-vertical " method="post" id="form_guardado">
            {{ csrf_field() }}
            <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-3">
                                    <div class="box-title " ><b>Visualizador - {{trans('contableM.notacredito')}} {{trans('contableM.acreedor')}}</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                            <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                        </a>
                                        <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante->id_asiento_cabecera])}}" target="_blank">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diaro
                                        </a>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 3px; padding: 7px 23px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body dobra">
                        <div class="row header">
                            <div class="col-md-12">
                                <div class="form-row ">
                                    
                                    <div class=" col-md-1 px-1" >
                                        <label class="label_header">{{trans('proforma.estado')}}</label>
                                        @if($comprobante->estado == 1)
                                            <div style="background-color: green; " class="form-control col-md-1"></div>
                                        @else
                                            <div style="background-color: red; " class="form-control col-md-1"></div>
                                        @endif
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}</label>
                                            <input class="form-control " type="text" name="id_factura" id="id_factura" value="@if(!is_null($comprobante->id)) {{$comprobante->id}} @endif" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                            <input class="form-control " type="text" id="numero_factura" value="@if(!is_null($comprobante->id)) {{$comprobante->secuencia}} @endif"  name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-1 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}:</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" value="ACR-DB" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                            <input class="form-control " type="text" id="asiento" value="@if(!is_null($comprobante)) {{$comprobante->id_asiento_cabecera}} @endif" name="asiento" readonly>
                                            @if(!is_null($iva_param))

                                                <input type="text" name="iva_par" id="iva_par" class="hidden" value="{{$iva_param->iva}}">
                                            @endif
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                            <input class="form-control " type="text" name="fecha_hoy" id="fecha_hoy" value="@if(!is_null($comprobante)) {{$comprobante->fecha}} @endif" readonly >
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="fecha_caducidad">{{trans('contableM.fechacaducidad')}}: </label>
                                        <input class="form-control " type="text" name="fecha_caducidad" value="@if(!is_null($comprobante)) {{$comprobante->fechand}} @endif" id="fecha_caducidad" readonly>
                                    
                                    </div>
                                </div>
                                
                                    <div class=" col-md-2 px-1">
                                            {{ csrf_field() }} 
                                            <input type="hidden" name="superavit" id="superavit" value="0">
                                            <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.acreedor')}} </label>
                                            <input type="hidden" name="id_proveedor" id="id_proveedor">
                                            <input type="text" id = "nombre_proveedor" name="nombre_proveedor" value="@if(!is_null($comprobante->proveedor)) {{$comprobante->proveedor->nombrecomercial}} @endif" class= "form-control form-control-sm nombre_proveedor" readonly >
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label class="control-label label_header" for="autorizacion">{{trans('contableM.autorizacion')}} </label>
                                        <input type="text" name="autorizacion" id="autorizacion" class="form-control" value="@if(!is_null($comprobante)) {{$comprobante->autorizacion}} @endif" readonly>
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label class="col-md-12 label_header control-label" for="serie">Serie</label>
                                        <input type="text" class="form-control" id="serie" name="serie"  value="@if(!is_null($comprobante)) {{$comprobante->serie}} @endif" readonly>
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label for="secuencia" class="label_header col-md-12 control-label">{{trans('contableM.secuencia')}}</label>
                                        <input type="text" class="form-control" id="secuencia" name="secuencia" value="@if(!is_null($comprobante)) {{$comprobante->secuencia}} @endif" readonly>
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="fechand">Fecha N/D: </label>
                                        <input class="form-control " type="date" name="fechand" id="fechand" value="@if(!is_null($comprobante)) {{$comprobante->fechand}} @endif" value="{{date('Y-m-d')}} " readonly>
                                    
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">{{trans('contableM.creditotributario')}}</label>
                                        <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas " style="width: 100%; heigth: 22px">
                                            <option value="">{{trans('proforma.seleccion')}}...</option>
                                            @foreach($c_tributario as $value)
                                                <option {{$value->codigo == $comprobante->id_credito_tributario ? 'selected' : ''}} value="{{$value->codigo}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 px-1">
                                            <label class="label_header" for="concepto">{{trans('contableM.concepto')}}:</label>
                                            <input class="form-control  col-md-12" type="text" name="concepto" value="@if(!is_null($comprobante)) {{$comprobante->concepto}} @endif" id="concepto"  readonly >
                                    </div>
                                    <div class=" col-md-2 px-1">
                                      <label class="col-md-12 label_header" for="nro_factura">{{trans('contableM.valorcontable')}}: </label>
                                      <input class="form-control " type="text" name="val_contable" id="val_contable" value="@if(!is_null($comprobante)) {{$comprobante->valor_contable}} @endif">
                                    </div>
                                    <div class=" col-md-2 px-1">
                                      <label class="col-md-12 label_header" for="nro_factura"># {{trans('contableM.factura')}} </label>
                                      <input class="form-control " type="text" name="nro_factura" id="nro_factura" value="@if(!is_null($comprobante)) {{$comprobante->nro_comprobante}} @endif">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">{{trans('contableM.tipocomprobante')}}</label>
                                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas " style="width: 100%; heigth: 22px">
                                            <option value="">{{trans('proforma.seleccion')}}...</option>
                                            @foreach($t_comprobante as $value)
                                                <option {{$value->codigo == $comprobante->id_tipo_comprobante ? 'selected' : ''}} value="{{$value->codigo}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>                                    
                                
                            </div>
                            <div class="col-md-12">
                            <label class="label_header" for="detalle_deuda">DETALLE DE RUBROS</label>
                            </div>
                            <div class="col-md-12 ">
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 px-1">          
                                    <table id="example3" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="width: 8%; text-align: center;">Codigo</th>
                                            <th style="width: 8%; text-align: center;">Rubro</th>
                                            <th style="width: 20%; text-align: center;">Detalle</th>
                                            <th style="width: 10%; text-align: center;">Divisa</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.valor')}}</th>
                                            <th style="width: 6%; text-align: center;"{{trans('contableM.total')}}Base</th>
                                            
                                            
                                        </tr>
                                        </thead>
                                        <tbody id="det_recibido">
                                            @foreach($detalles as $value)
                                                <tr>
                                                    <td>{{$value->codigo}}</td>
                                                    <td>{{$value->nombre}}</td>
                                                    <td>{{$value->concepto}}</td>
                                                    <td>Dolares</td>
                                                    <td>{{$value->valor}}</td>
                                                    <td>{{$value->valor}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">{{trans('contableM.subtotal0')}}</label>
                                            <input class="form-control  col-md-12" type="text" name="subtotal0" id="subtotal0" readonly value="@if(!is_null($comprobante)) {{number_format($comprobante->subtotal_0,2)}} @endif" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">{{trans('contableM.subtotal12')}}</label>
                                            <input class="form-control col-md-12" type="text" name="subtotal12" id="subtotal12" readonly value="@if(!is_null($comprobante)) {{number_format($comprobante->subtotal_12,2)}} @endif" >
                                        </div>
                                       
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">{{trans('contableM.subtotal')}}</label>
                                            <input class="form-control  col-md-12" type="text" readonly value="@if(!is_null($comprobante)) {{number_format($comprobante->subtotal,2)}} @endif" name="subtotal" id="subtotal" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="impuesto">{{trans('contableM.impuesto')}}</label>
                                            <input class="form-control col-md-12" type="text" readonly value="@if(!is_null($comprobante)) {{number_format($comprobante->impuesto,2)}} @endif" name="impuesto" id="impuesto" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">{{trans('contableM.total')}}</label>
                                            <input class="form-control col-md-12" value="@if(!is_null($comprobante)) {{$comprobante->valor_contable}} @endif" readonly type="text" name="total" id="total" >
                                        </div>
                                    </div>
                                </div>


                        </div>                     
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="input-group">
                                <label class="col-md-12 cabecera" style="color: black;" for="nota">{{trans('contableM.nota')}}: </label>
                                <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                                <input type="hidden" name="saldo_final" id="saldo_final">
                            </div>
                        </div>  

                        </div>

            </div>
    </form>
    <div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>



@endsection