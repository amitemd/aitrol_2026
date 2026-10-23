@extends('contable.debito_acreedores.base')
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
        location.href="{{route('debitoacreedores.index')}}";
    }
    
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
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
        <li class="breadcrumb-item"><a href="{{route('debitoacreedores.index')}}">Nota de Débito Acreedores</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nueva nota de Debito</li>
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
                                <div class="col-md-6 col-sm-6 col-5">
                                    <div class="box-title " ><b>Visualizador- Nota de Débito Acreedores</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                            <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                        </a>
                                        <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante->id_asiento_cabecera])}}" target="_blank">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                        </a>
                                                                            
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 3px; padding: 7px 23px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body dobra">
                        <div class="row header">
                            <div class="col-md-12 px-1">
                                <div class="form-row ">
                                   
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                    <div class=" col-md-1 px-1" >
                                        <label class="label_header">Estado</label>
                                        <div style="background-color: green;" class="form-control col-md-1"></div>           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                            <input class="form-control" type="text" name="id_factura" value="@if(($comprobante)) {{$comprobante->id}} @endif" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">Número</label>
                                            <input class="form-control" type="text" id="numero_factura" value="@if(($comprobante)) {{$comprobante->secuencia}} @endif" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-1 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                        <input class="form-control" type="text" name="tipo" id="tipo" value="ACR-DB" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">Asiento</label>
                                            <input class="form-control" type="text" id="asiento" name="asiento" value="@if(($comprobante)) {{$comprobante->id_asiento_cabecera}} @endif" readonly>
                                            @if(!is_null($iva_param))

                                                <input type="text" name="iva_par" id="iva_par" class="hidden" value="{{$iva_param->iva}}">
                                            @endif
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                            <input class="form-control" type="text" name="fecha_hoy" value="@if(($comprobante)) {{$comprobante->fecha_factura}} @endif" id="fecha_hoy" readonly >
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="fecha_caducidad">Fecha Caducidad: </label>
                                        <input class="form-control" type="text" value="@if(($comprobante)) {{$comprobante->f_autorizacion}} @endif"  name="fecha_caducidad" id="fecha_caducidad" readonly>
                                    
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class=" col-md-2 px-1">
                                            {{ csrf_field() }} 
                                            <input type="hidden" name="superavit" id="superavit" value="0">
                                            <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                                            <input type="hidden" name="id_proveedor" id="id_proveedor">
                                            <input type="text" id = "nombre_proveedor" name="nombre_proveedor" class= "form-control form-control-sm nombre_proveedor" value="@if(($comprobante->proveedor)) {{$comprobante->proveedor->nombrecomercial}} @endif" >
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label class="control-label label_header" for="autorizacion">Autorización</label>
                                        <input type="text" name="autorizacion" id="autorizacion" value="@if(($comprobante)) {{$comprobante->autorizacion}} @endif" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label class="col-md-12 label_header control-label" for="serie">Serie</label>
                                        <input type="text" class="form-control" id="serie" name="serie" value="@if(($comprobante)) {{$comprobante->secuencia}} @endif"  readonly>
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <label for="secuencia" class="label_header col-md-12 control-label">Secuencia</label>
                                        <input type="text" class="form-control" id="secuencia" name="secuencia" value="@if(($comprobante)) {{$comprobante->secuencia}} @endif" readonly>
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="fechand">Fecha N/D: </label>
                                        <input class="form-control" type="text" name="fechand" id="fechand" value="@if(($comprobante)) {{$comprobante->fecha_factura}} @endif" readonly>
                                    
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">Credito Tributario</label>
                                        <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas" style="width: 100%; heigth: 22px" readonly>
                                            <option value="">Seleccione...</option>
                                            @foreach($c_tributario as $value)
                                                <option {{$value->id == $comprobante->tipo_comprobante ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-10 px-1">
                                            <input type="hidden" name="total_suma" id="total_suma">
                                            <label class="label_header" for="concepto">Concepto:</label>
                                            <input class="form-control col-md-12" type="text" name="concepto" id="concepto"  value="@if(($comprobante)) {{$comprobante->concepto}} @endif" readonly>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1" >
                                        <label class="control-label label_header">Tipo de Comprobante</label>
                                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas" style="width: 100%;heigth: 22px">
                                            <option value="">Seleccione...</option>
                                            @foreach($t_comprobante as $value)
                                                <option {{$value->id == $comprobante->credito_tributario ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        <label class="col-md-12 label_header" for="fecha_factura">Fecha factura: </label>
                                        <input class="form-control" type="text" name="fecha_factura" id="fecha_factura" value="@if(($comprobante)) {{$comprobante->fecha_factura}} @endif">
                                    </div>
                                    <div class="col-md-2 px-1">
                                            <label class="label_header" for="serie_factura">Serie Fact:</label>
                                            <input class="form-control col-md-12" type="text" name="serie_factura" id="serie_factura" value="@if(($comprobante)) {{$comprobante->serie_factura}} @endif"  >
                                    </div>
                                    <div class="col-md-2 px-1">
                                            <label class="label_header" for="secuencia_fact">Secuncia . Fact:</label>
                                            <input class="form-control col-md-12" type="text" name="secuencia_fact" id="secuencia_fact" value="@if(($comprobante)) {{$comprobante->secuencia}} @endif" >
                                    </div>
                                    <div class="col-md-2 px-1">
                                            <label class="label_header" for="autorizacion_factura">Autori. Fact:</label>
                                            <input class="form-control col-md-12" type="text" name="autorizacion_factura" id="autorizacion_factura" value="@if(($comprobante)) {{$comprobante->autorizacion}} @endif" >
                                    </div>
                    
                                    <div class="col-md-4 col-xs-2 px-1" >
                                        <label class="control-label label_header">Tipo de Comprobante</label>
                                        <select name="tipo_comprobante2" id="tipo_comprobante2" class="form-control  select2_cuentas" style="width: 100%; heigth: 22px">
                                            <option value="">Seleccione...</option>
                                            @foreach($t_comprobante as $value)
                                                <option {{$value->id == $comprobante->tipo_comprobante ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-12 px-1">
                                <label class="label_header" for="detalle_deuda">DETALLE DE RUBROS</label>
                            </div>
                          
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 px-1" style="width: 100%;"   >               
                                    <table id="example3" style="width: 100%;" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="width: 8%;  text-align: center;">Rubro</th>
                                            <th style="width: 20%; text-align: center;">Detalle</th>
                                            <th style="width: 10%; text-align: center;">Divisa</th>
                                            <th style="width: 10%; text-align: center;">Valor</th>
                                            <th style="width: 6%;  text-align: center;">Total Base</th>
                                          
                                        </tr>
                                        </thead>
                                        <tbody id="det_recibido" style="background-color: white;">
                                            @foreach($detalles2 as $value)
                                                <tr>
                                                    <td>{{$value->codigo}}</td>
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
                                
                                  
                                        <div class="col-md-6">
                                            &nbsp;
                                            @php
                                                $siniva= $comprobante->valor_contable*0.12;
                                                $sinivat= $comprobante->valor_contable-$siniva;
                                         @endphp
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">Subtotal</label>
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
                                   
                                

                                         
                        
                            <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS CON EL PROVEEDOR</label>   
                            <input type="hidden" name="contadore" id="contadore" value="0">
                            <div class="table-responsive col-md-12 px-1" style="width: 100%;">
                                            
                                <table id="example2" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                    <thead style="background-color: #9E9E9E; color: white;" >
                                    <tr style="position: relative;">
                                        
                                        <th style="width: 14.28%; text-align: center;">Vence</th>
                                        <th style="width: 14.28%; text-align: center;">Tipo</th>
                                        <th style="width: 14.28%; text-align: center;">Número</th>
                                        <th style="width: 14.28%; text-align: center;">Concepto</th>
                                        <th style="width: 14.28%; text-align: center;">Div</th>
                                        <th style="width: 14.28%; text-align: center;">Saldo</th>
                                        <th style="width: 14.28%; text-align: center;">Abono</th>
                                        
                                    </tr>   
                                    </thead>
                                    <tbody id="det_recibido" style="background-color: #fff;">
                                   
                                    @foreach ($detalle as $value)
                                        <tr>
                                            <td> {{$value->vence}} </td>
                                            <td> COM-FA </td>
                                            <td> {{$value->secuencia}} </td>
                                            <td> {{$value->concepto}} </td>
                                            <td>$</td>
                                            <td style="text-align: center;" > {{$value->total_factura}} </td>
                                            <td style="text-align: center;"> {{$value->total}} </td>

                                        </tr>
                                       
                                    @endforeach
                                        
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                       
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="input-group">
                                <label class="col-md-12 cabecera" style="color: black;" for="nota">Nota: </label>
                                <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                                <input type="hidden" name="saldo_final" id="saldo_final">
                            </div>
                        </div>   

                        </div>

            </div>
    </form>

</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">



</script>

@endsection
