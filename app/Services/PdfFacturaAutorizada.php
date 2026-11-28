<?php

namespace App\Services;

use RuntimeException;

final class PdfFacturaAutorizada
{
    public function generar(string $xml): string
    {
        $autorizacion = AutorizacionSri::leerXml($xml);
        $factura = AutorizacionSri::leerXml((string) $autorizacion?->comprobante);
        if ($factura?->getName() !== 'factura' || (string) $autorizacion->estado !== 'AUTORIZADO') {
            throw new RuntimeException('El comprobante autorizado no se pudo leer.');
        }

        $emisor = $factura->infoTributaria;
        $info = $factura->infoFactura;
        $numero = "$emisor->estab-$emisor->ptoEmi-$emisor->secuencial";
        $lineas = [
            'Emisor: '.$emisor->razonSocial.'  |  RUC: '.$emisor->ruc,
            'Matriz: '.$emisor->dirMatriz,
            'Establecimiento: '.$info->dirEstablecimiento,
            'Autorización: '.$autorizacion->numeroAutorizacion,
            'Fecha de autorización: '.$autorizacion->fechaAutorizacion,
            'Ambiente: '.((string) $emisor->ambiente === '2' ? 'Producción' : 'Pruebas'),
            'Clave de acceso: '.$emisor->claveAcceso,
            '',
            'Fecha de emisión: '.$info->fechaEmision,
            'Cliente: '.$info->razonSocialComprador.'  |  Identificación: '.$info->identificacionComprador,
            '',
            'DETALLE',
        ];
        foreach ($factura->detalles->detalle ?? [] as $detalle) {
            $lineas[] = $detalle->codigoPrincipal.'  '.$detalle->descripcion;
            $lineas[] = "Cantidad: $detalle->cantidad  |  Precio: $detalle->precioUnitario  |  Descuento: $detalle->descuento  |  Subtotal: $detalle->precioTotalSinImpuesto";
        }

        $lineas[] = '';
        $lineas[] = 'Subtotal sin impuestos: '.$info->totalSinImpuestos;
        $lineas[] = 'Descuento: '.$info->totalDescuento;
        foreach ($info->totalConImpuestos->totalImpuesto ?? [] as $impuesto) {
            $lineas[] = "Impuesto $impuesto->codigo/$impuesto->codigoPorcentaje  |  Base: $impuesto->baseImponible  |  Valor: $impuesto->valor";
        }
        $lineas[] = 'Propina: '.$info->propina;
        $lineas[] = "TOTAL: $info->importeTotal  $info->moneda";
        foreach ($info->pagos->pago ?? [] as $pago) {
            $codigo = (string) $pago->formaPago;
            $lineas[] = 'Forma de pago: '.$codigo.' '.(FacturaVenta::PAGOS[$codigo] ?? '').'  |  Importe: '.$pago->total;
        }
        foreach ($factura->infoAdicional->campoAdicional ?? [] as $campo) {
            $lineas[] = $campo['nombre'].': '.$campo;
        }

        return $this->documento('Factura '.$numero, $lineas);
    }

    /** @param list<string> $lineas */
    private function documento(string $titulo, array $lineas): string
    {
        $texto = array_map(function (string $linea): string {
            $normalizada = preg_replace('/\s+/u', ' ', trim($linea));

            return wordwrap(mb_convert_encoding($normalizada, 'Windows-1252', 'UTF-8'), 92, "\n", true);
        }, $lineas);
        $paginas = array_chunk(explode("\n", implode("\n", $texto)), 53);
        $objetos = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>',
        ];
        $referencias = [];

        foreach ($paginas as $indice => $pagina) {
            $idPagina = 5 + $indice * 2;
            $idContenido = $idPagina + 1;
            $referencias[] = "$idPagina 0 R";
            $objetos[$idPagina] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents $idContenido 0 R >>";
            $encabezado = $this->escapar($titulo);
            $pie = ($indice + 1).' / '.count($paginas);
            $contenido = "0.10 0.24 0.40 RG 40 790 m 555 790 l S\n"
                ."BT /F1 18 Tf 40 808 Td ($encabezado) Tj ET\n"
                ."BT /F2 9 Tf 40 770 Td 13 TL\n";
            foreach ($pagina as $linea) {
                $contenido .= '('.$this->escapar($linea).") Tj T*\n";
            }
            $contenido .= "ET\nBT /F1 9 Tf 40 32 Td (Aitrol - $pie) Tj ET\n";
            $objetos[$idContenido] = '<< /Length '.strlen($contenido)." >>\nstream\n".$contenido.'endstream';
        }

        $objetos[2] = '<< /Type /Pages /Count '.count($paginas).' /Kids ['.implode(' ', $referencias).'] >>';
        $pdf = "%PDF-1.4\n";
        $xref = ["0000000000 65535 f \n"];
        foreach ($objetos as $id => $objeto) {
            $xref[] = sprintf("%010d 00000 n \n", strlen($pdf));
            $pdf .= "$id 0 obj\n$objeto\nendobj\n";
        }
        $posicion = strlen($pdf);
        $total = count($xref);

        return $pdf."xref\n0 $total\n".implode('', $xref)."trailer\n<< /Size $total /Root 1 0 R >>\nstartxref\n$posicion\n%%EOF\n";
    }

    private function escapar(string $texto): string
    {
        return strtr($texto, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
    }
}
