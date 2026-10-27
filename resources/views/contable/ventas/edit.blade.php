@extends('contable.ventas.base')
@section('action-content')
<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('/css/aitrol-invoice-detail.css') }}?v={{ filemtime(public_path('css/aitrol-invoice-detail.css')) }}">

<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
        window.history.back();
    }
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="modalpdf" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<section class="content aitrol-invoice-detail">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Ventas</a></li>
            <li class="breadcrumb-item"><a href="../ventas">Registro de Factura de Ventas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>
    <div class="box aitrol-invoice-detail__toolbar-card">
        <div class="box-header aitrol-invoice-detail__toolbar">
            <div class="aitrol-invoice-detail__heading">
                <span class="aitrol-invoice-detail__heading-icon" aria-hidden="true">
                    <i class="fa fa-file-text-o"></i>
                </span>
                <div>
                    <span class="aitrol-invoice-detail__eyebrow">Comprobante de venta</span>
                    <h1 class="box-title">Factura {{ $ventas->sucursal }}-{{ $ventas->punto_emision }}-{{ $ventas->numero }}</h1>
                    <p>Revise la información general, los productos, valores y formas de pago.</p>
                </div>
            </div>
            <div class="aitrol-invoice-detail__actions">
                <button type="button" onclick="goBack()" class="btn aitrol-invoice-action aitrol-invoice-action--subtle">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i><span>Regresar</span>
                </button>
                <a class="btn aitrol-invoice-action" data-remote="{{ route('modalsubir_ventas',['id'=> $ventas->id])}}" data-toggle="modal" data-target="#modalpdf">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i><span>Subir PDF</span>
                </a>
                <a class="btn aitrol-invoice-action" data-remote="{{ route('compras.modal_estado',[$ventas->id_asiento])}}" data-toggle="modal" data-target="#visualizar_estado">
                    <i class="fa fa-eye" aria-hidden="true"></i><span>Ver asiento</span>
                </a>
                <a class="btn aitrol-invoice-action aitrol-invoice-action--primary" href="{{route('librodiario.edit',['id'=>$ventas->id_asiento])}}" target="_blank" rel="noopener">
                    <i class="fa fa-pencil" aria-hidden="true"></i><span>Editar asiento</span>
                </a>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{$ventas->id}}" id="idventa">
    <div class="box-body dobra aitrol-invoice-detail__body">
        <div class="header row aitrol-invoice-detail__fields">
            <form action="{{route('ventas_update',['id'=>$ventas->id])}}" method="post" id="forms" class="aitrol-invoice-detail__form">
                {{ csrf_field() }}
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="sucursal" class="label_header">Sucursal (Empresa)</label>
                    </div>
                    <input type="text" class="form-control col-md-12" value="{{$ventas->sucursal}}" disabled>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="punto_emision" class="label_header">P. Emision (Empresa)</label>
                    </div>
                    <input type="text" class="form-control col-md-12" disabled value="{{$ventas->punto_emision}}">
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id" class=" label_header">Id</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="id" id="id" value="@if(!is_null($ventas)){{$ventas->id}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero" class=" label_header">N&uacute;mero</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="numero" id="numero" value="@if(!is_null($ventas)){{$ventas->numero}}@endif" onchange="ingresar_cero()">
                    </div>
                </div>

                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo" class="label_header">Tipo</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="tipo" id="tipo" value="@if(!is_null($ventas)){{$ventas->tipo}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="asiento" class="label_header">Asiento</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="asiento" id="asiento" value="@if(!is_null($ventas)){{$ventas->id_asiento}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_asiento" class="label_header">Fecha</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input readonly id="fechas" type="date" class="form-control" name="fecha_asiento" value="{{$ventas->fecha}}">
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="orden_venta" class="label_header">Orden de Venta</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="orden_venta" type="text" class="form-control" name="orden_venta" value="@if(!is_null($ventas)){{$ventas->orden_venta}}@endif" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="empresa" class="label_header">Empresa</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="empresa" type="text" class="form-control" name="empresa" value="@if(!is_null($ventas)){{$ventas->id_empresa}}@endif" readonly>
                    </div>
                </div>

                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="divisas" class="label_header">Divisas</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="divisas" name="divisas" class="form-control" disabled>
                            @foreach($divisas as $value)
                            <option value="{{$value->id}}">{{$value->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="recaudador" class="label_header">Recaudador</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="recaudador" name="recaudador" class="form-control" disabled>
                            <option value="">Seleccione...</option>
                            @foreach($user_recaudador as $value)
                            <option value="{{$value->id}}" @if($ventas->id_recaudador == $value->id) selected="selected" @endif>{{$value->nombre1}} {{$value->apellido1}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" class="form-control input-sm" name="cedula_recaudador" id="cedula_recaudador" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="vendedor" class="label_header">Vendedor</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="vendedor" name="vendedor" class="form-control" required>
                            <option value="">Seleccione...</option>
                            @foreach($user_vendedor as $value)
                            <option value="{{$value->nombre1}} {{$value->apellido1}}" data-id="{{$value->id}}" data-name="{{$value->nombre1}} {{$value->apellido1}}" @if($ventas->ci_vendedor == $value->id) selected="selected" @endif>{{$value->nombre1}} {{$value->apellido1}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" class="form-control input-sm" name="cedula_vendedor" id="cedula_vendedor" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-12  px-1">
                    <div class="col-md-12 px-0">
                        <label for="cliente" class="label_header text-left">Cliente</label>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="identificacion_cliente" type="text" class="form-control" name="identificacion_cliente" value="@if(!is_null($ventas)){{$ventas->id_cliente}}@endif" onchange="reloadCliente()">
                    </div>
                    @php
                    $cliente= DB::table('ct_clientes')->where('identificacion',$ventas->id_cliente)->first();
                    @endphp
                    <div class="col-md-2 px-0">
                        <input id="nombre_cliente" type="text" class="form-control" name="nombre_cliente" value="{{$cliente->nombre}}">
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="direccion_cliente" type="text" class="form-control" name="direccion_cliente" value="@if(!is_null($ventas)){{$ventas->direccion_cliente}}@endif">

                    </div>
                    <div class="col-md-2 px-0">
                        <input id="ciudad_cliente" type="text" class="form-control" name="ciudad_cliente" value="@if(!is_null($ventas)){{$ventas->direccion_cliente}}@endif">

                    </div>
                    <div class="col-md-2 px-0">
                        <input id="mail_cliente" type="text" class="form-control" name="mail_cliente" value="@if(!is_null($ventas)){{$ventas->email_cliente}}@endif">
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="telefono_cliente" type="text" class="form-control" name="telefono_cliente" value="@if(!is_null($ventas)){{$ventas->telefono_cliente}}@endif">
                    </div>
                </div>
                <div class="form-group col-xs-12  px-1">
                    <div class="col-md-12 px-0">
                        <label for="paciente" class="label_header text-left">Paciente</label>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="identificacion_paciente" type="text" class="form-control" name="identificacion_paciente" value="@if(!is_null($ventas)){{$ventas->id_paciente}}@endif">
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="nombre_paciente" type="text" class="form-control" name="nombre_paciente" value="@if(!is_null($ventas)){{$ventas->nombres_paciente}}@endif">

                    </div>
                    <div class="col-md-2 px-0">
                        <select class="form-control" name="id_seguro" id="id_seguro">
                            <option value="">Seguro ...</option>
                            @foreach($seguros as $seguro)
                            <option value="{{$seguro->id}}" @if($ventas->seguro_paciente == $seguro->id) selected="selected" @endif>{{$seguro->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="procedimiento" type="text" class="form-control" name="procedimiento" value="@if(!is_null($ventas)){{$ventas->procedimientos}}@endif">
                    </div>
                    <div class="col-md-2 px-0">
                        <input readonly type="date" class="form-control" name="fecha_procedimiento" value="@if(!is_null($ventas)){{date('Y-m-d',strtotime($ventas->fecha_procedimiento))}}@endif">
                    </div>
                    <div class="col-md-2 px-0">
                        <select class="form-control" name="tipo_consulta" id="tipo_consulta" readonly>
                            <option value=""> --- Tipo --- </option>
                            <option value="1" {{ $ventas->tipo_consulta == "1" ? 'selected' : ''}}>Consulta</option>
                            <option value="2" {{ $ventas->tipo_consulta == "2" ? 'selected' : ''}}>Procedimiento</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-10  px-1">
                    <label for="concepto" class="label_header">Concepto</label>
                    <input type="text" class="form-control" name="concepto" id="concepto" value="@if(!is_null($ventas->concepto)){{$ventas->concepto}} @endif">
                </div>
                <div class="form-group col-xs-2 px-1">
                    <label for="concepto" class="label_header">Dias Plazo</label>
                    <input type="text" readonly class="form-control" name="concepto" id="concepto" value="@if(!is_null($ventas->dias_plazo)){{$ventas->dias_plazo}} @endif">
                </div>



                @if($ventas->tipo=='VENFA-CO' || $ventas->omni==1)
                @php
                $data= \Sis_medico\Ct_Detalle_Venta_Conglomerada::where('id_ct_ventas',$ventas->id)->get()->toArray();
                if($ventas->omni==1){
                $data= \Sis_medico\Ct_Detalle_Venta_Omni::where('id_ct_ventas',$ventas->id)->get()->toArray();
                }
                $productos= \Sis_medico\Ct_productos::where('id_empresa',$ventas->id_empresa)->get();
                $result = array();
                $key="id_paciente";
                foreach ($data as $val) {
                if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
                } else {
                $result[""][] = $val;
                }
                }
                @endphp
                <div class="col-md-12" id="items" style=" height: 400px; overflow-y: scroll; margin-top: 10px;">
                    @foreach($result as $s=>$z)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @php
                            $ek= \Sis_medico\Paciente::where('id',$s)->first();
                            @endphp
                            @if(!is_null($ek))
                            <label>{{$ek->apellido1}} {{$ek->apellido2}} {{$ek->nombre1}} {{$ek->nombre2}} </label>
                            <div class="panel-body" style="padding:0;">
                                <div class="col-md-12 table-responsive " style="padding:0 !important;">
                                    <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">
                                        <thead>
                                            <tr class="well-dark">
                                                <th width="35%" tabindex="0">Descripci&oacute;n del Producto</th>
                                                <th width="10%" tabindex="0">Cantidad</th>
                                                <th width="10%" tabindex="0">Precio</th>
                                                <th width="10%" tabindex="0">Cobrar Seguro</th>
                                                <th width="10%" tabindex="0">% Desc</th>
                                                <th width="10%" tabindex="0">Descuento</th>
                                                <th width="10%" tabindex="0">Precio Neto</th>
                                                <th width="5%" tabindex="0">IVA</th>
                                            </tr>
                                        </thead>
                                        <tbody id="entrega">

                                            @foreach($z as $a=>$x)
                                            <tr>
                                                <td style="max-width:100px;">

                                                    <input type="hidden" class="codigo_producto" />
                                                    <input type="hidden" name="verid[]" class="verid" value="1">
                                                    @php
                                                    $func= DB::table('ct_productos')->where('codigo',$x['id_ct_productos'])->first();

                                                    @endphp
                                                    <input type="text" class="form-control" style="width: 96%; height: 20px" value="{{$func->nombre}}" disabled>
                                                    <textarea wrap="hard" rows="3" class="form-control px-1 desc_producto" disabled placeholder="Detalle del producto">{{$x['detalle']}}</textarea>
                                                    <input type="hidden" name="iva[]" class="iva" />
                                                </td>
                                                <td>
                                                    <input class="form-control text-right cneto" disabled type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="@if(!is_null($x['cantidad'])) {{$x['cantidad']}} @endif" required>
                                                </td>
                                                <td>
                                                    @php
                                                    $precio= $x['precio'];
                                                    if(is_null($precio)){
                                                    $precio=0.00;
                                                    }
                                                    @endphp
                                                    <input type="text" class="form-control pneto" disabled onkeypress="return isNumberKey(event)" style="width:90%;display:inline;height:20px;" value="{{$precio}}">

                                                </td>
                                                <td>
                                                    <input class="form-control text-right copago" disabled type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" readonly>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                                    <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" required>
                                                </td>
                                                <td>
                                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" required>
                                                </td>
                                                <td>
                                                    <input class="form luffy" disabled type="checkbox" @if($x['check_iva']>0) selected="selected" @endif style="width: 80%;height:20px;" name="valoriva[]" value="@if($x['check_iva']!=null){{$x['check_iva']}}@endif">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="table-responsive wrap">
                    <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="55%" class="" tabindex="0"></th>
                                <th width="10%" class="" tabindex="0"></th>
                                <th width="10%" class="" tabindex="0"></th>
                                <th width="10%" class="" tabindex="0"></th>
                                <th width="10%" class="" tabindex="0"></th>
                                <th width="10%" class="" tabindex="0"></th>
                                <th width="10%" class="" tabindex="0"></th>
                                <th width="5%" class="" tabindex="0"></th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="subtotal_12" class="text-right px-1"></td>
                            </tr>
                        </tbody>
                        <tfoot class='well'>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal {{$porcentajeIva}}%</td>
                                <td id="subtotal_12" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->subtotal_12,2)}}@endif</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                <td id="subtotal_0" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->subtotal_0,2)}}@endif</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Descuento</td>
                                <td id="descuento" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->descuento,2)}}@endif</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Base Imponible</td>
                                <td id="base" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->base_imponible,2)}}@endif</td>
                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Tarifa Iva {{$porcentajeIva}}%</td>
                                <td id="tarifa_iva" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->impuesto,2)}}@endif</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Transporte</td>
                                <td id="transporte" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->transporte,2)}}@endif</td>
                                <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                <td id="total" class="text-right px-1"><strong>@if(!is_null($ventas)){{number_format($ventas->total_final,2)}}@endif</strong></td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
                @else
                <div class="table-responsive wrap">
                    <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr class='well-dark'>
                                <th width="55%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                                <th width="10%" class="" tabindex="0">Cantidad</th>
                                <th width="10%" class="" tabindex="0">Precio</th>
                                <th width="10%" class="" tabindex="0">Cobrar Seguro</th>
                                <th width="10%" class="" tabindex="0">% Desc</th>
                                <th width="10%" class="" tabindex="0">Descuento</th>
                                <th width="10%" class="" tabindex="0">Precio Neto</th>
                                <th width="5%" class="" tabindex="0">IVA</th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            @if($ventas->tipo=='VENFA-CO')
                            @foreach($detalle_venfaco as $key => $value)
                            @php
                            $ct_prods = DB::table('ct_productos')->where('codigo',$value->id_ct_productos)->first();
                            //dd($value);
                            @endphp
                            <tr class="well" style="position: relative;">
                                <!--<td style="width: 10%;text-align: center;">@if(!is_null($value->id_ct_productos)){{$value->id_ct_productos}}@endif</td>-->
                                <td style="width: 10%;">@if(!is_null($value->nombre)){{$value->id_ct_productos}} | {{$value->nombre}}@endif
                                    <span class="detalle_producto">@if(!is_null($ct_prods))<br />{{$ct_prods->descripcion}}@endif</span>
                                </td>

                                <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->precio)){{number_format($value->precio, 2)}}@endif</td>
                                <td style="width: 5%; text-align: center;"></td>
                                <td style="width: 5%; text-align: center;"></td>
                                <td style="width: 5%; text-align: center;">@if(isset($value->descuento)){{number_format($value->descuento, 2)}}@endif</td>
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{number_format($value->cantidad* $value->precio, 2)}}@endif</td>
                                <td style="width: 5%; text-align: center;">

                                    <input type="checkbox" disabled />

                                </td>
                            </tr>
                            @endforeach
                            @else
                            @php


                            $obs="";
                            if(count($detalle_venta)>0){
                            $obs = $detalle_venta[0]->codigo;
                            $cont= sizeof($detalle_venta);
                            $last_key = end(($detalle_venta));
                            }
                            @endphp
                            @foreach($detalle_venta as $key => $value)
                            <tr class="well" style="position: relative;">
                                <!--<td style="width: 10%;text-align: center;">@if(!is_null($value->id_ct_productos)){{$value->id_ct_productos}}@endif</td>-->
                                <td style="width: 10%;">@if(!is_null($value->nombre)){{$value->id_ct_productos}} | {{$value->nombre}}@endif
                                    <span class="detalle_producto">@if(!is_null($value->detalle))<br />{{$value->detalle}}@endif</span>
                                </td>
                                <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->bodega)){{$value->bodega}}@endif</td>-->
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                                <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->empaque)){{$value->empaque}}@endif</td>-->
                                <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->total)){{$value->total}}@endif</td>-->
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->precio)){{number_format($value->precio, 2)}}@endif</td>
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->copago)){{number_format($value->copago, 2)}}@endif</td>
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento_porcentaje)){{$value->descuento_porcentaje}}@endif</td>
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento)){{number_format($value->descuento, 2)}}@endif</td>

                                @if($value->copago > 0)
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->extendido)){{number_format($value->extendido / $value->cantidad, 2)}}@endif</td>
                                @else
                                <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{number_format($value->cantidad* $value->precio, 2)}}@endif </td>
                                @endif

                                <td style="width: 5%; text-align: center;">
                                    @if(($value->check_iva))<input type="checkbox" checked disabled />
                                    @else <input type="checkbox" disabled />@endif

                                </td>
                            </tr>
                            @if($cont>1)
                            @if ($value->codigo != $last_key[1]->codigo)
                            <tr>
                                <td colspan="5">
                                    {{$value->codigo}}
                                </td>
                            </tr>
                            @endif
                            @else
                            @if ($value->codigo !="")
                            <tr>
                                <td colspan="5">
                                    {{$value->codigo}}
                                </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif

                        <tfoot class='well'>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal {{$porcentajeIva}}%</td>
                                <td id="subtotal_12" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->subtotal_12,2)}}@endif</td>

                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                <td id="subtotal_0" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->subtotal_0,2)}}@endif</td>

                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Descuento</td>
                                <td id="descuento" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->descuento,2)}}@endif</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Base Imponible</td>
                                <td id="base" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->base_imponible,2)}}@endif</td>
                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Tarifa Iva {{$porcentajeIva}}%</td>
                                <td id="tarifa_iva" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->impuesto,2)}}@endif</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Transporte</td>
                                <td id="transporte" class="text-right px-1">@if(!is_null($ventas)){{number_format($ventas->transporte,2)}}@endif</td>
                                <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                <td id="total" class="text-right px-1"><strong>@if(!is_null($ventas)){{number_format($ventas->total_final,2)}}@endif</strong></td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
                @endif

                <div class="col-md-12">
                    <label for="diasPlazo" class="label_header">(Opcional) Nota: </label>
                    @php
                    $array = [];
                    if (!empty($ventas->nota_array)) {
                    $decoded = json_decode($ventas->nota_array, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                    $array = $decoded;
                    } else {
                    // Maneja el error de JSON aquí (por ejemplo, regístralo o establece $array como un array vacío)
                    $array = [];
                    }
                    }
                    @endphp
                    @foreach($array as $val)
                    <div class="col-md">
                        <textarea readonly name="nota_electronica[]" class="form-control nota_electronica_class" id="nota_electronica" maxlength="300">@if(isset($val->nombre)) {{$val->nombre}} @endif  - @if(isset($val->valor)) {{$val->valor}} @endif</textarea>
                    </div>
                    @endforeach

                </div>

                <div class="col-md-12" style="height:30px;">
                    <div class="row head-title">
                        <div class="col-md-12 cabecera">
                            <label class="color_texto">Forma de Pago</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 table-responsive ">
                    <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                    <table id="example1" role="grid" class="table table-bordered table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
                        <thead>
                            <tr class='well-dark'>
                                <th width="20%" style="text-align: center;">Tipo</th>
                                <th width="10%" style="text-align: center;">Fecha</th>
                                <th width="10%" style="text-align: center;">Número</th>
                                <th width="20%" style="text-align: center;">Banco</th>
                                <th width="10%" style="text-align: center;">Cuenta</th>
                                <th width="10%" style="text-align: center;">Girado a</th>
                                <th width="10%" style="text-align: center;">Valor</th>
                                <th width="10%" style="text-align: center;">Valor Base</th>
                                <!--<th width="5%" style="text-align: center;"><button id="btn_pago" type="button" class="btn btn-success btn-gray" >
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button></th>-->
                            </tr>
                        </thead>
                        <tbody id="agregar_pago">
                            @foreach($forma_pago as $value)
                            <tr class='well'>
                                <td width="20%" style="text-align: center;">
                                    <select class="form-control" name="id_tip_pago[]" disabled>
                                        <option value="">Seleccione</option>
                                        @foreach($tipo_pago as $val)
                                        <option value="{{$value->id}}" {{ $val->id == $value->tipo ? 'selected' : ''}}>{{$val->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td width="10%" style="text-align: center;">{{$value->fecha}}</td>
                                <td width="10%" style="text-align: center;">{{$value->numero}}</td>
                                <td width="20%" style="text-align: center;">
                                    <select class="form-control" name="id_tip_pago[]" disabled>
                                        <option value="">Seleccione</option>
                                        @foreach($lista_banco as $val)
                                        <option value="{{$value->id}}" {{ $val->id == $value->banco ? 'selected' : ''}}>{{$val->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td width="10%" style="text-align: center;">
                                    {{$value->cuenta}}
                                </td>
                                <td width="10%" style="text-align: center;">
                                    {{$value->giradoa}}
                                </td>
                                <td width="10%" style="text-align: center;">{{$value->valor}}</td>
                                <td width="10%" style="text-align: center;">{{$value->valor_base}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>

                <div class="box box-solid box-warning" style="background-color: white;display:none">
                    <div class="header box-header with-border">
                        <div class="box-title col-md-9"><b style="font-size: 16px;">FACTURA DE VENTA</b></div>
                        <div class="col-md-3" style="text-align: right;">
                            <button onclick="goBack()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;">
                                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                            </button>
                        </div>
                    </div>
                    <div class="box-body" style="background-color: #ffffff;">
                        <form class="form-vertical" id="crear_factura" role="form" method="POST">
                            {{ csrf_field() }}
                            <div class="col-md-12">
                                <div class="row">
                                    @if($ventas->estado == 1)
                                    <div class="col-md-2 col-xs-2">
                                        <label style="padding-left: 0px;font-size: 13px">Estado:</label>
                                        <div style="background-color: green;" class="form-control col-md-1"></div>
                                    </div>
                                    @elseif($ventas->estado == 0)
                                    <div class="col-md-2 col-xs-2">
                                        <label style="padding-left: 0px;font-size: 13px">Estado:</label>
                                        <div style="background-color: red;" class="form-control col-md-1"></div>
                                    </div>
                                    @endif
                                    <!--Identificador Relacional del documento generado por el Sistema.-->
                                    <div class="col-md-1 col-xs-1" style="padding-left: 2px;padding-right: 2px;">
                                        <label class="control-label" style="font-size: 13px">ID:</label>
                                        <div class="input-group">
                                            <input id="id" name="id" type="text" maxlength="11" class="form-control" value="@if(!is_null($ventas)){{$ventas->id}}@endif" disabled>
                                        </div>
                                    </div>
                                    <!--Número del Documento generado por el Sistema-->
                                    <div class="col-md-1 col-xs-1" style="padding-left: 2px;padding-right: 2px;">
                                        <label class="control-label" style="font-size: 13px">Número:</label>
                                        <div class="input-group">
                                            <input id="numero" name="numero" type="text" maxlength="25" class="form-control" value="@if(!is_null($ventas)){{$ventas->numero}}@endif" disabled>
                                        </div>
                                    </div>
                                    <!--Tipo del Documento generado por el Sistema-->
                                    <div class="col-md-1 col-xs-1" style="padding-left: 2px;padding-right: 2px;">
                                        <label class="control-label" style="font-size: 13px">Tipo:</label>
                                        <div class="input-group">
                                            <input id="tipo" name="tipo" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->tipo}}@endif" disabled>
                                        </div>
                                    </div>
                                    <!--Lista desplegable de divisas-->
                                    <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                        <label class="control-label" style="font-size: 13px">Divisas:</label>
                                        <!--<div class="input-group">
                                    <input id="divisas" name="divisas" type="text"  class="form-control" value="@if(!is_null($ventas)){{$ventas->divisas}}@endif" disabled>
                                </div>-->
                                        <select id="divisas" name="divisas" class="form-control" style="width: 100%" disabled>
                                            <option value="">Seleccione...</option>
                                            @foreach($divisas as $value)
                                            <option {{ $ventas->divisas == $value->id ? 'selected' : ''}} value="$value->id">{{$value->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!--Fecha de la transacción; puede ser modificada por el usuario-->
                                    <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                        <label class="control-label" style="font-size: 13px">Fecha de Emisión:</label>
                                        <div class="input-group">
                                            <input id="fecha" name="fecha" type="date" class="form-control" value="{{$ventas->fecha}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">&nbsp;</div>
                            <div class="col-md-12">
                                <div class="card1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h8>DATOS CLIENTE</h8>
                                        </div>
                                        <!--Código del Cliente-->
                                        <div class="col-md-2 col-xs-2">
                                            <label for="id" class="control-label" style="font-size: 13px">Cliente:</label>
                                            <select id="cliente" name="cliente" class="form-control select2_cliente" style="width: 100%">
                                                <option value="">Seleccione...</option>
                                                
                                                <option selected value="">{{$ventas->nombre_cliente}}</option>
                                                
                                            </select>
                                        </div>
                                        <!--Direccion del Cliente-->
                                        <div class="col-md-3 col-xs-3" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Dirección:</label>
                                            <input id="direccion" name="direccion" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->direccion_cliente}}@endif" disabled>
                                        </div>
                                        <!--Ruc/Cid del Cliente-->
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">RUC/CID:</label>
                                            <div class="input-group">
                                                <input id="ruc_cedula" name="ruc_cedula" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->ruc_id_cliente}}@endif" disabled>
                                            </div>
                                        </div>
                                        <!--Telefono del Cliente-->
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Teléfono:</label>
                                            <div class="input-group">
                                                <input id="telefono" name="telefono" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->telefono_cliente}}@endif" disabled>
                                            </div>
                                        </div>
                                        <!--Email del Cliente-->
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Email:</label>
                                            <div class="input-group">
                                                <input id="email" name="email" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->email_cliente}}@endif" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">&nbsp;</div>
                            @php
                            if(!is_null($ventas)){
                            $seguro = Sis_medico\Seguro::find($ventas->seguro_paciente);
                            }
                            @endphp
                            @if(!is_null($ventas->id_paciente))
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h8>DATOS PACIENTE</h8>
                                        </div>
                                        <!--Cedula Paciente-->
                                        <div class="col-md-2 col-xs-2">
                                            <label class="control-label" style="font-size: 13px">Cédula:</label>
                                            <input id="ced_paciente" name="ced_paciente" type="number" class="form-control" value="@if(!is_null($ventas)){{$ventas->id_paciente}}@endif" disabled>
                                        </div>
                                        <!--Nombre Paciente-->
                                        <div class="col-md-3 col-xs-3" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Paciente:</label>
                                            <input id="nomb_paciente" name="nomb_paciente" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->nombres_paciente}}@endif" disabled>
                                        </div>
                                        <!--Seguro Paciente-->
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Seguro:</label>
                                            <input id="seguro" name="seguro" type="text" class="form-control" value="@if(!is_null($seguro)){{$seguro->nombre}}@endif" disabled>
                                        </div>
                                        <!--Procedimiento-->
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Procedimiento:</label>
                                            <input id="procedimiento" name="procedimiento" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->procedimientos}}@endif" disabled>
                                        </div>
                                        <!--Fecha de Procedimiento-->
                                        <div class="col-md-2 col-xs-2" style="padding-left: 2px;padding-right: 2px;">
                                            <label class="control-label" style="font-size: 13px">Fecha de Procedimiento:</label>
                                            <input id="fecha_proced" name="fecha_proced" type="date" class="form-control" value="@if(!is_null($ventas)){{$ventas->fecha_procedimiento}}@endif" disabled>
                                        </div>
                                        <div class="col-md-2 px-0">
                                            <select class="form-control" name="tipo_consulta" id="tipo_consulta">
                                                <option value=""> --- Tipo --- </option>
                                                <option value="1" {{ $ventas->tipo_consulta == "1" ? 'selected' : ''}}>Consulta</option>
                                                <option value="2" {{ $ventas->tipo_consulta == "2" ? 'selected' : ''}}>Procedimiento</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">&nbsp;</div>
                            @if(!is_null($forma_pago))
                            <div class="form-group col-md-12">
                                <div class="card">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <h8>FORMAS DE PAGO</h8>
                                        </div>
                                        <div class="form-group col-md-3 col-sm-3">
                                            <label for="efectivo" class="col-form-label-sm">VALOR EFECTIVO</label>
                                            <input type="text" id="valor_efectivo" name="valor_efectivo" class="form-control form-control-sm" placeholder="EFECTIVO" value="@if(!is_null($forma_pago))@endif" disabled>
                                        </div>



                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">&nbsp; </div>
                            <div class="col-md-12" style="padding-top: 8px">
                                <div class="row">
                                    <!--Nota-->
                                    <label class="col-md-1 control-label" style="font-size: 13px">Detalle Asiento:</label>
                                    <div class="col-md-6">
                                        <input id="nota" name="nota" type="text" class="form-control" value="@if(!is_null($ventas)){{$ventas->nota}}@endif" disabled>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12"> &nbsp;</div>
                            <!--Formas de Pago -->
                        </form>
                        <div class="table-responsive col-mds-12" style="min-height: 100px; max-height: 250px;">
                            <table id="example2" role="grid" aria-describedby="example2_info">
                                <caption><b>Detalle de Productos y Servicios</b></caption>
                                <thead style="background-color: #FFF3E3">
                                    <tr style="position: relative;">
                                        <th style="width: 10%; text-align: center;">Códigos</th>
                                        <th style="width: 15%; text-align: center;">Descripción del Producto/Servicio</th>
                                        <th style="width: 5%; text-align: center;">Cantidad</th>
                                        <th style="width: 5%; text-align: center;">Precio Unitario</th>
                                        <th style="width: 5%; text-align: center;">Des%</th>
                                        <th style="width: 5%; text-align: center;">Desc.</th>
                                        <th style="width: 5%; text-align: center;">Precio Neto</th>
                                        <th style="width: 1%; text-align: center;">Iva</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalle_venta as $value)
                                    <tr style="position: relative;">
                                        <td style="width: 10%;text-align: center;">@if(!is_null($value->id_ct_productos)){{$value->id_ct_productos}}@endif</td>
                                        <td style="width: 10%; text-align: center;">@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                                        <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->bodega)){{$value->bodega}}@endif</td>-->
                                        <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)) 1 @endif</td>
                                        <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->empaque)){{$value->empaque}}@endif</td>-->
                                        <!--<td style="width: 5%; text-align: center;">@if(!is_null($value->total)){{$value->total}}@endif</td>-->
                                        <td style="width: 5%; text-align: center;">@if(!is_null($value->precio)){{$value->precio}}@endif</td>
                                        <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento_porcentaje)){{$value->descuento_porcentaje}}@endif</td>
                                        <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento)){{$value->descuento}}@endif</td>
                                        <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                                        <td style="width: 5%; text-align: center;"><input type="checkbox" @if($value->check_iva == '1') checked @endif disabled></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--Calculo de Valores -->
                        <div class="col-md-12" style="padding-top: 20px">
                            <div class="row">
                                <div class="col-md-9"></div>
                                <div class="col-md-3">
                                    <div class="col-md-12" style="padding: 5px">
                                        <div class="row">
                                            <label class="col-md-6">Subtotal sin IVA</label>
                                            <input class="col-md-6" type="text" name="subtotal" id="subtotal" disabled>

                                        </div>

                                    </div>
                                    <div class="col-md-12" style="padding: 5px">
                                        <div class="row">
                                            <label class="col-md-6">Descuento</label>
                                            <input class="col-md-6" type="text" name="descuento" id="descuento" value="@if(!is_null($ventas)){{$ventas->descuento}}@endif" disabled>

                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding: 5px">
                                        <div class="row">
                                            <label class="col-md-6">Base Imponible</label>
                                            <input class="col-md-6" type="text" name="descuento" id="descuento" value="@if(!is_null($ventas)){{$ventas->base_imponible}}@endif" disabled>

                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding: 5px">
                                        <div class="row">
                                            <label class="col-md-6">Tarifa Iva 12%</label>
                                            <input class="col-md-6" type="text" name="impuesto" id="impuesto" value="@if(!is_null($ventas)){{$ventas->impuesto}}@endif" disabled>

                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding: 5px">
                                        <div class="row">
                                            <label class="col-md-6">Transporte</label>
                                            <input class="col-md-6" type="text" name="transporte" id="transporte" value="@if(!is_null($ventas)){{$ventas->transporte}}@endif" disabled>

                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding: 5px">
                                        <div class="row">
                                            <label class="col-md-6">Total</label>
                                            <input class="col-md-6" type="text" name="total" id="total" value="@if(!is_null($ventas)){{$ventas->total_final}}@endif" disabled>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                @php
                if(!is_null($ventas)){
                $procedimientos = Sis_medico\Ct_Factura_Procedimiento::where('id_ct_ventas', $ventas->id)->count();

                }
                @endphp
                @if($procedimientos>0)
                <div class="col-md-12">

                    <a href="{{ route('venta.excel', ['id' => $ventas->id]) }}" class="btn btn-primary">Ver Excel</a>
                </div>
                @endif

            </form>
            <div class="col-md-12 px-1" style="text-align:center; display: none;">
                <button type="button" class="btn btn-success btn-gray jef"> <i class="fa fa-save"></i> Actualizar </button>
            </div>
        </div>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="{{ asset ('/js/icheck.js') }}"></script>
        <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
        <script src="{{ asset ('/hospital/cleave/dist/cleave.min.js')}}"></script>
        <script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                $('.select2_cliente').select2({
                    tags: false
                });

                $('#iva').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    increaseArea: '20%' // optional
                });

            });


            $('#example2').DataTable({
                'paging': false,
                'lengthChange': false,
                'searching': false,
                'ordering': false,
                'info': false,
                'autoWidth': false,
                'order': [
                    [1, "asc"]
                ]
            });

            function goBack() {
                window.history.back();
                //location.href="{{ route('venta_index') }}";
            }

            $(document).on("focus", "#identificacion_cliente", function() {
                $("#identificacion_cliente").autocomplete({

                    source: function(request, response) {
                        $.ajax({
                            type: 'GET',
                            url: "{{route('ventas.buscarclientexid')}}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    change: function(event, ui) {
                        $("#nombre_cliente").val(ui.item.nombre);
                    },
                    selectFirst: true,
                    minLength: 1,
                });
            });

            function nombre() {
                $.ajax({
                    type: 'GET',
                    url: "{{route('ventas.buscarclientexid')}}",
                    dataType: "json",
                    data: {
                        term: $("#identificacion_cliente").val(),
                    },
                    success: function(data) {
                        //response(data);
                        console.log(data);
                        $("#nombre_cliente").val(data[0].nombre);
                    }
                });
            }

            function cargar_nivel() {

                var id_emp = $('#id_empresa').val();
                var xseguro = $('#id_seguro').val();

                $.ajax({
                    type: 'post',
                    url: "{{route('lista_nivel.seguro')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'id_seguro': xseguro,
                        'id_empresa': id_emp
                    },
                    success: function(data) {
                        console.log(data);

                        if (data.value != 'no') {
                            if (xseguro != 0) {
                                $("#id_nivel").empty();
                                $.each(data, function(key, registro) {
                                    $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                                });
                            } else {

                                $("#id_nivel").empty();

                            }

                        } else {
                            document.getElementById("ident_nivel").style.visibility = "hidden";
                        }

                    },
                    error: function(data) {

                    }
                });
            }

            var fila = $("#mifila").html();
            var existeCliente = false;
            $('.select2_cuentas').select2({
                tags: false
            });
            $(document).on("focus", "#nombre_cliente", function() {
                $("#nombre_cliente").autocomplete({
                    classes: {
                        "ui-autocomplete": "dob_autocomplete",
                    },
                    source: function(request, response) {
                        $.ajax({
                            type: 'GET',
                            url: "{{route('ventas.buscarcliente')}}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    change: function(event, ui) {
                        $("#identificacion_cliente").val(ui.item.id);

                        $("#direccion_cliente").val(ui.item.direccion);
                        $("#ciudad_cliente").val(ui.item.ciudad);
                        $("#mail_cliente").val(ui.item.mail);
                        $("#telefono_cliente").val(ui.item.telefono);
                        $("#tipo_cliente").val(ui.item.tipo);
                        totales(0);
                    },
                    selectFirst: true,
                    minLength: 1,
                });

            });

            $(document).on("focus", "#identificacion_cliente", function() {
                $("#identificacion_cliente").autocomplete({
                    classes: {
                        "ui-autocomplete": "dob_autocomplete",
                    },
                    source: function(request, response) {
                        $.ajax({
                            type: 'GET',
                            url: "{{route('ventas.buscarclientexid')}}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                                //console.log("identificacion_cliente", data.id);
                                if (data.id == "") {
                                    //swal("el cliente no existe");
                                    existeCliente = false;
                                } else {
                                    existeCliente = true;
                                }
                            }
                        });
                    },
                    change: function(event, ui) {
                        $("#nombre_cliente").val(ui.item.nombre);

                        $("#direccion_cliente").val(ui.item.direccion);
                        $("#ciudad_cliente").val(ui.item.ciudad);
                        $("#mail_cliente").val(ui.item.mail);
                        $("#telefono_cliente").val(ui.item.telefono);
                        $("#tipo_cliente").val(ui.item.tipo);
                        totales(0);
                    },
                    selectFirst: true,
                    minLength: 1,
                });
            });
            $(document).on("focus", "#identificacion_paciente", function() {
                $("#identificacion_paciente").autocomplete({
                    classes: {
                        "ui-autocomplete": "dob_autocomplete",
                    },
                    source: function(request, response) {
                        $.ajax({
                            type: 'GET',
                            url: "{{route('ventas.buscarpaciente')}}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                                if (data.id == "") {
                                    //swal("el cliente no existe");
                                    existeCliente = false;
                                }
                            }
                        });
                    },
                    change: function(event, ui) {
                        if (ui.item != null) {
                            //console.log(ui.item);
                            $("#nombre_paciente").val(ui.item.nombre);
                            $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                        } else {
                            swal("No Existe el paciente");
                        }
                        /*$("#direccion_cliente").val(ui.item.direccion);
                        $("#ciudad_cliente").val(ui.item.ciudad);
                        $("#mail_cliente").val(ui.item.mail);
                        $("#telefono_cliente").val(ui.item.telefono);*/
                    },
                    selectFirst: true,
                    minLength: 3,
                });
            });
            $(document).on("focus", "#nombre_paciente", function() {
                $("#nombre_paciente").autocomplete({
                    classes: {
                        "ui-autocomplete": "dob_autocomplete",
                    },
                    source: function(request, response) {
                        $.ajax({
                            type: 'GET',
                            url: "{{route('ventas.buscarpaciente_nombre')}}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                                if (data.id == "") {
                                    //swal("el cliente no existe");
                                    existeCliente = false;
                                }
                            }
                        });
                    },
                    change: function(event, ui) {
                        if (ui.item != null) {
                            console.log(ui.item);
                            $("#identificacion_paciente").val(ui.item.nombre);
                            $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                        } else {
                            swal("No Existe el paciente");
                        }
                        /*$("#direccion_cliente").val(ui.item.direccion);
                        $("#ciudad_cliente").val(ui.item.ciudad);
                        $("#mail_cliente").val(ui.item.mail);
                        $("#telefono_cliente").val(ui.item.telefono);*/
                    },
                    selectFirst: true,
                    minLength: 3,
                });
            });

            $('body').on('click', '.delete', function() {
                console.log($(this));

                $(this).parent().parent().remove();
                totales(0);
            });
            $('body').on('click', '.jef', function() {
                $('#forms').submit();
            });

            $(document).ready(function() {


                limpiar();

                $('.select2_cliente').select2({
                    tags: false
                });

                $('#iva').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    increaseArea: '20%'
                });

            });

            $('body').on('click', '.cp', function() {
                console.log($(this));
                console.log($(this).prev().attr('class'));
                var clase = $(this).prev().attr('class');
                var html = '<input type="text" class="form-control pneto"  name="precio[]" style="width:40%;display:inline;height:20px;">' +
                    '<button type="button" class="btn btn-info btn-gray btn-xs cp">' +
                    '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>' +
                    '</button>';
                console.log($(this).parent());
                if (clase.includes('select2_precio')) {
                    $(this).parent().append(html);
                    $(this).prev().remove();
                    $(this).remove();

                } else {
                    html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" autofocus active required>' +
                        '<option value="0"> </option></select>' +
                        '<button type="button" class="btn btn-info btn-gray btn-xs cp" >' +
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
                    // $(this).parent().empty();
                    // $(this).parent().append(html);
                    $(this).parent().append(html);
                    verificar($(this).parent().prev().prev().children().closest('.select2_cuentas'));
                    $(this).prev().remove();
                    $(this).remove();

                }


            });
            /*function changePrecio(i, e){
            var html = '<input type="text" class="form-control pneto" style="width:40%;display:inline;height:20px;">'+
                        '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,2)">'+
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>'+
                        '</button>';
                        console.log(i.parent());
            if(e==1){

                $("#tprecio").empty();
                $("#tprecio").append(html);
            }else{
                html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;display:inline;" required>'+
                        '<option value="0"> </option></select>'+
                        '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,1)">'+
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
                $("#tprecio").empty();
                $("#tprecio").append(html);
                console.log($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
                verificar($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
            }
            }*/

            function goNew() {
                $(".btn_add").attr("disabled", false);
                location.href = "{{route('ventas_crear')}}";
                $("#asiento").val("");
                $("#id").val("");
                $("#numero").val("");

            }

            //Desabilita Componentes
            function desabilita_componente(elemento, id) {

                var id_tipo = parseInt(elemento.value);

                if ((id_tipo == 1) || (id_tipo == 4) || (id_tipo == 5) || (id_tipo == 6)) {

                    $('#fecha' + id).val("");

                    document.getElementById('fecha' + id).disabled = true;
                    document.getElementById('numero' + id).disabled = true;
                    document.getElementById('id_banco' + id).disabled = true;
                    document.getElementById('id_cuenta_pago' + id).disabled = true;
                    document.getElementById('giradoa' + id).disabled = true;

                } else {

                    if ((id_tipo == 2) || (id_tipo == 3)) {

                        document.getElementById('fecha' + id).disabled = false;
                        document.getElementById('numero' + id).disabled = false;
                        document.getElementById('id_banco' + id).disabled = false;
                        document.getElementById('id_cuenta_pago' + id).disabled = false;
                        document.getElementById('giradoa' + id).disabled = false;

                    }

                }
            }


            function verificar(e) {
                var iva = $('option:selected', e).data("iva");
                var codigo = $('option:selected', e).data("codigo");
                var usadescuento = $('option:selected', e).data("descuento");
                var max = $('option:selected', e).data("maxdesc");
                var modPrecio = $('option:selected', e).data("precio");

                $(e).parent().children().closest(".codigo_producto").val(codigo);
                $(e).parent().children().closest(".iva").val(iva);
                //console.log($(e).parent().next().next().children().closest(".cp"));

                if (modPrecio) {
                    //$(e).parent().next().next().closest(".cp");
                    //console.log("modifica precio");
                    $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
                } else {
                    //console.log("no modifca el precio");
                    $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
                }
                if (!usadescuento) {
                    $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
                    $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
                    $(e).parent().next().next().next().next().children().val(0);
                    $(e).parent().next().next().next().next().next().children().val(0);
                } else {
                    $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
                    $(e).parent().next().next().next().next().children().removeAttr("readonly");
                    $(e).parent().next().next().next().next().next().children().val(0);
                    $(e).parent().next().next().next().next().children().val(0);
                }
                $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
                if (iva == '1') {
                    $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
                } else {
                    $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
                }

                //cargarPrecios
                var tipo = $("#tipo_cliente").val();
                var selected = "";

                //Obtiene Id_Seguro y Id_Nivel
                var id_seg = $("#id_seguro").val();
                var id_niv = $("#id_nivel").val();

                $.ajax({
                    type: 'post',
                    url: "{{route('precios')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        id: codigo,
                        id_seguro: id_seg,
                        id_nivel: id_niv
                    },
                    success: function(data) {

                        //alert(data.nivel);

                        $(e).parent().next().next().children().find('option').remove();
                        $.each(data, function(key, value) {

                            if (tipo == value.nivel) {

                                selected = "selected";
                            } else {
                                selected = "";
                            }
                            $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');

                        });

                    },
                    error: function(data) {
                        console.log(data.responseText);
                    }
                });
            }

            //cantidad
            //precio
            //copago
            //%descuento
            //descuento
            //precioneto
            $('body').on('blur', '.pneto', function() {
                // verificar(this);
                var cant = $(this).parent().prev().children().val();
                var copago = $(this).parent().next().children().val();
                var descuento = $(this).parent().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().next().children().val(total);
                totales(0);
            });
            $('body').on('active', '.pneto', function() {
                // verificar(this);
                var cant = $(this).parent().prev().children().val();
                var copago = $(this).parent().next().children().val();
                var descuento = $(this).parent().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().next().children().val(total);
                totales(0);
            });
            $('body').on('change', '.luffy', function() {
                var cant = parseFloat($(this).parent().prev().prev().prev().prev().prev().children().val());
                var precio = parseFloat($(this).val());
                var totales = precio / cant;
                totales = redondeafinal(totales);
                $(this).parent().prev().prev().prev().prev().find('.cp').click();
                $(this).parent().prev().prev().prev().prev().children().val(totales);
                totales(0);

                //$(this).val(totales);
            });
            $('body').on('change', '.pneto', function() {
                // verificar(this);
                var cant = $(this).parent().prev().children().val();
                var copago = $(this).parent().next().children().val();
                var descuento = $(this).parent().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().next().children().val(total);
                totales(0);
            });
            $('body').on('change', '.cneto', function() {
                // verificar(this);
                var cant = $(this).val();
                var precio = $(this).parent().next().children().val();
                // console.log("this", $(this).parent().next().children().val());
                var copago = $(this).parent().next().next().children().val();
                //console.log("copago", copago);
                var descuento = $(this).parent().next().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().next().next().children().val(total);

                totales(0);
            });

            $('body').on('change', '.copago', function() {
                verificar(this);
                var cant = $(this).parent().prev().prev().children().val();
                var precio = $(this).parent().prev().children().val();

                var copago = $(this).val();
                console.log("copago", copago);
                var descuento = $(this).parent().next().next().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                console.log(total);
                total = redondeafinal(total);
                $(this).parent().next().next().next().children().val(total);

                totales(0);
            });


            $('body').on('change', '.pdesc', function() {

                var m = $(this).next().val();
                var cant = $(this).parent().prev().prev().prev().children().val();
                var precio = $(this).parent().prev().prev().children().val();
                var pdesc = $(this).val();
                //console.log("el descuento maximo debe de ser", m, pdesc);
                if (parseFloat(pdesc) > parseFloat(m)) {
                    swal("El descuento no puede ser mayor a " + m + "%");
                    $(this).val(m).focus();
                }
                var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
                $(this).parent().next().children().val(descuento.toFixed(2));
                var copago = $(this).parent().prev().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().children().val(total);
                totales(0);
            });
            $('body').on('change', '.desc', function() {
                var m = verificar(this);
                var cant = $(this).parent().prev().prev().prev().prev().children().val();
                var precio = $(this).parent().prev().prev().prev().children().val();
                /*if(pdesc> m){
                    swal("El descuento no puede ser mayor a "+m+"%");
                    $(this).val(m);
                }*/
                var descuento = $(this).val();
                verificar(this);
                //console.log(cant, precio);
                var pdesc = 0;
                if (cant == 0 || precio == 0) {
                    pdesc = 0;
                } else {
                    pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
                }
                //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
                $(this).parent().prev().children().val(pdesc);
                var copago = $(this).parent().prev().prev().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().children().val(total);
                totales(0);
            });

            $('body').on('change', '.fpago', function() {
                var total_pagos = 0;
                $('.fpago').each(function(i, obj) {
                    total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
                });
                $("#valor_totalPagos").val(total_pagos);
            });

            function totales(e) {
                var subt12 = [];
                var subt0 = [];
                var descuento = [];
                var descuentosub0 = 0;
                var descuentosub12 = 0;
                var sb12 = 0;
                var sb0 = 0;
                var d = 0;
                var copagoTotal = 0;
                if (e == 0) {
                    $('.cneto').each(function(i, obj) {
                        var cant = $(this).val();
                        var e = $(this).parent().prev().children().closest(".select2_cuentas");
                        var precio1 = 0;
                        var precio2 = 0;
                        var precio3 = 0;
                        var precio4 = 0;
                        var precio5 = 0;
                        var precioAut = 0;
                        var tipo = $("#tipo_cliente").val();
                        //console.log("el e es: ", e.val());

                        var precio = $(this).parent().next().children().val();
                        var copago = $(this).parent().next().next().children().val();
                        var descuento = $(this).parent().next().next().next().next().children().val();
                        d = parseFloat(d) + parseFloat(descuento);
                        var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                        //console.log(iva);
                        precio = precio != null ? precio : 0;
                        var total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago);
                        //console.log("precio y cantidad"+total);
                        if (iva) {
                            subt12.push(total);
                            sb12 = sb12 + total;
                            descuentosub12 += parseFloat(descuento);
                        } else {
                            subt0.push(total);
                            sb0 = sb0 + total;
                            descuentosub0 += parseFloat(descuento);
                        }
                        copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                        //aqui falta
                        //console.log("subtotal12"+sb12);
                        $("#subtotal_12").html(sb12.toFixed(2));
                        $("#subtotal_0").html(sb0.toFixed(2));
                        $("#descuento").html(d.toFixed(2));
                        var descuento_total = descuentosub12 + descuentosub0;
                        var sum = sb12 + sb0 - descuento_total;
                        $("#base").html(sum.toFixed(2, 2));
                        sum = redondeafinal(sum);

                        var iva = $("#ivareal").val();
                        var ti = iva * sb12;
                        //console.log(ti);
                        if (d > 0) {
                            if (sb12 > 0) {
                                ti = iva * (sb12 - descuentosub12);
                            }

                        }
                        ti = redondeafinal(ti);
                        $("#tarifa_iva").html(ti);
                        var t = sb12 + sb0 + ti - d;
                        var totax = sum + ti;
                        totax = redondeafinal(totax);
                        $("#total").html(totax);
                        $("#copagoTotal").html(copagoTotal.toFixed(2));
                        sb12 = redondeafinal(sb12);
                        $("#subtotal_121").val(sb12);
                        sb0 = redondeafinal(sb0);
                        d = redondeafinal(d);
                        $("#subtotal_01").val(sb0);
                        $("#descuento1").val(d);
                        $("#tarifa_iva1").val(ti);
                        $("#total1").val(totax);
                        $("#totalc").val(copagoTotal.toFixed(2));
                    });
                }
            }
            //new change to round best because no square values date: 30 Nov 2020 10:19 AM
            function redondeafinal(num, decimales = 2) {
                var signo = (num >= 0 ? 1 : -1);
                num = num * signo;
                //console.log("eduardo maricon");
                if (decimales === 0) //con 0 decimales

                    return signo * Math.round(num);
                // round(x * 10 ^ decimales)
                num = num.toString().split('e');
                num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
                // x * 10 ^ (-decimales)
                num = num.toString().split('e');
                return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
            }

            function isNumberKey(evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode
                if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                    return false;
                return true;
            }

            function nuevo() {
                var nuevafila = $("#mifila").html();
                var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
                //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
                rowk.innerHTML = fila;
                //rowk.className="well";
                $('.select2_cuentas').select2({
                    tags: false
                });
            }


            function ingresar_cero() {
                var secuencia_factura = $('#numero').val();
                secuencia_factura = secuencia_factura.trim();
                var digitos = 9;
                var ceros = 0;
                var varos = '0';
                var secuencia = 0;
                if (secuencia_factura > 0) {
                    var longitud = parseInt(secuencia_factura.length);
                    if (longitud > 10) {
                        swal("Error!", "Valor no permitido", "error");
                        $('#numero').val('');

                    } else {
                        var concadenate = parseInt(digitos - longitud);
                        switch (longitud) {
                            case 1:
                                secuencia = '000000000';
                                break;
                            case 2:
                                secuencia = '00000000';
                                break;
                            case 3:
                                secuencia = '0000000';
                                break;
                            case 4:
                                secuencia = '000000';
                                break;
                            case 5:
                                secuencia = '00000';
                                break;
                            case 6:
                                secuencia = '0000';
                                break;
                            case 7:
                                secuencia = '000';
                                break;
                            case 8:
                                secuencia = '00';
                                break;
                            case 9:
                                secuencia = '0';
                        }
                        $('#numero').val(secuencia + secuencia_factura);
                    }


                } else {
                    swal("Error!", "Valor no permitido", "error");
                    $('#numero').val('');
                }
            }

            $(".btn_add").click(function() {

                if ($("#form").valid()) {
                    var contador_pago = parseInt($("#contador_pago").val());
                    $(".print").css('visibility', 'visible');
                    var contador = 0;
                    $(".pago").each(function() {
                        contador++;
                    });
                    var se = $("#electronica").val();
                    if (se == "0" || se == "") {
                        contador++;
                    }
                    if (contador > 0) {
                        $(".btn_add").attr("disabled", true);
                        $("#mifila").html("");
                        $.ajax({
                            url: "{{route('ventas_store')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            type: 'POST',
                            datatype: 'json',
                            data: $("#form").serialize(),
                            success: function(data) {
                                console.log(data);
                                if (data != "error") {
                                    $("#asiento").val(data.idasiento);
                                    $("#id").val(data.idventa);
                                    $("#numero").val(data.num_vent);
                                    if (data.error != "no") {
                                        swal("Error", data.error, "error");
                                    } else {
                                        if (data.getSri == "ok" || data.getSri == "No") {
                                            swal("Mensaje: ", "Guardado con Exito!", "success");
                                        } else {
                                            swal(data.getSri);
                                        }
                                    }


                                } else {
                                    swal("Error! No guardo correctamente, intente de nuevo");
                                }

                            },
                            error: function(data) {
                                console.log(data.responseText);
                            }
                        });
                    } else {

                        swal("Mensaje:", "Faltan pagos por ingresar", "error")
                    }

                } else {
                    swal("Tiene campos vacios");
                    console.log($("#form").serialize());
                }

            });




            //Elimina Registro de la Tabla Forma de Pago
            function eliminar_form_pag(valor) {
                var dato_pago1 = "dato_pago" + valor;
                var nombre_pago2 = 'visibilidad_pago' + valor;
                alert("entra");
                document.getElementById(dato_pago1).style.display = 'none';
                document.getElementById(nombre_pago2).value = 0;
            }

            function revisar_componentes(e, id) {
                metodo = $('#id_tip_pago' + id).val();
                if (metodo == 1) {
                    $("#tipo_tarjeta" + id).prop('disabled', true);
                    $("#numero_pago" + id).prop('disabled', true);
                    $("#id_banco_pago" + id).prop('disabled', true);
                    $("#id_cuenta_pago" + id).prop('disabled', true);
                    //$("#fi"+id).prop('disabled', true);
                    revision_total(id);
                } else if (metodo == 2) {
                    $("#tipo_tarjeta" + id).prop('disabled', true);
                    $("#numero_pago" + id).prop('disabled', false);
                    $("#id_banco_pago" + id).prop('disabled', false);
                    $("#id_cuenta_pago" + id).prop('disabled', false);
                    //$("#fi"+id).prop('disabled', true);
                    revision_total(id);
                } else if (metodo == 3) {
                    $("#tipo_tarjeta" + id).prop('disabled', true);
                    $("#numero_pago" + id).prop('disabled', false);
                    $("#id_banco_pago" + id).prop('disabled', false);
                    $("#id_cuenta_pago" + id).prop('disabled', false);
                    //$("#fi"+id).prop('disabled', true);
                    revision_total(id);
                } else if (metodo == 4) {
                    $("#tipo_tarjeta" + id).prop('disabled', false);
                    $("#numero_pago" + id).prop('disabled', false);
                    $("#id_banco_pago" + id).prop('disabled', false);
                    $("#id_cuenta_pago" + id).prop('disabled', false);
                    //$("#fi"+id).prop('disabled', false);
                    revision_total(id);
                } else if (metodo == 5) {
                    $("#tipo_tarjeta" + id).prop('disabled', false);
                    $("#numero_pago" + id).prop('disabled', false);
                    $("#id_banco_pago" + id).prop('disabled', false);
                    $("#id_cuenta_pago" + id).prop('disabled', false);
                    //$("#fi"+id).prop('disabled', false);
                    revision_total(id);
                } else if (metodo == 6) {
                    $("#tipo_tarjeta" + id).prop('disabled', false);
                    $("#numero_pago" + id).prop('disabled', false);
                    $("#id_banco_pago" + id).prop('disabled', false);
                    $("#id_cuenta_pago" + id).prop('disabled', false);
                    //$("#fi"+id).prop('disabled', false);
                    revision_total(id);
                }
            }


            function soloNumeros(e) {
                // capturamos la tecla pulsada
                var teclaPulsada = window.event ? window.event.keyCode : e.which;

                // capturamos el contenido del input
                var valor = e.value;

                // 45 = tecla simbolo menos (-)
                // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
                // Modificamos el contenido del mismo añadiendo el simbolo menos al
                // inicio
                console.log("indexof", valor);
                if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
                    document.getElementById("inputNumero").value = "-" + valor;
                }

                // 13 = tecla enter
                // 46 = tecla punto (.)
                // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
                // punto
                if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
                    return true;
                }

                // devolvemos true o false dependiendo de si es numerico o no
                return /\d/.test(String.fromCharCode(teclaPulsada));
            }

            function revision_total(id) {

                var valor = $('#valor' + id).val();
                ntotal = valor * 1;
                $('#valor_base' + id).val(ntotal.toFixed(2));

            }

            function redondea_valor_base(e, id, val) {
                return parseFloat(e.value).toFixed(val);
            }

            function reloadCliente() {
                if (!existeCliente) {
                    //swal("epa");
                }

            }

            function goBack() {
                location.href = "{{ route('venta_index') }}";
            }

            function crear_factura_venta() {

                //$('#crear_factura').submit();
                var valor_total = $('#total1').val();

                var formulario = document.forms["crear_factura"];

                //Datos Cabecera Factura
                var divisas = formulario.divisas.value;

                //Datos Generales
                var id_emp = formulario.id_empresa.value;
                var sucurs = formulario.sucursal.value;
                var punt_emision = formulario.punto_emision.value;


                //Datos Paciente
                var cedula = formulario.ced_paciente.value;
                var nombre_paciente = formulario.nomb_paciente.value;
                var seguro_paciente = formulario.id_seguro.value;
                var proced = formulario.procedimiento.value;
                var fech_proced = formulario.fecha_proced.value;


                //Datos Clientes
                var cliente = formulario.cliente.value;
                var ruc_ced_cliente = formulario.ruc_cedula.value;
                var direccion = formulario.direccion.value;
                var ciud_cliente = formulario.ciudad.value;
                var email_cliente = formulario.email.value;
                var telf_cliente = formulario.telefono.value;


                //Datos Recaudador
                var recaud = formulario.recaudador.value;
                var ced_recaudador = formulario.cedula_recaudador.value;

                //Detalle de Asiento
                var det_asiento = formulario.nota.value;

                var msj = "";


                if (divisas == "") {
                    msj += "Por favor,Selecione la divisa\n";
                }


                //Datos Generales

                if (id_emp == "") {
                    msj += "Por favor, Seleccione la Empresa\n";
                }

                if (sucurs == "") {
                    msj += "Por favor, Seleccione la Sucursal\n";
                }

                if (punt_emision == "") {
                    msj += "Por favor, Seleccione el Punto de Emision\n";
                }


                //Paciente
                if (cedula == "") {
                    msj += "Por favor,Ingrese la cedula del paciente\n";
                }
                if (nombre_paciente == "") {
                    msj += "Por favor,Ingrese el nombre del paciente\n";
                }
                if (seguro_paciente == "") {
                    msj += "Por favor,Seleccione el seguro paciente\n";
                }
                if (proced == "") {
                    msj += "Por favor,Ingrese los Procedimientos del paciente\n";
                }
                if (fech_proced == "") {
                    msj += "Por favor,Seleccione la fecha de procedimientos\n";
                }

                //Cliente
                if (cliente == "") {
                    msj += "Por favor,Selecione el cliente\n";
                }
                if (ruc_ced_cliente == "") {
                    msj += "Por favor,Ingrese el ruc del cliente\n";
                }
                if (direccion == "") {
                    msj += "Por favor,Ingrese la direccion del cliente\n";
                }
                if (ciud_cliente == "") {
                    msj += "Por favor,Ingrese la ciudad del cliente\n";
                }
                if (email_cliente == "") {
                    msj += "Por favor,Ingrese el email del cliente\n";
                }
                if (telf_cliente == "") {
                    msj += "Por favor,Ingrese el telefono del cliente\n";
                }

                //Vendedor/Recaudador
                if (recaud == "") {
                    msj += "Por favor,Selecione el recaudador\n";
                }
                if (ced_recaudador == "") {
                    msj += "Por favor,Ingrese la cedula del recaudador\n";
                }

                //Detalle Asiento
                if (det_asiento == "") {
                    msj += "Por favor,Ingrese el detalle del asiento\n";
                }

                if (msj == "") {

                    if (valor_total > 0) {
                        $.ajax({
                            type: 'post',
                            url: "{{route('ventas_store')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('input[name=_token]').val()
                            },
                            datatype: 'json',
                            data: $("#crear_factura").serialize(),
                            success: function(data) {
                                //console.log(data);

                                swal({
                                        title: "Guardado correctamente",
                                        buttons: true,
                                    })
                                    .then((value) => {
                                        location.href = "{{route('venta_index')}}";
                                    });

                            },
                            error: function(data) {
                                console.log(data);
                            }
                        })
                    } else {

                        swal({
                            title: "Calcular el total a pagar",
                            buttons: true,

                        });

                    }
                } else {
                    alert(msj);
                }

            }


            //Completa 2 Decimales a la izquierda
            function completa_decimales(elemento, id, nDec) {

                var cod = $('#codigo' + id).val();
                var nomb = $('#nombre' + id).val();
                var num = elemento.value;

                var n = parseFloat(elemento.value);
                var s;
                //n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                //s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);


                if ((cod.length == 0) && (nomb.length == 0)) {
                    alert("Debe ingresar el código, nombre del producto");
                    $('#cantidad' + id).val("0.00");
                    $('#desc' + id).val("0.00");
                    $('#extendido' + id).val("0.00");
                    return false;
                }


                if (num.length == 0) {

                    alert("Cantidad no permitida");
                    $('#cantidad' + id).val("0");
                    $('#desc' + id).val("0");
                    $('#extendido' + id).val("0");

                    return false;
                }

                $('#cantidad' + id).val(s);

            }

            //Completa 2 decimales a la izquierda y redondea el valor a dos decimales
            function redondea_precio(elemento, id, nDec) {

                var n = parseFloat(elemento.value);
                var s;

                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);

                $('#precio' + id).val(s);


            }

            //Redondea descuento a 2 decimales
            function redondea_descuento(elemento, id, nDec) {

                var n = parseFloat(elemento.value);
                var s;

                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);

                $('#desc_porcentaje' + id).val(s);


            }

            //Valida el valor Ingresado en el Campo Cantidad
            function validarcantidad(elemento, id) {

                var cod = $('#codigo' + id).val();
                var nomb = $('#nombre' + id).val()

                if ((cod.length == 0) && (nomb.length == 0)) {
                    alert("Debe ingresar el código, nombre del producto");
                    $('#cantidad' + id).val("0");
                    $('#desc' + id).val("0");
                    $('#extendido' + id).val("0");
                    return false;
                }

                var num = elemento.value;
                if (num.length == 0) {

                    alert("Cantidad no permitida");
                    $('#cantidad' + id).val("0");
                    $('#desc' + id).val("0");
                    $('#extendido' + id).val("0");

                    return false;
                }

                var st = $('#stock' + id).val();
                var nu = parseInt(elemento.value, 10);

                if (nu > st) {
                    alert("Cantidad no debe ser mayor al stock");
                    $('#cantidad' + id).val("0");
                    $('#total_acum' + id).val('');
                }


                var numero = parseInt(elemento.value, 10);
                //Validamos que se cumpla el rango
                if (numero < 1 || numero > 999999999) {
                    alert("Cantidad no permitida");
                    $('#cantidad' + id).val("0");
                    return false;
                }

                $('#total_acum' + id).val(numero);

                return true;
            }

            //No usada por el momento
            function validartotal(elemento, id) {
                var numero = parseInt(elemento.value, 10);
                //Validamos que se cumpla el rango
                if (numero < 1 || numero > 999999999) {
                    alert("Total no permitido");
                    $('#total' + id).val("");
                    return false;
                }
                return true;
            }


            //Calcula el Total
            function total_calculo(id) {

                total = 0;
                descuento_total = 0;

                cantidad = parseFloat($('#cantidad' + id).val());
                precio = parseFloat($('#precio' + id).val());
                descuento = parseFloat($('#desc_porcentaje' + id).val());

                total = cantidad * precio;

                descuento_total = (total * descuento) / 100;

                $('#desc' + id).val(descuento_total.toFixed(2));
                $('#extendido' + id).val(total.toFixed(2));

                $('#desc1' + id).val(descuento_total.toFixed(2));
                $('#extendido1' + id).val(total.toFixed(2));

                suma_totales();

            }

            function suma_totales() {

                contador = 0;
                iva1 = 0;
                iva = 0;
                total = 0;
                sub = 0;
                descu1 = 0;
                total_fin = 0;
                descu = 0;
                trans = 0;
                subtotal_0 = 0;
                subtotal_12 = 0;
                base_imponible = 0;
                val_cost_prod = 0;
                cost_prod = 0;
                iva_p = 0;
                vent_tar_12 = 0;
                vent_tar_0 = 0;
                sum1 = 0;
                sum2 = 0;

                $("#crear tr").each(function() {
                    $(this).find('td')[0];
                    visibilidad = $(this).find('#visibilidad' + contador).val();
                    if (visibilidad == 1) {
                        //iva_p = parseInt($(this).find('#iva_obt'+contador).val());
                        cost_prod = parseFloat($(this).find('#cost_prod' + contador).val());
                        cantidad = parseFloat($(this).find('#cantidad' + contador).val());
                        valor = parseFloat($(this).find('#precio' + contador).val());
                        descu = parseFloat($(this).find('#desc1' + contador).val());
                        pre_neto = parseFloat($(this).find('#extendido1' + contador).val());
                        total = cantidad * valor;

                        if ($('#iva' + contador).prop('checked')) {

                            subtotal_12 = subtotal_12 + total;

                            if (total > 0) {

                                val_cost_prod = val_cost_prod + cost_prod;
                            }

                            if (descu > 0) {
                                descu1 = descu1 + descu;
                            }

                        } else {

                            subtotal_0 = subtotal_0 + total;

                            if (total > 0) {
                                val_cost_prod = val_cost_prod + cost_prod;
                            }

                            if (descu > 0) {
                                descu1 = descu1 + descu;
                            }

                        }


                    }

                    contador = contador + 1;

                });

                sum_subt = subtotal_12 + subtotal_0;

                iva = subtotal_12 * '{{$valorIva}}';

                base_imponible = subtotal_12;

                trans = parseFloat($('#transporte').val());

                if (trans > 0) {
                    total_fin = (sum_subt - descu1) + trans + iva;
                } else {
                    total_fin = (sum_subt - descu1) + iva;
                }

                $('#subtotal').val(sum_subt);
                $('#impuesto').val(iva.toFixed(2));
                $('#descuento').val(descu1.toFixed(2));
                $('#total').val(total_fin.toFixed(2));
                $('#base_imponible').val(base_imponible.toFixed(2));
                $('#subtotal').val(subtotal_12.toFixed(2));
                $('#subtotal2').val(subtotal_0.toFixed(2));

                //Campos Oculto
                $('#subtotal1').val(subtotal_12.toFixed(2));
                $('#subtotal_2').val(subtotal_0.toFixed(2));
                $('#impuesto1').val(iva.toFixed(2));
                $('#descuento1').val(descu1.toFixed(2));
                $('#total1').val(total_fin.toFixed(2));
                $('#base_imponible1').val(base_imponible.toFixed(2));
                $('#transporte1').val(trans);
                $('#cost_vent_merc').val(val_cost_prod);
            }

            //Valida Precio
            function validarprecio(elemento, id) {

                var cod = $('#codigo' + id).val();
                var nomb = $('#nombre' + id).val()

                if ((cod.length == 0) && (nomb.length == 0)) {
                    alert("Debe ingresar el código, nombre, cantidad del producto");
                    $('#precio' + id).val("0");
                    $('#desc' + id).val("0");
                    $('#extendido' + id).val("0");
                    return false;
                }

                var prec = elemento.value;
                if (prec.length == 0) {

                    alert("Precio no permitido");
                    $('#precio' + id).val("0");
                    $('#desc' + id).val("0");
                    $('#extendido' + id).val("0");
                    return false;
                }

                var numero = parseInt(elemento.value, 10);
                //Validamos que se cumpla el rango
                if (numero < 1 || numero > 999999999) {
                    alert("Precio no permitido");
                    $('#precio' + id).val("0");
                    return false;
                }
                return true;
            }

            function validardescuento(elemento, id) {

                var desc = elemento.value;
                if (desc.length == 0) {

                    alert("Rango de descuento permitido (0% - 100%)");
                    $('#desc_porcentaje' + id).val("0");
                    $('#desc' + id).val("0");
                    return false;
                }

                var numero = parseInt(elemento.value, 10);
                //Validamos que se cumpla el rango
                if (numero < 0 || numero > 100) {
                    alert("Rango de descuento permitido (0% - 100%)");
                    $('#desc_porcentaje' + id).val("0");
                    $('#desc' + id).val("0");
                    return false;
                }
                return true;
            }

            //Obtengo el Ruc o Cedula del Cliente Seleccionado
            $("#cliente").change(function() {
                $.ajax({
                    type: 'post',
                    url: "{{route('ventas_buscar_identificacion')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#cliente"),
                    success: function(data) {
                        $('#ruc_cedula').val(data.client_identificacion);
                        $('#direccion').removeAttr('disabled');
                        $('#direccion').val(data.client_direccion);
                        $('#identif_cliente').val(data.client_identificacion);
                        $('#telefono').val(data.client_telefono);
                        $('#email').val(data.client_email);
                        $('#ciudad').val(data.client_ciudad);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            });

            //Obtengo datos del vendedor de la Tabla Usuarios
            /*$("#vendedor").change(function(){
            $.ajax({
                type: 'post',
                url:"{{route('vendedor.identificacion')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#vendedor"),
                success: function(data){
                    $('#cedula_vendedor').val(data.vendedor_cedula);
                },
                error: function(data){
                    console.log(data);
                }
            })
            });*/

            $("#vendedor").change(function() {
                $('#cedula_vendedor').val($('option:selected', $(this)).data("id"));
            });
            //Obtengo datos del Recaudador de la Tabla Usuarios
            $("#recaudador").change(function() {
                $.ajax({
                    type: 'post',
                    url: "{{route('recaudador.identificacion')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#recaudador"),
                    success: function(data) {
                        $('#cedula_recaudador').val(data.recaudador_cedula);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            });

            //Actualiza la direccion del Cliente
            $("#direccion").change(function() {
                $.ajax({
                    type: 'post',
                    url: "{{route('update_direccion_client')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    //data: $("#identif_cliente"),
                    data: {
                        'ident_cliente': $("#identif_cliente").val(),
                        'direc_cliente': $("#direccion").val()
                    },
                    success: function(data) {
                        //console.log(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            });

            function verificar_stock(e) {

                //console.log($(e).parent().parent().find('.cneto').val());
                var cantidad = $(e).parent().parent().find('.cneto').val();
                var bodega = $(e).parent().parent().find('.bodega').val();
                var producto = $(e).parent().parent().find('.codigo_producto').val();

                $.ajax({
                    type: 'post',
                    url: "{{route('ventas.inventariable')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        producto: producto,
                    },
                    success: function(data) {
                        if (data >= 1 && bodega != "" && cantidad > 0) {
                            $.ajax({
                                type: 'post',
                                url: "{{route('ventas.stock')}}",
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                                },
                                datatype: 'json',
                                data: {
                                    producto: producto,
                                    bodega: bodega,
                                },
                                success: function(data) {
                                    //console.log('data stock',data.length);
                                    if (data.length > 0) {
                                        //console.log(parseInt(data[0].existencia), parseInt(cantidad));
                                        if (parseInt(data[0].existencia) < parseInt(cantidad)) {
                                            swal("No tiene existencia en dicha bodega");
                                            //$(".btn_add").attr("disabled", true);
                                        } else {
                                            // $(".btn_add").attr("disabled", false);
                                        }
                                    } else {
                                        swal("El producto no existe en dicha bodega");
                                        // $(".btn_add").attr("disabled", true);
                                    }
                                },
                                error: function(data) {
                                    console.log(data.responseText);
                                }
                            })
                        }
                    },
                    error: function(data) {
                        console.log(data.responseText);
                    }
                });
            }


            //Obtengo el Codigo del Producto por el nombre del producto Ingresado
            function cambiar_nombre(id) {

                $.ajax({
                    type: 'post',
                    url: "{{route('ventas_buscar_codigo')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'nombre': $("#nombre" + id).val()
                    },
                    success: function(data) {
                        //console.log(data);
                        $('#codigo' + id).val(data.cod_product);
                        $('#cost_prod' + id).val(data.cost_vent);
                        //$('#iva_obt'+id).val(data.iva_prod);
                        if (data.iva_prod == '1') {
                            $('#iva' + id).prop("checked", true);
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            }


            //Obtengo el Nombre del Producto por el codigo del producto Ingresado
            function cambiar_codigo(id) {
                $.ajax({
                    type: 'post',
                    url: "{{route('ventas_buscar_nombre2')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'codigo': $("#codigo" + id).val()
                    },
                    success: function(data) {
                        $('#nombre' + id).val(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            }


            /* function obtener_num_factura(){
             $.ajax({
                 url:"{{route('numero_factura')}}",
                 type: 'get',
                 datatype: 'json',
                 success: function(data){
                    //console.log(data);
                    $('#nfactura').val(data);
                 },
                 error: function(data){
                     console.log(data);
                 }
             })
            }*/


            function limpiar() {

                //obtenemos la fecha actual
                var now = new Date();
                var day = ("0" + now.getDate()).slice(-2);
                var month = ("0" + (now.getMonth() + 1)).slice(-2);
                var today = now.getFullYear() + "-" + (month) + "-" + (day);
                var fe_proced = new Date('Y-m-d H:i:s');
                $("#fecha").val(today);
                $("#fecha_proced").val(fe_proced);


            }

            //Completa la cedula del Paciente por minimo 3 digitos
            $(".ced_paciente").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('autocomple_paciente_cedula')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 1,
            });


            //Obtengo datos del paciente por la cedula
            function obtener_datos_paciente() {
                $.ajax({
                    type: 'post',
                    url: "{{route('obtener_info_paciente')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    datatype: 'json',
                    data: {
                        'ced_paciente': $("#ced_paciente").val()
                    },
                    success: function(data) {
                        //console.log(data);
                        if (data.texto != '') {
                            $('#nomb_paciente').val(data.texto);
                        }

                        if (data.id_seg != '') {
                            $('#id_seguro').val(data.id_seg);
                        }

                        /*if(data.trim() == 'error'){
                          alert("No existe el Paciente");
                          $('#ced_paciente').val('');
                          $('#nomb_paciente').val('');
                          $('#id_seguro').val('');
                        }*/

                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            }

            function obtener_sucursal() {

                var id_seleccionado = $("#id_empresa").val();

                $.ajax({
                    type: 'post',
                    url: "{{route('sucursal.empresa')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'id_emp': id_seleccionado
                    },
                    success: function(data) {
                        //console.log(data);

                        if (data.value != 'no') {
                            if (id_seleccionado != 0) {
                                $("#sucursal").empty();

                                $.each(data, function(key, registro) {
                                    $("#sucursal").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '</option>');

                                });
                            } else {
                                $("#sucursal").empty();

                            }

                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })



            }

            function obtener_caja() {

                var id_sucursal = $("#sucursal").val();

                $.ajax({
                    type: 'post',
                    url: "{{route('caja.sucursal')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'id_sucur': id_sucursal
                    },
                    success: function(data) {
                        //console.log(data);

                        if (data.value != 'no') {
                            if (id_sucursal != 0) {
                                $("#punto_emision").empty();

                                $.each(data, function(key, registro) {
                                    $("#punto_emision").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                                });
                            } else {
                                $("#punto_emision").empty();

                            }

                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })

            }


            $('#busqueda').click(function(event) {

                id = document.getElementById('contador').value;

                var midiv = document.createElement("tr")
                midiv.setAttribute("id", "dato" + id);


                midiv.innerHTML = '<td><input name="codigo' + id + '" class="codigo" id="codigo' + id + '" style="width: 110px;" onchange="cambiar_codigo(' + id + ')"/><input type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input type="text" class="hidden" id="id_prod' + id + '" name="id_prod' + id + '"><input type="text" class="hidden" name="cost_prod' + id + '" id="cost_prod' + id + '"></td><td><input name="nombre' + id + '" class="nombre" id="nombre' + id + '"  onchange="cambiar_nombre(' + id + ')"></td><td> <input type="text" style="width: 110px;" id="cantidad' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="return completa_decimales(this,' + id + ',2);" name="cantidad' + id + '"></td><td><input type="text" style="width: 110px;" id="precio' + id + '" name="precio' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onchange="return redondea_precio(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc_porcentaje' + id + '" name="desc_porcentaje' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  onchange="return redondea_descuento(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc' + id + '" name="desc' + id + '"  value="0.00" disabled><input type="text" class="hidden" name="desc1' + id + '" id="desc1' + id + '"></td><td><input type="text" name="extendido' + id + '" id="extendido' + id + '" value="0.00"  value="" disabled><input type="text" class="hidden" name="extendido1' + id + '" id="extendido1' + id + '"></td><td><input type="checkbox" id="iva' + id + '" name="iva' + id + '" disabled></td><td><button type="button" onclick="eliminar_registro(' + id + ')" class="btn btn-warning btn-margin">Eliminar</button></td>';
                document.getElementById('crear').appendChild(midiv);
                id = parseInt(id);
                id = id + 1;
                document.getElementById('contador').value = id;

                //Completa el codigo del Producto al Ingresar
                $(".codigo").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "{{route('ventas_completa_codigo')}}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    selectFirst: true,
                    minLength: 1,
                });
                //Completa el nombre  del Producto al Ingresar
                $(".nombre").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "{{route('ventas_buscar_nombre')}}",
                            //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            dataType: "json",
                            //type: 'post',
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    selectFirst: true,
                    minLength: 3,
                });


            });

            //Elimina Registro de la Tabla Productos
            function eliminar_registro(valor) {
                var dato1 = "dato" + valor;
                var nombre2 = 'visibilidad' + valor;
                document.getElementById(dato1).style.display = 'none';
                document.getElementById(nombre2).value = 0;
                suma_totales();
            }

            function Cambio() {
                var seguro = document.getElementById("id_seguro");
                var selected = seguro.options[seguro.selectedIndex].text;
                if (selected != "") {
                    alert("esta cambiando el seguro");
                } else {

                }

                console.log(selected);

            }
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                //CREDIT CARD PREVIEW INPUT
                initCreditCardPreview();
                initCreditCardPreview2();
                //NUMERIC INPUTS
                var cleave = new Cleave('.numeric1', {
                    numeral: true
                });
                var cleave = new Cleave('.numeric2', {
                    numeral: true
                });
                var cleave = new Cleave('.numeric3', {
                    numeral: true
                });
                //CVC NUMBER HELP MODAL
                $('[data-toggle="tooltip"]').tooltip();

            });
        </script>
        <script type="text/javascript">
            $(function() {

                $('#fecha_proced').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
                $('.fecha_pago').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
                $('#fecha').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
                obtener_caja();
            });
        </script>

</section>
@endsection
