<html>

<body>
    <table align="center" width="100%" bgcolor="#e0e0e0" cellpadding="0" cellspacing="0" border="0" style="max-width:650px;">
        <tr>
            <td align="center">
                <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#fff; font-family:Verdana, Geneva, sans-serif;">
                    <tr>
                        <td aling="center">
                            <img src="http://pruebas.aitrol.com/storage/app/facturaelectronica/fondo/fondoHead.png" style="width:100%;" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table align="center" width="80%" border="0" cellpadding="10" cellspacing="0" style="background-color:#fff; font-family:Verdana, Geneva, sans-serif;">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Estimad@: </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td style="font-size: 30px;"><b>{{ $nombre . ' ' . $apellido }}</b></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td style="text-align:justify ;">
                                        Continuando con el compromiso de protección del medio ambiente y matener un servicio de calidad adjuntamos su {{ $tipoDoc }} digital {{ $num }} , correspondiente al mes de <span style="color: #0084DA;"><b>{{ strtoupper($mes) }}</b></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><b>RESUMEN: </b></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="center">
                                        <table style="width:90%;" align="center">
                                            <tr>
                                                <td align="center"><b>No de {{ $tipoDoc }} :</b> {{ $num }}</td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center"><b>Fecha de emisión :</b> {{ $fechaEmision }}</td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <?php
                                            if ($tipoDoc == 'Factura') {
                                            ?>
                                                <tr>
                                                    <td align="center"><b>Valor total a pagar :</b> $ {{ $valorPagar }}</td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="center">Además puede realizar la impresión de su documento, accediendo a nuestro portal <a href="https://mdconsgroup.com/" style="color: #0084DA;">Aquí</a></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <img src="http://pruebas.aitrol.com/storage/app/facturaelectronica/fondo/fondoFooter.png" style="width:100%" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>