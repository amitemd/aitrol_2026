@extends('contable.productos.base')
@section('action-content')
<section class="content">

    <div class="box " style="background-color: white;">
        <div class="box-header color_cab with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h5><b>EDITAR PRODUCTO O SERVICIO</b></h5>
            </div>
            <!--div class="col-md-1" style="text-align: right;">
                <button onclick="limpiar();" class="btn btn-success " >
                    &nbsp;&nbsp;Nuevo
                </button>
            </div-->
            <div class="col-md-1" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <table class="table table-bordered table-hover" width="100%">
            <tr>
                <td>No.</td>
                <td>ID</td>
                <td>Fecha</td>
                <td>No Fact</td>
                <td>Base 0</td>
                <td>Base diferente 0</td>
                <td>Descuento</td>
                <td>IVA</td>
                <td>SubTotal</td>
                <td>Estado</td>
                <td>Detalle</td>
            </tr>
            <?php

            use Sis_medico\Venta_Kardex;

            $html = '';
            $cont = 0;
            if (count($ventas) > 0) {
                $html .= '';
                foreach ($ventas as $venta) {
                    $kardexVenta = Venta_Kardex::where('id_venta', $venta->id)->get();
                    if ($venta->detalles->count() != count($kardexVenta)) {
                        $cont++;
                        $total = $venta->subtotal_0 + $venta->subtotal_12 - $venta->descuento + $venta->impuesto;
                        $estado = 'Activo';
                        if ($venta->estado == 0) {
                            $estado = 'Inactivo';
                        }
                        $html .= '<tr>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $cont . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->id . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->fecha . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->nro_comprobante . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->subtotal_0 . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->subtotal_12 . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->descuento . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $venta->impuesto . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $total . '</a></td>
            <td style="border-bottom: 1px solid gray;"><a href="javascript:;" onclick="verDetalleKardex(' . $venta->id . ')">' . $estado . '</a></td>';

                        if (count($venta->detalles) > 0) {
                            $html .= '<td style="border-bottom: 1px solid gray;">
            <table width="100%">';
                            foreach ($venta->detalles as $detalle) {
                                $html .= '<tr>
                <td style="border-bottom: 1px solid gray;">' . $detalle->id_ct_productos . '</td>
                <td style="border-bottom: 1px solid gray;">' . $detalle->nombre . '</td>
                <td style="border-bottom: 1px solid gray;">' . $detalle->cantidad . '</td>
                <td style="border-bottom: 1px solid gray;">' . $detalle->precio . '</td>';
                                $html .= '</tr>';
                            }
                            $html .= '</table></td>';
                        }
                        $html .= '</tr>';
                    }
                }
                $html .= '';
            }
            echo $html;
            ?>
        </table>
        <div class="modal" tabindex="-1" id="modalDetalle">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="btnCerrar()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="contModal">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="btnCerrar()">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function btnCerrar(id) {
        $('#modalDetalle').toggle('modal')
    }

    function verDetalleKardex(id) {
        $.ajax({
            url: '/contable/productos/detalleKardex',
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                opcion: 'detalleKardex',
                id: id,
            },
            dataType: 'html',
            success: function(data) {
                $('#contModal').html(data);
            }
        });
        $('#modalDetalle').toggle('modal')
    }
</script>
@endsection