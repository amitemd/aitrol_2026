<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title> Ingreso / Egreso Varios </title>
    <style>
        h3 {
            color: black;
            text-align: center;
        }

        table {
            width: 100%;
            border: 1px solid;
            size: 8px;
        }

        th,
        td {
            padding: 3px;
            text-align: left;
        }

        thead {}

        caption {
            padding: 0.3em;
            color: #fff;
            background: #000;
        }

        div {
            text-align: left;
        }

        p.thick {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table id="factura_head" class="bordes_padding">
        <tr>
            <td class="info_empresa">
                <div style="text-align: center">
                    @if(!is_null($empresa->logo))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:150px;height: 80px">
                    @endif

                </div>
                <div style="text-align: center; font-size:0.8em">
                    {{$empresa->id}}<br />
                    {{$empresa->nombrecomercial}}<br />
                    @if(!is_null($empresa->direccion)){{$empresa->direccion}} @endif<br />
                    <br />
                </div>
            </td>
        </tr>
    </table>
    <table id="factura_head" class="bordes_padding" style="font-size:0.8em">
        <tr>
            <td>
                <label for="bodega_recibe"><b>Bodega:</b>@if(isset($pedido->bodega)) {{$pedido->bodega->nombre}} @endif</label>
            </td>
            <td>
                <label for="tipo"><b>Tipo:</b>@if(!is_null($cab_movimiento)) {{$documento_bodega}} @endif</label>
            </td>
            <td>
                <label for="id_proveedor"><b>Proveedor: </b>@if(isset($pedido->proveedor)) {{$pedido->proveedor->nombrecomercial}} @endif</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="observaciones" class="col-md-2 control-label"><b>Observaciones:</b>@if(!is_null($pedido)) {{ $pedido->observaciones }} @endif</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="usuario_crea"><b>Usuario Creación:</b>@if(!is_null($cab_movimiento)) {{ $cab_movimiento->usuariocrea->apellido1 }} {{ $cab_movimiento->usuariocrea->nombre1 }} @endif</label>
            </td>
            <td>
                <label for="fecha_creacion"><b>Fecha Creación:</b>@if(!is_null($cab_movimiento)) {{ $cab_movimiento->created_at }}@endif</label>
            </td>
        </tr>
    </table>

    <div class="col-md-12">
        &nbsp;
    </div>
    <div class="box-body">
        <div class="panel panel-default">
            <div class="box-body">
                <div class="table-responsive col-md-12">
                    <table>
                        <thead style="text-align:center;font-size:0.8em">
                            <tr role="row">
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Serie</th>
                                <th>Bodega</th>
                                <th>Lote</th>
                                <th>Registro Sanitario</th>
                                <th>Fecha de Vecimiento</th>
                                <th style="text-align:center">Descuento %</th>
                                <th>Precio Unitario</th>
                                <th>Precio Final</th>
                            </tr>
                        </thead>
                        <tbody id="crear" style="text-align:center; font-size:0.8em">
                            @php $i=0; @endphp
                            @if(isset($pedido->movimientos))
                            @foreach($pedido->movimientos as $row)
                            <tr style="border: 1px solid">
                                <input type='hidden' id='iva{{$i}}' value='0'>
                                <input type='hidden' id='id_movimiento{{$i}}' name='id_movimiento{{$i}}' value='{{$row->id}}'>
                                <td><input type='hidden' id='visibilidad{{$i}}' name='visibilidad{{$i}}' value='1'> <input name='id{{$i}}' type='hidden' value='{{$row->id_producto}}'> <input name='usos{{$i}}' type='hidden' value='{{$row->usos}}'>{{$row->producto->codigo}}</td>
                                <td>{{$row->producto->nombre}}</td>
                                <td style="text-align:center"><input type='hidden' class='input-number td_der' style='width: 60px;' id='cantidad{{$i}}' name='cantidad{{$i}}' value='{{$row->cantidad}}' readonly>{{$row->cantidad}}</td>
                                <td><input type='hidden' value='{{$row->serie}}' name='serie{{$i}}' id='s{{$i}}'>{{$row->serie}}</td>
                                <td>@if(isset($row->bodega)){{$row->bodega->nombre}}@endif</td>
                                <td>{{$row->lote}}</td>
                                <td>
                                    <div class='form-group'>@if(isset($row->producto)){{$row->producto->registro_sanitario}}@endif</div>
                                </td>
                                <td>

                                    {{$row->fecha_vencimiento}}
                                </td>
                                <td><input type="hidden" name='descuento{{$i}}' id='descuento{{$i}}' class='input-number td_der' style='width: 50%;' value='0.00' readonly='readonly'> 0.00 <input type='hidden' name='descuentof{{$i}}' id='descuentof{{$i}}' value='0.00'> </td>
                                <td>{{number_format($row->precio, 2, '.', '')}}</td>
                                <td id='precio_final{{$i}}'>{{number_format(($row->cantidad * $row->precio) , 2, '.', '')}}</td>

                            </tr>
                            @php $i++; @endphp
                            @endforeach
                            @else
                            @foreach($cab_movimiento->detalles as $row)
                            <tr style="border: 1px solid">
                                <input type='hidden' id='iva{{$i}}' value='0'>
                                <input type='hidden' id='id_movimiento{{$i}}' name='id_movimiento{{$i}}' value='{{$row->id}}'>
                                <td><input type='hidden' id='visibilidad{{$i}}' name='visibilidad{{$i}}' value='1'> <input name='id{{$i}}' type='hidden' value='{{$row->id_producto}}'> <input name='usos{{$i}}' type='hidden' value='{{$row->usos}}'>{{$row->producto->codigo}}</td>
                                <td>{{$row->producto->nombre}}</td>
                                <td style="text-align:center"><input type='hidden' class='input-number td_der' style='width: 60px;' id='cantidad{{$i}}' name='cantidad{{$i}}' value='{{$row->cantidad}}' readonly>{{$row->cantidad}}</td>
                                <td><input type='hidden' value='{{$row->serie}}' name='serie{{$i}}' id='s{{$i}}'>{{$row->serie}}</td>
                                <td>@if(isset($row->bodega)){{$row->bodega->nombre}}@endif</td>
                                <td>{{$row->lote}}</td>
                                <td>
                                    <div class='form-group'>@if(isset($row->producto)){{$row->producto->registro_sanitario}}@endif</div>
                                </td>
                                <td>

                                    {{$row->fecha_vencimiento}}
                                </td>
                                <td><input type="hidden" name='descuento{{$i}}' id='descuento{{$i}}' class='input-number td_der' style='width: 50%;' value='0.00' readonly='readonly'> 0.00 <input type='hidden' name='descuentof{{$i}}' id='descuentof{{$i}}' value='0.00'> </td>
                                <td>{{number_format($row->precio, 2, '.', '')}}</td>
                                <td id='precio_final{{$i}}'>{{number_format(($row->cantidad * $row->precio) , 2, '.', '')}}</td>

                            </tr>
                            @php $i++; @endphp
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot style="font-size:0.8em">
                            @php
                                $conf = \Sis_medico\Ct_Configuraciones::find(3);
                            @endphp
                            <tr>

                                <td colspan="8"><input name='contador' type="hidden" value="{{$i}}" id="contador"></td>
                                <td colspan="2"><b>{{trans('winsumos.subtotal')}} {{$conf->iva*100}}%:</b></td>
                                <td><input type="hidden" name="subtotal_12" id="subtotal_12"> <input class="td_der"  type="hidden" readonly id="subtotal_12_1" value="{{number_format($cab_movimiento->subtotal,2,'.', '')}}">
                                    {{number_format($cab_movimiento->subtotal,2,'.', '')}}
                                </td>

                            </tr>
                            <tr>
                                <td colspan="8"></td>
                                <td colspan="2"><b>Subtotal 0%:</b></td>
                                <td><input type="hidden" name="subtotal_0" id="subtotal_0"> <input class="td_der"  type="hidden" readonly id="subtotal_0_1" value="{{number_format($cab_movimiento->subtotal_0,2,'.', '')}}">
                                    {{number_format($cab_movimiento->subtotal_0,2,'.', '')}}
                                </td>

                            </tr>
                            <tr>
                                <td colspan="8"></td>
                                <td colspan="2"><b>IVA:</b></td>
                                <td><input type="hidden" name="iva" id="iva"> <input class="td_der"  type="hidden" readonly id="iva_1" value="{{number_format($cab_movimiento->iva,2,'.', '')}}">
                                    {{number_format($cab_movimiento->iva,2,'.', '')}}
                                </td>

                            </tr>
                            <tr>
                                <td colspan="8"></td>
                                <td colspan="2"><b>Total:</b></td>
                                <td><input type="hidden" name="total" id="total"> <input class="td_der"  type="hidden" readonly id="total_1" value="{{number_format($cab_movimiento->total,2,'.', '')}}">
                                    {{number_format($cab_movimiento->total,2,'.', '')}}
                                </td>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>