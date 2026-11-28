<?php

namespace App\Services;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use DateTimeImmutable;
use Illuminate\Validation\ValidationException;

final class XmlCompra
{
    public function leer(string $xml, string $rucEmpresa): array
    {
        $documento = AutorizacionSri::leerXml($xml);
        $autorizacion = null;
        if ($documento?->getName() === 'autorizacion') {
            $this->validar(trim((string) $documento->estado) === 'AUTORIZADO', 'El XML contiene un comprobante sin autorización.');
            $autorizacion = trim((string) $documento->numeroAutorizacion);
            $documento = AutorizacionSri::leerXml(trim((string) $documento->comprobante));
        }
        $this->validar($documento?->getName() === 'factura', 'Carga el XML de una factura electrónica.');
        $emisor = $documento->infoTributaria;
        $factura = $documento->infoFactura;
        $clave = trim((string) $emisor->claveAcceso);
        $ruc = trim((string) $emisor->ruc);
        $proveedor = trim((string) $emisor->razonSocial);
        $direccion = trim((string) $emisor->dirMatriz);
        $ambiente = trim((string) $emisor->ambiente);
        $fecha = trim((string) $factura->fechaEmision);
        $fechaFactura = DateTimeImmutable::createFromFormat('!d/m/Y', $fecha);
        $serie = (string) $emisor->estab.(string) $emisor->ptoEmi.(string) $emisor->secuencial;
        $this->validar(preg_match('/\A[0-9]{49}\z/', $clave) === 1
            && preg_match('/\A[0-9]{13}\z/', $ruc) === 1
            && preg_match('/\A[0-9]{15}\z/', $serie) === 1
            && in_array($ambiente, ['1', '2'], true)
            && $fechaFactura && $fechaFactura->format('d/m/Y') === $fecha
            && str_starts_with($clave, $fechaFactura->format('dmY').'01'.$ruc.$ambiente.$serie)
            && ($autorizacion === null || $autorizacion === $clave), 'La fecha, numeración o clave de acceso del XML es incorrecta.');
        $this->validar($proveedor !== '' && mb_strlen($proveedor) <= 255, 'El XML no contiene una razón social válida del proveedor.');
        $comprador = trim((string) $factura->identificacionComprador);
        $tipoComprador = trim((string) $factura->tipoIdentificacionComprador);
        if (in_array($tipoComprador, ['05', ''], true) && preg_match('/\A[0-9]{10}\z/', $comprador) === 1) {
            $comprador .= '001';
        }
        $this->validar($rucEmpresa !== '' && $comprador === $rucEmpresa,
            'El documento del comprador del XML no coincide con la empresa activa. Selecciona la empresa a la que se emitió la factura.');
        $this->validar(in_array(mb_strtoupper(trim((string) $factura->moneda)), ['DOLAR', 'DÓLAR', 'USD'], true)
            && $this->numero((string) ($factura->propina ?? '0'), 2, 1000000) === 0,
            'La carga de compras admite facturas en USD sin propina.');

        $lineas = [];
        $subtotal = $impuesto = 0;
        foreach ($documento->detalles->detalle ?? [] as $detalle) {
            $descripcion = trim((string) $detalle->descripcion);
            $this->validar($descripcion !== '' && mb_strlen($descripcion) <= 255, 'El concepto de cada línea debe tener entre 1 y 255 caracteres.');
            $cantidad = $this->numero((string) $detalle->cantidad, 3, 1000000);
            $precio = $this->numero((string) $detalle->precioUnitario, 4, 100000);
            $descuento = $this->numero((string) ($detalle->descuento ?? '0'), 2, 100000000000);
            $base = intdiv($cantidad * $precio + 50000, 100000) - $descuento;
            $this->validar($cantidad > 0 && $precio > 0 && $base >= 0 && $base + $descuento > 0
                && $base === $this->numero((string) $detalle->precioTotalSinImpuesto, 2, 100000000000), 'Un subtotal de línea del XML no coincide con su cantidad, precio y descuento.');

            $tasa = $valorIva = 0;
            $codigoIva = 0;
            $impuestos = $detalle->impuestos->impuesto ?? [];
            $this->validar(count($impuestos) <= 1, 'La carga de compras admite una tarifa de IVA por línea.');
            foreach ($impuestos as $iva) {
                $this->validar((string) $iva->codigo === '2', 'El XML incluye un impuesto distinto del IVA que la compra no puede registrar.');
                $tasa = $this->numero((string) $iva->tarifa, 2, 100);
                $codigoPorcentaje = trim((string) $iva->codigoPorcentaje);
                $codigoIva = ctype_digit($codigoPorcentaje) && isset(FacturaVenta::IVA[(int) $codigoPorcentaje])
                    && FacturaVenta::IVA[(int) $codigoPorcentaje] * 100 === $tasa
                    ? (int) $codigoPorcentaje : null;
                $valorIva = $this->numero((string) $iva->valor, 2, 100000000000);
                $this->validar($valorIva === intdiv($base * $tasa + 5000, 10000), 'El IVA de una línea no coincide con su base y tarifa.');
            }
            $subtotal += $base;
            $impuesto += $valorIva;
            $lineas[] = [
                'descripcion' => $descripcion, 'cantidad' => Cantidad::decimal($cantidad, 3), 'precio' => Cantidad::decimal($precio, 4),
                'descuento' => Cantidad::decimal($descuento, 2), 'tasa' => Cantidad::decimal($tasa, 2),
                'codigo_xml' => trim((string) $detalle->codigoPrincipal) ?: trim((string) $detalle->codigoAuxiliar),
                'iva_codigo_xml' => $codigoIva,
            ];
        }
        $total = $subtotal + $impuesto;
        $this->validar(count($lineas) >= 1 && count($lineas) <= 100, 'La factura debe tener entre 1 y 100 líneas.');
        $this->validar($subtotal === $this->numero((string) $factura->totalSinImpuestos, 2, 10000000000000)
            && $total === $this->numero((string) $factura->importeTotal, 2, 10000000000000),
            'Los totales de la factura no coinciden con el detalle de sus líneas.');

        $pagos = [];
        $totalPagos = 0;
        $formasPago = $factura->pagos->pago ?? [];
        $this->validar(count($formasPago) <= 20, 'La factura supera el número de formas de pago admitidas.');
        foreach ($formasPago as $pago) {
            $codigo = trim((string) $pago->formaPago);
            $this->validar(isset(FacturaVenta::PAGOS[$codigo]), 'La factura contiene un código de pago desconocido.');
            $importe = $this->numero((string) $pago->total, 2, 10000000000000);
            $totalPagos += $importe;
            $pagos[] = ['codigo' => $codigo, 'importe' => Cantidad::decimal($importe, 2),
                'plazo' => (string) $pago->plazo, 'unidad_tiempo' => (string) $pago->unidadTiempo];
        }
        $this->validar($pagos === [] || $totalPagos === $total, 'Los importes de las formas de pago no coinciden con el total de la factura.');

        return [
            'numero' => substr($serie, 0, 3).'-'.substr($serie, 3, 3).'-'.substr($serie, 6),
            'fecha' => $fechaFactura->format('Y-m-d'), 'ruc_emisor' => $ruc,
            'proveedor' => $proveedor, 'direccion_proveedor' => $direccion !== '' ? mb_substr($direccion, 0, 255) : null,
            'lineas' => $lineas,
            'fiscal' => ['clave_acceso' => $clave, 'ambiente' => $ambiente, 'pagos' => $pagos, 'xml_recibido' => $xml],
        ];
    }

    private function numero(string $texto, int $escala, int $maximo): int
    {
        $valor = trim($texto);
        $this->validar(preg_match('/\A[0-9]{1,14}(?:\.[0-9]{1,9})?\z/', $valor) === 1,
            'El XML contiene una cantidad, precio o importe inválido.');
        [$entero, $fraccion] = array_pad(explode('.', $valor, 2), 2, '');
        $fraccion = rtrim($fraccion, '0');
        $this->validar(strlen($fraccion) <= $escala && (int) $entero <= $maximo,
            'El XML supera la precisión o el importe admitido por el formulario de compras.');
        $unidades = Cantidad::unidades($entero.'.'.$fraccion, $escala);
        $this->validar($unidades <= $maximo * (10 ** $escala), 'El XML supera el importe admitido por el formulario de compras.');

        return $unidades;
    }

    private function validar(bool $valido, string $mensaje): void
    {
        if (! $valido) {
            throw ValidationException::withMessages(['xml_compra' => $mensaje]);
        }
    }
}
