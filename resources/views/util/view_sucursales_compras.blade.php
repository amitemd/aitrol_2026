<html>

<head>
    <title>Actualización de sucursales en compras</title>
    <style>
        .th:hover {
            background-color: #DCD9D6;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>

<body>
    <button id="btn_excel" onclick="exportTableToExcel('tableLista', 'sucursales_compras')">Excel</button>
    <table width="100%" cellspacing="0" cellpadding="0" id="tableLista" style="font-size: 10px;">
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 8%;">ID Asiento</th>
                <th style="width: 8%;">Fecha</th>
                <th style="width: 6%;">observaciones</th>
                <th style="width: 8%;">Sucursal</th>
                <th style="width: 8%;">Emision</th>
                <th style="width: 8%;">Numero</th>
                <th style="width: 8%;">Serie</th>
                <th style="width: 8%;">Comprobante</th>
                <th style="width: 8%;">Autorización</th>
                <th style="width: 8%;">Sucursal Asiento</th>
                <th>...</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($compras) && count($compras) > 0) {
                foreach ($compras as $row) {
                    echo '<tr class="th">
                    <td style="border-bottom: 1px solid gray;">' . $row->id . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $row->id_asiento_cabecera . '</td>
                    <td style="border-bottom: 1px solid gray;" align="center">' . $row->fecha . '</td>
                    <td style="border-bottom: 1px solid gray;">' . $row->observacion . '</td>
                    <td style="border-bottom: 1px solid gray;" align="center" id="' . $row->id . '" class="suc"><input type="text" id="sucursalC_' . $row->id . '" name="sucursalC_' . $row->id . '" value="' . $row->sucursal . '"/></td>
                    <td style="border-bottom: 1px solid gray;" align="center" id="' . $row->id . '" class="emi"><input type="text" id="emisionC_' . $row->id . '" name="emisionC_' . $row->id . '" value="' . $row->punto_emision . '"/></td>
                    <td style="border-bottom: 1px solid gray;" align="center">' . $row->numero . '</td>
                    <td style="border-bottom: 1px solid gray;" align="center">' . $row->serie . '</td>
                    <td style="border-bottom: 1px solid gray;" align="center">' . $row->secuencia_factura . '</td>
                    <td style="border-bottom: 1px solid gray;" align="center">' . $row->autorizacion . '</td>
                    <td style="border-bottom: 1px solid gray;" align="center" id="' . $row->id_asiento_cabecera  . '" class="suca"><input type="text" id="sucursalA_' . $row->id_asiento_cabecera . '"  name="sucursalA_' . $row->id_asiento_cabecera . '" value=" ' . $row->sucursal_asiento . '"/></td>
                    <td style="border-bottom: 1px solid gray;" align="center"><button onclick="actualizarSucursal(\'' . $row->id . '\',\'' . $row->id_asiento_cabecera . '\');">Save</button></td>
                    </tr>';
                }
            }
            ?>
        </tbody>
    </table>
    <script>
        function actualizarSucursal(id, ida) {
            $.ajax({
                url: '<?= url('/') ?>/compras/actualizarSucursal',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    opcion: 'actualizarSucursal',
                    id: id,
                    ida: ida,
                    sucursalC: $('#sucursalC_' + id).val(),
                    emisionC: $('#emisionC_' + id).val(),
                    sucursalA: $('#sucursalA_' + ida).val()
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.success)
                        alert('Sucursal actualizada correctamente...');
                }
            });
        }

        function exportTableToExcel(tableID, filename = '') {
            var suc = document.querySelectorAll('.suc');
            var emi = document.querySelectorAll('.emi');
            var suca = document.querySelectorAll('.suca');
            for (var i = 0; i < suc.length; i++) {
                suc[i].innerHTML = $('#sucursalC_' + suc[i].id).val();
            }
            for (var j = 0; j < emi.length; j++) {
                emi[j].innerHTML = $('#emisionC_' + emi[j].id).val();
            }
            for (var k = 0; k < suca.length; k++) {
                suca[k].innerHTML = $('#sucursalA_' + suca[k].id).val();
            }
            /*var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            // Specify file name
            filename = filename ? filename + '.xls' : 'excel_data.xls';
            // Create download link element
            downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            if (navigator.msSaveOrOpenBlob) {
                var blob = new Blob(['ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                // Setting the file name
                downloadLink.download = filename;
                //triggering the function
                downloadLink.click();
            }*/
        }
    </script>
</body>

</html>