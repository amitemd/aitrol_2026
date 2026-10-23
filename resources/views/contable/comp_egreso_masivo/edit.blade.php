@extends('contable.comp_egreso_masivo.base')
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

</style>
<script type="text/javascript">
    function goBack() {
        location.href="{{ route('comp_egreso_masivo.index')}}";
    }
    
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('comp_egreso_masivo.index')}}">Comprobante de Egreso Masivo</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevo Comprobante de Egreso Masivo</li>
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
                                <div class="col-md-7 col-sm-7 col-3">
                                    <div class="box-title " ><b>VISUALIZADOR COMP. DE EGRESOS ACREEDORES MASIVOS</b></div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante_egreso_m->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                                <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                        </a>
                                        <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante_egreso_m->id_asiento_cabecera])}}" target="_blank">
                                              <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diaro
                                        </a>
                                       
                                        <button type="button" class="btn btn-success  btn-gray" onclick="goBack()" style="margin-left: 10px;">
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
                                    <div class=" col-md-2 px-1" >
                                        <label class="label_header">{{trans('contableM.estado')}}</label>
                                        <div style=" @if(($comprobante_egreso_m->estado)==1) background-color: green; @else background-color: red;  @endif " class="form-control col-md-1"></div>           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}:</label>
                                            <input class="form-control " type="text" name="id_factura"  value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->id}} @endif" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                            <input class="form-control " type="text" id="numero_factura" value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->secuencia}} @endif" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" value="ACR-EG-MA" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                            <input class="form-control " type="text" id="asiento"  value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->id_asiento_cabecera}} @endif" name="asiento" readonly>
                                        
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                            <input class="form-control " type="text" name="fecha_hoy" id="fecha_hoy"  value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->fecha_comprobante}} @endif" readonly>
                                            
                                        
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class=" col-md-10 px-0">
                                            <label class="label_header" for="acreedor">{{trans('contableM.concepto')}}:</label>
                                            <input class="form-control  col-md-12" type="text" name="aaa"  value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->descripcion}} @endif" readonly autocomplete="off" id="aaa" >
                                    </div>
                                    <div class=" col-md-2 px-0">
                                        <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                                        <label class="container col-md-12">Cheque entregado
                                            <input type="checkbox" id="cheque_entregado" name="cheque_entregado" readonly @if(!is_null($comprobante_egreso_m)) @if($comprobante_egreso_m->check==1) checked @endif @endif>
                                            <span class="checkmark"></span>

                                        </label>
                                    </div>
                                    <div class=" col-md-4  px-0">
                                            <label class="col-md-12 label_header" for="ruc">Banco:</label>
                                            <select class="form-control " name="banco" id="banco">
                                            <option value="0">Seleccione..</option>
                                            @foreach($banco as $value)
                                                <option {{$value->id == $comprobante_egreso_m->id_caja_banco ? 'selected' : ''}} value="0">{{$value->nombre}}</option>
                                            @endforeach
                                            </select>
                                    </div>
                                    <div class=" col-md-2  px-0">
                                            <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.divisass')}}:</label>
                                            <input type="text" class="form-control" value="DOLARES" readonly>
                                    </div>
                                    <div class=" col-md-2  px-1">
                                            <label class="col-md-12 label_header" for="secuencia">Cambio:</label>
                                            <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                                    </div>
                                    <div class=" col-md-2  px-1">
                                            <label  class="col-md-12 label_header control-label" for="numero_cheque">Cheque No:</label>
                                            <input class="form-control " type="text" name="numero_cheque" value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->no_cheque}} @endif"  id="numero_cheque" readonly> 
                                    </div>
                                    <div class=" col-md-2  px-1">
                                            <label class="col-md-12 label_header" for="fecha_cheque">Fecha Cheque: </label>
                                            <input class="form-control " type="text" name="fecha_cheque" id="fecha_cheque" value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->fecha_cheque}} @endif">
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        <label for="valor" class="label_header" >Valor</label>
                                        <input class="form-control " type="text" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->valor_pago}} @endif" readonly>
                                    
                                    </div>                            
                                    <div class=" col-md-4  px-1">
                                            <label class="col-md-12 label_header" for="girado"> Girado a:</label>
                                            <input class="form-control " type="text" name="girado_a" id="girado_a" value="@if(!is_null($comprobante_egreso_m)) {{$comprobante_egreso_m->girado_a}} @endif" readonly>
                                    </div>
                                    <input type="hidden" name="total_suma" id="total_suma">
                                </div>
                            </div>
                            <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL PROVEEDOR</label>
                           
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                <div class="table-responsive col-md-12 px-1 " style="min-height: 250px; max-height: 250px; width: 100%;">               
                                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" style="width: 100%;" aria-describedby="example2_info">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="width: 10%; text-align: center;">Compra</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.acreedor')}}</th>
                                            <th style="width: 10%; text-align: center;">Deuda</th>
                                            <th style="width: 10%; text-align: center;">Vence</th>
                                            <th style="width: 10%; text-align: center;">Tipo</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.concepto')}}</th>
                                            <th style="width: 10%; text-align: center;">Div</th>
                                            <th style="width: 10%; text-align: center;">Saldo</th>
                                            <th style="width: 10%; text-align: center;">Abono</th>
                                    
                                            
                                        </tr>
                                        </thead>
                                        
                                        <tbody id="crear">
                                        @php $cont=0; @endphp
                                        @foreach($detalle_egreso_m as $value)
                                            <tr>
                                                <td>{{$value->id_compra}}</td>
                                                <td> @if(isset($value->proveedor)) {{$value->proveedor->nombrecomercial}} @endif </td>
                                                <td> @if(isset($value->compras)) {{$value->compras->secuencia_f}} @endif </td>
                                                <td> @if(!is_null($comprobante_egreso_m->fecha_comprobante)) {{$comprobante_egreso_m->fecha_comprobante}} @endif </td>
                                                <td> COM-FA </td>
                                                <td> {{$value->observacion}}  </td>
                                                <td > $ </td>
                                                <td style="text-align: center;"> {{$value->saldo_base}} </td>
                                                <td style="text-align: center;"> {{$value->abono}} </td>
                                        
                                             
                                                
                                            </tr>
                                            @php $cont = $cont +1; @endphp
                                        @endforeach
                                            
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12" style="margin-top: 30px;">
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                           
                                            <input type="hidden" name="saldo_hidden0" id="saldo_hidden0">
                                            <input type="hidden" name="total_egreso" id="total_egreso" value="0">
                                        </div>
                                       
                                        <div class="form-group col-md-3" style="text-align: right;">
                                           
                                        </div>
                                        <div class="form-group col-md-2">
                                            <div class="input-group">
                                              
                                                <input type="hidden" name="proveedor" id="proveedor"> 
                                                <input type="hidden" name="sobrante" id="sobrante">
                                                <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                        </div>
                        



                        
            </div>
    </form>

</section>

<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
 $(document).ready(function(){
        $('#cheque_entregado').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });

</script>

@endsection
