<b>Ventas del período:</b>
<div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
        <div class="table-responsive col-md-12">
            <table id="tbl_ventas" class="table table-sm table-bordered table-condensed dataTable table-striped dataTables_wrapper" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class="well-dark">
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia</th>
                <th width="20%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Clie</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">RUC/Cédula</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Com</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">SubTotal.</th> 
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">B.No Iva</th> 
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base 0%</th> 
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base Iva</th> 
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Monto Iva</th> 
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ret. Iva</th> 
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ret. Fue</th> 
                </tr>
            </thead>
            <tbody id="tbl_detalles" name="tbl_detalles">
                @foreach ($ventas as $value)
                <tr class="well"> 
                <td ><input type="checkbox" id="id_factura_{{ $value['id'] }}" class="form-check-input" name="id_factura[]" onchange="actualizar(this);" value="{{ $value['id'] }}"  @if(@$value['validado']=="1") checked @endif> &nbsp; {{ $value['id'] }}</td>
                <td >{{ $value['tipo'] }}</td>
                <td >{{ $value['nro_comprobante'] }}</td>
                <td >{{ $value->cliente->nombre }} &nbsp;</td>
                <td ></td>
                <td >{{ $value->id_cliente }}</td>
                <td >18</td>
                <td style="text-align: right">{{ number_format(($value['subtotal_0']+$value['subtotal_12']), 2, '.', '')  }}</td>
                <td style="text-align: right">{{ number_format($value['subtotal_0'], 2, '.', '')  }}</td>
                <td style="text-align: right">{{ number_format($value['subtotal_0'], 2, '.', '')  }}</td>
                <td style="text-align: right">{{ number_format($value['subtotal_12'], 2, '.', '')  }}</td>
                <td style="text-align: right">{{ number_format($value['impuesto'], 2, '.', '')  }}</td>
                <td style="text-align: right">@if(isset($retfventa[$value->id]['IVA'])) {{ number_format($retfventa[$value->id]['IVA']['valor'], 2, '.', '') }} @endif</td>
                <td style="text-align: right">@if(isset($retfventa[$value->id]['RENTA'])) {{ number_format($retfventa[$value->id]['RENTA']['valor'], 2, '.', '') }} @endif</td> 
                </tr>
                @endforeach
            </tbody>
            <tfoot>
            </tfoot>
            </table>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-2">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros  {{count($ventas)}} </div>
        </div> 
        </div> 
        <div class="row">
        <div class="col-sm-2">
            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true, 'tbl_detalles')" class="btn btn-default">
                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
            </button>
        </div> 
        <div class="col-xs-2">
            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false, 'tbl_detalles')" class="btn btn-default">
                <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
            </button>
        </div>
        </div>
    </div>
</div>