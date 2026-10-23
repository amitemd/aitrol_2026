<b>Compras del período:</b>
<div id="contenedor">
    <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
            <div class="table-responsive col-md-12">
                <table id="tbl_compras" class="table table-sm table-bordered table-hover table-striped dataTables_wrapper" role="grid" aria-describedby="tbl_compras_info">
                    <thead>
                        <tr class="well-dark">
                            <th colspan="16">&nbsp;</th>
                            @foreach($retf as $value)
                            <th colspan="3" style="text-align:center">{{ $value->nombre }}</th>
                            @endforeach
                            <th colspan="{{ ($retf->count()+1) }}">&nbsp;</th>
                        </tr>
                        <tr class="well-dark">
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Linea</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sust.</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Prov</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RUC/Cédula</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Com</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estab.</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">P.Emi</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                            <th width="35%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Autorización</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">SubTotal</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base 0</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base 12</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Monto iva</th>
                            @foreach($retf as $value)
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Codigo</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base Imp</th>
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                            @endforeach
                            {{-- <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Codigo</th> 
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base Imp</th> 
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>  --}}
                            {{-- <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RET IVA 30%</th> 
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RET IVA 70%</th> 
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RET IVA 100%</th>  --}}
                            @foreach($reti as $value)
                            <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ $value->nombre }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="tbl_detalle_compras" name="tbl_detalle_compras">
                        @foreach ($compras as $value)
                        <tr class="well">
                            <td width="5%"><input type="checkbox" id="id_compra_{{ $value['id'] }}" class="form-check-input" name="id_compra[]" onchange="actualizar(this);" value="{{ $value['id'] }}" @if(@$value['validado']=="1" ) checked @endif> &nbsp; {{ $value['id'] }}</td>
                            <td width="5%">{{ $value['secuencia_f'] }}</td>
                            <td width="5%">{{ $value['tipo'] }}</td>
                            <td width="10%">{{ isset($value->proveedorf->razonsocial)?$value->proveedorf->razonsocial:'' }}</td>
                            <td width="5%">{{ $value['credito_tributario'] }}</td>
                            <td width="5%">{{ $value['id_tipoproveedor'] }}</td>
                            <td width="10%">{{ $value['proveedor'] }}</td>
                            <td width="5%"><input type="text" size="1" class="form-control" name="tipo_comprobante[{{ $value['id'] }}]" id="tipo_comprobante[{{ $value['id'] }}]" value="{{ $value['tipo_comprobante'] }}"> </td>
                            @php
                            $variable = explode("-",$value['numero']);
                            $est = "";
                            $pto = "";
                            $sec = "";
                            if(count($variable)>2){
                            list($est, $pto, $sec) = explode("-",$value['numero']);
                            }
                            @endphp
                            <td width="10%">{{ $est }}</td>
                            <td width="10%">{{ $pto }}</td>
                            <td width="10%">{{ $sec }}</td>
                            <td width="35%"><input type="text" size="5" class="form-control" name="autorizacion[{{ $value['id'] }}]" id="autorizacion[{{ $value['id'] }}]" value="{{ $value['autorizacion'] }}"> </td>
                            <td width="5%" style="text-align: right">{{ number_format($value['subtotal'],2, '.', '') }}</td>
                            <td width="10%"></td>
                            <td width="10%" style="text-align: right">{{ number_format($value['subtotal'],2, '.', '') }}</td>
                            <td width="10%" style="text-align: right">{{ number_format($value['iva_total'],2, '.', '') }}</td>
                            @foreach($retf as $ret)
                            <td width="10%">@if(isset($retfcompra[$value->id][$ret->id])) {{ $retfcompra[$value->id][$ret->id]['codigo'] }} @endif</td>
                            <td width="10%" style="text-align: right">@if(isset($retfcompra[$value->id][$ret->id])) {{ $retfcompra[$value->id][$ret->id]['base'] }} @endif</td>
                            <td width="10%" style="text-align: right">@if(isset($retfcompra[$value->id][$ret->id])) {{ number_format($retfcompra[$value->id][$ret->id]['valor'],2, '.', '') }} @endif</td>
                            @endforeach
                            @foreach($reti as $ret)
                            <td width="10%" style="text-align: right">@if(isset($retfcompra[$value->id][$ret->id])) {{ number_format($retfcompra[$value->id][$ret->id]['valor'],2, '.', '') }} @endif</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-xs-2">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros {{count($compras)}} </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true,'tbl_detalle_compras')" class="btn btn-default">
                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                    </button>
                </div>
                <div class="col-xs-2">
                    <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false,'tbl_detalle_compras')" class="btn btn-default">
                        <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>