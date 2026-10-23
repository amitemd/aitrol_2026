<script src="{{url('v3/')}}/plugins/jquery/jquery.min.js"></script>
<form action="{{url('/')}}/compras/deudas_pagos" method="post">
    {{ csrf_field() }}
    <table>
        <tr>
            <td>Año:</td>
            <td>
                <select id="anio" name="anio">
                    <option value="">--Seleccionar--</option>
                    <?php
                    for ($anio = date('Y'); 2010 <= $anio; $anio--) {
                        echo "<option value='$anio'>$anio</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr>
            </td>
        </tr>
        <tr>
            <td>Mes:</td>
            <td>
                <select id="mes" name="mes">
                    <option value="">--Seleccionar--</option>
                    <?php
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    for ($i = 0; $i < count($meses); $i++) {
                        $m = $i + 1;
                        echo "<option value='$m'>$meses[$i]</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr>
            </td>
        </tr>
        <tr>
            <td>Día:</td>
            <td>
                <select id="dia" name="dia">
                    <option value="">--Seleccionar--</option>
                    <?php
                    for ($j = 1; $j < 32; $j++) {
                        echo "<option value='$j'>$j</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr>
            </td>
        </tr>
        <tr>
            <td><input type="submit" value="Buscar" /></td>
        </tr>
    </table>
</form>
<table style="width: 100%;font-size:12px;">
    <tr>
        <td style="width: 70%;">
            <table>
                <tr>
                    <th>ID compra</th>
                    <th>Fecha compra</th>
                    <th>Documento</th>
                    <th>Fecha documento</th>
                    <th>Proveedor</th>
                    <th>Nombre comercial</th>
                    <th>Id asiento</th>
                    <th># comprobante</th>
                    <th>Subtotal 0</th>
                    <th>Subtotal 12</th>
                    <th>Descuento</th>
                    <th>Subtotal</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                    <th>Valor contable</th>
                    <th>Saldo</th>
                </tr>
                <?php
                if (isset($datos) && count($datos) > 0) {
                    foreach ($datos as $key => $value) {
                        echo '<tr id="tr_' . $value->id_asiento_cabecera . '">';
                        echo '<td>' . $value->id_compra . '</td>';
                        echo '<td>' . $value->fecha . '</td>';
                        echo '<td>' . $value->tipo . '</td>';
                        echo '<td>' . $value->fecha_comprobante . '</td>';
                        echo '<td>' . $value->proveedor . '</td>';
                        echo '<td>' . $value->nombrecomercial . '</td>';
                        echo '<td><a href="javascript:;" onclick="verAsiento(\'' . $value->id_asiento_cabecera . '\')">' . $value->id_asiento_cabecera . '</a></td>';
                        echo '<td style="text-align:right;">' . $value->numero . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->subtotal0, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->subtotal12, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->descuento, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->subtotal, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->iva, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->total, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->valor_contable, 2) . '</td>';
                        echo '<td style="text-align:right;">' . number_format($value->saldo_por_compra, 2) . '</td>';
                    }
                }
                ?>
            </table>
        </td>
        <td>
            <table style="width: 30%;" id="cuerpo">

            </table>
        </td>
    </tr>
</table>
<script>
    function getColor() {
        var simbolos, color;
        simbolos = "0123456789ABCDEF";
        color = "#";
        for (var i = 0; i < 6; i++) {
            color = color + simbolos[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    function verAsiento(id_asiento) {
        $.ajax({
            url: "{{url('/')}}/compras/ver_asiento",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                id: id_asiento
            },
            dataType: "html",
            success: function(html) {
                $('#tr_' + id_asiento).css("background-color", getColor());
                $("#cuerpo").html(html);
            }
        });
    }
</script>