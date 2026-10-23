<b>Compras del período:</b>
<div class="content" id="contenedor">
    <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
            <div class="table-responsive col-md-12">
                <table id="tbl_compras" class="table-bordered table-hover dataTable table-striped" role="grid"
                    aria-describedby="tbl_compras_info">
                    <thead>
                        <tr class="well-dark">
                            <th colspan="2">Detalles</th>
                            <th colspan="3" style="text-align:center">Entradas</th>
                            <th colspan="3" style="text-align:center">Salidas</th>
                            <th colspan="3" style="text-align:center">Saldos</th>
                        </tr>
                        <tr class="well-dark">
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Fecha</th>
                            <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Producto</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Total</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Total</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                            <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1"
                                aria-label="Codigo: activate to sort column ascending">Total</th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach ($kardex as $value)
                            <tr class="well"> 
                                <td >{{ $value->fecha }}</td>
                                <td >{{ $value->product->nombre}} <br> {{$value->tipo}} {{$value->numero}}</td>
                                @if($value->movimiento==1)
                                <td >{{ $value->cantidad}}</td>
                                <td >{{ $value->valor_unitario}}</td>
                                <td >{{ $value->total }}</td>

                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                @else
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>

                                <td >{{ $value->cantidad}}</td>
                                <td >{{ $value->valor_unitario }}</td>
                                <td >{{ $value->total }}</td>
                                @endif
                                <td >{{ $value->saldo_cantidad}}</td>
                                <td >{{ $value->saldo_valor_unitario }}</td>
                                <td >{{ $value->saldo_total }}</td>
                            </tr>
                       
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $('#tbl_compras').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            "scrollY": 200,
            "scrollX": true,
            'scrollCollapse': true,
        });

        tinymce.init({
            selector: '#hc'
        });


    });
</script>