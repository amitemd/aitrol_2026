@extends('contable.nota_debito_cliente.base')
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
        .alerta_correcto{
            position: absolute;
            z-index: 9999;
            top: 100px;
            right: 10px;
	    }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 18px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

/* Hide the browser's default checkbox */
        .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

/* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }
       /* .form-control{
            background-color: #f5f5f5;
        }*/

/* On mouse-over, add a grey background color */
        .container:hover input ~ .checkmark {
        background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .container input:checked ~ .checkmark {
        background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        }

        /* Show the checkmark when checked */
        .container input:checked ~ .checkmark:after {
        display: block;
        }

        /* Style the checkmark/indicator */
        .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        }
</style>
<script type="text/javascript">
    function goBack() {
        location.href="{{ route('nota_cliente_debito.index') }}";
    }
    
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('nota_cliente_debito.index')}}">Nota de Débito Cliente</a></li>
        <li class="breadcrumb-item active" aria-current="page">Visualizador Nota de Débito</li>
      </ol>
    </nav>
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente 
</div>   
    <form class="form-vertical " method="post" id="form_guardado">
            {{ csrf_field() }}
            <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-3">
                                    <div class="box-title " ><b>Visualizador- Nota de Débito Cliente</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                    <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                </a>
                                    <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante->id_asiento_cabecera])}}" target="_blank">
                                     <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>

                                        <button type="button" class="btn btn-success btn-gray" onclick="goBack()" style="margin-left: 3px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true" style="padding: 3px 5px"></i>&nbsp;&nbsp;Regresar
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
                                  
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                    <div class=" col-md-2 px-1" >
                                        <label class="label_header">Estado</label>
                                        <input style="@if(($comprobante->estado)==1) background-color: green; @else background-color: red; @endif" readonly class="form-control col-md-1">           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                            <input class="form-control" type="text" name="id_factura" value="@if(!is_null($comprobante)) {{$comprobante->id}} @endif" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">Número</label>
                                            <input class="form-control" type="text" id="numero_factura"  name="numero_factura" value="@if(!is_null($comprobante)) {{$comprobante->secuencia}} @endif" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                        <input class="form-control" type="text" name="tipo" id="tipo" value="CLI-DB" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">Asiento</label>
                                            <input class="form-control" type="text" id="asiento" readonly name="asiento" value="@if(!is_null($comprobante)) {{$comprobante->id_asiento_cabecera}} @endif" readonly>
                                           
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                            <input class="form-control" type="text" name="fecha_hoy" readonly id="fecha_hoy" value="@if(!is_null($comprobante)) {{$comprobante->fecha}} @endif">
                                        
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class="col-md-10 px-1">
                                            <input type="hidden" name="total_suma" id="total_suma">
                                            <label class="label_header" for="concepto">Concepto:</label>
                                            <input class="form-control col-md-12" readonly type="text" value="@if(!is_null($comprobante)) {{$comprobante->concepto}} @endif" name="concepto" id="concepto" >
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">Factura ID # </label>
                                        <input class="form-control" name="facturan" value="@if(!is_null($comprobante)) {{$comprobante->id}} @endif" readonly id="facturan">
                                    </div>
                                    <div class=" col-md-5 px-0">
                                        <label class="col-md-12 label_header" for="nombre_cliente">Cliente : </label>
                                        <input class="form-control" type="text" name="id_cliente" value="@if(!is_null($comprobante)) {{$comprobante->id_cliente}} @endif" readonly id="id_cliente">
                                    </div>
                                    <div class="col-md-5 px-0">
                                            <label class="label_header" for="serie_factura">&nbsp;</label>
                                            <input class="form-control col-md-12" type="text" name="nombre_cliente" value="@if(!is_null($comprobante->cliente)) {{$comprobante->cliente->nombre}} @endif" readonly id="nombre_cliente" >
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">Factura # </label>
                                        <input class="form-control" name="facturano" id="facturano" readonly value="@if(!is_null($comprobante)) {{$comprobante->numero}} @endif" >
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-12">
                            <label class="label_header" for="detalle_deuda">DETALLE DE RUBROS DE DÉBITO</label>
                            </div>
                            <div class="col-md-12 px-1">
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 ">               
                                    <table id="example3"role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="width: 10%; text-align: center;">Código</th>
                                            <th style="width: 12%; text-align: center;">Rubro</th>
                                            <th style="width: 8%; text-align: center;">Fecha</th>
                                            <th style="width: 8%; text-align: center;">Vence</th>
                                            <th style="width: 20%; text-align: center;">Detalle</th>
                                            <th style="width: 8%; text-align: center;">Divisa</th>
                                            <th style="width: 8%; text-align: center;">Valor</th>
                                            <th style="width: 8%; text-align: center;">Total Base</th>
                                            
                                        </tr>
                                        </thead>
                                        <tbody id="det_recibido">   
                                            @php $acumulador=0; @endphp
                                            @foreach($detalles as $value)
                                                @php $acumulador+=$value->valor; @endphp
                                                <tr>
                                                    <td>{{$value->codigo}}</td>
                                                    <td>{{$value->nombre}}</td>
                                                    <td>{{$value->fecha}}</td>
                                                    <td>{{$value->vencimiento}}</td>
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
                                        <div class="col-md-6">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1"> 
                                            <!-- arreglar esto no salen bien los valores 15 de julio del 2020  17:45 -->
                                            <label class="label_header" for="subtotal">Subtotal</label>
                                            @php
                                                $siniva=0;
                                                $sinivat=$acumulador;
                                                //dd($comprobante->iva);
                                                if($comprobante->iva!=0){
                                                     $siniva= $acumulador*$iva_param->iva;
                                                     $sinivat= $comprobante->valor_contable-$siniva;
                                                }
                                               
                                            @endphp
                                            <input class="form-control  col-md-12" type="text" readonly value="@if(!is_null($comprobante)) {{number_format($sinivat,2)}} @endif" name="subtotal" id="subtotal" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="impuesto">Impuesto</label>
                                            <input class="form-control col-md-12" type="text" readonly value="@if(!is_null($comprobante)) {{number_format($siniva,2)}} @endif" name="impuesto" id="impuesto" >
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">Total</label>
                                            <input class="form-control col-md-12" value="@if(!is_null($comprobante)) {{$comprobante->valor_contable}} @endif" readonly type="text" name="total" id="total" >
                                        </div>
                                    </div>
                                </div>


                        </div>    

                        </div>
                 
            </div>
    </form>

</section>


@endsection
