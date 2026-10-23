@extends('contable.nota_inventario.base')
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
        location.href="{{ route('notainventario.index') }}";
        
    }
    
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('notainventario.index')}}">Nota de Ingreso Inventario</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nueva nota de Ingreso Inventario</li>
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
                                <div class="col-md-6 col-sm-9 col-6">
                                    <div class="box-title"><b> Visualizador Nota de Ingreso Inventario</b></div>
                                </div>
                                <div class="col-md-6">
                                        <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$inventario->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                         <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                        </a>
                                        <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$inventario->id_asiento_cabecera])}}" target="_blank">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                        </a>

                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                        </button>
                                
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
                                    <div class=" col-md-2 px-1" >
                                        <label class="label_header">Estado</label>
                                        <input @if(!is_null($inventario)) @if(($inventario->estado)==1) style="background-color: green;" @else style="background-color: red;" @endif @endif  readonly class="form-control col-md-1">           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                            <input class="form-control" type="text" name="id_factura" value="@if(!is_null($inventario)) {{$inventario->id}} @endif" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">Número</label>
                                            <input class="form-control" type="text" id="numero_factura" value="@if(!is_null($inventario)) {{$inventario->secuencia}} @endif" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                        <input class="form-control" type="text" name="tipo" id="tipo" value="INV-IN" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">Asiento</label>
                                            <input class="form-control" type="text" id="asiento" value="@if(!is_null($inventario)) {{$inventario->id_asiento_cabecera}} @endif" name="asiento" readonly>
                                           
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                            <input class="form-control" type="text" name="fecha_hoy" id="fecha_hoy" value="@if(!is_null($inventario)) {{$inventario->fecha}} @endif">
                                        
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class="col-md-8 px-1">
                                            <input type="hidden" name="total_suma" id="total_suma">
                                            <label class="label_header" for="concepto">Concepto:</label>
                                            <input class="form-control col-md-12" autocomplete="off" value="@if(!is_null($inventario)) {{$inventario->concepto}} @endif" type="text" name="concepto" id="concepto" readonly>
                                    </div>
                                    <div class="col-md-4 px-1">
                                        <label class="label-control label_header" for="">Bodega</label>
                                        <select name="id_bodega" id="id_bodega" class="form-control select2" style="width: 100%;"  readonly>
                                            <option value="">Seleccione...</option>
                                            @foreach($bodega as $value)
                                             <option @if(!is_null($inventario)) {{ $value->id == $inventario->id_bodega ? 'selected' : ''}} @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>   
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 px-1">
                                <label class="label_header" for="detalle_deuda">DETALLE DE LOS PRODUCTOS QUE INGRESAN</label>
                            </div>
                            <div class="col-md-12 px-1">
                                <input type="hidden" name="contadora" id="contadora" value="0">
                                <div class="table-responsive col-md-12 px-1" style="width: 100%;" >               
                                    <table id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr>
                                            <th style="width: 20%; text-align: center;">Código</th>
                                            <th style="width: 20%; text-align: center;">Nombre</th>
                                            <th style="width: 15%; text-align: center;">Bodega</th>
                                            <th style="width: 15%; text-align: center;">Cantidad</th>
                                            <th style="width: 10%; text-align: center;">Divisa</th>
                                            <th style="width: 10%; text-align: center;">Costo</th>
                                            <th style="width: 10%; text-align: center;">Costo Base</th>
                                           
                                        </tr>
                                        </thead>
                                            <tbody id="det_x">
                                                @if(!is_null($inventario))
                                                    @if(isset($inventario->detalles))
                                                        @foreach($inventario->detalles as $value)
                                                        <tr>
                                                            <td>{{$value->codigo}}</td>
                                                            <td style="text-align: center;">{{$value->nombre}}</td>
                                                            <td style="text-align: center;">{{$value->bodega}}</td>
                                                            <td style="text-align: center;">{{$value->cantidad}}</td>
                                                            <td style="text-align: center;">USD</td>
                                                            <td style="text-align: center;">{{$value->total}}</td>
                                                            <td style="text-align: center;">{{$value->costo}}</td>
                                                        </tr>
                                                        @endforeach
                                                    @endif
                                                @endif
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
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">Total</label>
                                            <input class="form-control  col-md-12" @if(!is_null($inventario)) value="{{$inventario->valor_contable}}" @endif type="text" name="total" id="total" readonly >
                                        </div>
                                    </div>
                                </div>

                            </div> 
                            <div class="col-md-12 px-1">
                                <label class="label_header" for="detalle_deuda">JUSTIFICACION CONTABLE</label>
                            </div>
                            <div class="col-md-12 px-1">
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 px-1" style="width: 100%;" >               
                                    <table id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr>
                                            <th style="width: 18%; text-align: center;">Código</th>
                                            <th style="width: 20%; text-align: center;">Nombre</th>
                                            <th style="width: 20%; text-align: center;">Detalle</th>
                                            <th style="width: 15%; text-align: center;">Divisa</th>
                                            <th style="width: 15%; text-align: center;">Valor</th>
                                            <th style="width: 10%; text-align: center;">Total Base</th>
                                           
                                        </tr>
                                        </thead>
                                            <tbody id="det_recibido">
                                               @if(!is_null($inventario))
                                                    @if(isset($inventario->rubros))
                                                        @foreach($inventario->rubros as $value)
                                                        <tr>
                                                            <td style="text-align: left;">{{$value->codigo}}</td>
                                                            <td style="text-align: left;">{{$value->nombre}}</td>
                                                            <td style="text-align: center;">{{$value->nombre}}</td>
                                                            <td>USD</td>
                                                            <td style="text-align: center;">{{$value->valor}}</td>
                                                            <td style="text-align: center;">{{$value->valor}}</td>
                                                        </tr>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-8">
                                            &nbsp;
                                        </div>
                                       
                                        <!--
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="total">Total</label>
                                            <input class="form-control  col-md-12" value="0.00" type="text" name="total" id="total" readonly >
                                        </div>-->
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="subtotal">Total Debe</label>
                                            <input class="form-control  col-md-12" @if(!is_null($inventario)) value="{{$inventario->valor_contable}}" @endif value="0.00" type="text" name="total_debe" readonly id="total_debe" readonly>
                                        </div>
                                        <div class="col-md-2 px-1">
                                            <label class="label_header" for="impuesto">Total Haber</label>
                                            <input class="form-control  col-md-12"  type="text" autocomplete="off" onchange="sumar_impuesto()" @if(!is_null($inventario)) value="{{$inventario->valor_contable}}" @endif  name="total_haber" id="total_haber" readonly>
                                        </div>
                                    </div>
                                </div>

                            </div> 

                        </div>
 
                    </div>                   
            </div>
    </form>

</section>


@endsection
