<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario;
use App\Services\AutorizacionSri;
use App\Services\EmpresaActiva;
use App\Services\FirmaSri;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;
use stdClass;

final readonly class FirmaLocalFactura
{
    public function __construct(
        private EmpresaActiva $empresa,
        private DocumentosComerciales $documentos,
        private AutorizacionSri $autorizacion,
        private FirmaSri $firma,
    ) {}

    public function estado(int $id): array
    {
        $factura = $this->documentos->facturaParaCobro($id);
        $identidad = $this->autorizacion->identidad($factura->datos_factura);
        $firmado = $this->consulta($id)->value('firmado_at');
        $recepcion = $firmado === null ? null : $this->autorizacion->consultas($id)
            ->whereIn('estado', array_keys(AutorizacionSri::RECEPCIONES))->latest('id')->value('estado');
        $sinAutorizar = $recepcion === 'RECEPCION_CONFIRMADA' && $this->autorizacion->consultas($id)
            ->whereIn('estado', ['AUTORIZADO', 'NO_AUTORIZADO'])->latest('id')->value('estado') === 'NO_AUTORIZADO';
        $estado = match (true) {
            $factura->anulado_at !== null => 'anulada',
            $identidad === null => 'sin_datos_fiscales',
            $this->autorizacion->autorizado($id) !== null => 'autorizada',
            $sinAutorizar => 'no_autorizada',
            $recepcion === 'RECEPCION_CONFIRMADA' => 'enviada_pendiente_autorizacion',
            $recepcion === 'RECEPCION_RECHAZADA' => 'rechazada_en_recepcion',
            $recepcion === 'RECEPCION_INCIERTA' => 'recepcion_por_confirmar',
            $firmado !== null => 'firmada_pendiente_envio',
            $identidad['ambiente'] === '1' => 'pendiente_firma',
            default => 'fuera_del_ambiente_de_pruebas',
        };

        return ['id_documento' => $id, 'estado' => $estado, 'firmado_at' => $firmado, 'estado_recepcion' => $recepcion];
    }

    public function firmar(int $id): array
    {
        return $this->empresa->transaccion(function () use ($id): array {
            $factura = $this->documentos->consultar('ventas', 'documentos', $id);
            $estado = $this->estado($id);
            if ($estado['firmado_at'] !== null && $factura->anulado_at === null) {
                return $estado;
            }
            abort_unless($estado['estado'] === 'pendiente_firma', 409, 'La firma local está disponible para facturas vigentes del ambiente de pruebas sin autorización.');
            $xml = $this->firma->firmar($this->xml($factura), 'factura');
            DB::table('com_firmas_facturas')->insert([
                'id_empresa' => $this->empresa->id(), 'id_documento' => $id, 'xml_firmado' => $xml,
                'id_usuario' => auth()->id(), 'firmado_at' => now(),
            ]);

            return $this->estado($id);
        });
    }

    public static function bloqueoEnvio(int $empresa, int $factura): string
    {
        return 'envio-factura-sri-'.$empresa.'-'.$factura;
    }

    public function enviar(int $id): array
    {
        abort_unless(config('comercial.ventas.facturacion_electronica.envio_pruebas'), 409, 'Activa el envío manual de facturas al ambiente de pruebas.');

        return Cache::lock(self::bloqueoEnvio($this->empresa->id(), $id), 90)->get(function () use ($id): array {
            $factura = $this->documentos->consultar('ventas', 'documentos', $id);
            $identidad = $this->autorizacion->identidad($factura->datos_factura);
            abort_unless($factura->anulado_at === null && ($identidad['ambiente'] ?? null) === '1', 409, 'El envío está reservado para facturas vigentes del ambiente de pruebas.');
            $estado = $this->estado($id);
            if ($estado['estado'] === 'autorizada') {
                return $estado;
            }
            $xml = $this->consulta($id)->value('xml_firmado');
            abort_if($xml === null, 409, 'Firma la factura en AITROL antes de enviarla.');
            $contenido = AutorizacionSri::leerXml($xml);
            abort_unless($contenido?->getName() === 'factura' && (string) $contenido->infoTributaria->ambiente === '1'
                && (string) $contenido->infoTributaria->claveAcceso === $identidad['clave'], 409, 'El XML firmado no corresponde a esta factura de pruebas.');
            if ($estado['estado_recepcion'] !== null) {
                $consulta = $this->autorizacion->consultar($id, $identidad['clave']);
                if ($consulta !== 'SIN_RESPUESTA' || $estado['estado_recepcion'] === 'RECEPCION_CONFIRMADA') {
                    return $this->estado($id) + ['resultado_consulta' => $consulta];
                }
            }

            $intento = DB::table('com_sri_consultas')->insertGetId([
                'id_empresa' => $this->empresa->id(), 'id_documento' => $id, 'id_usuario' => auth()->id(),
                'estado' => 'RECEPCION_INCIERTA', 'mensajes' => '["Envío iniciado."]', 'created_at' => now(),
            ]);
            $resultado = $this->autorizacion->transmitirPrueba($xml);
            $this->autorizacion->consultas($id)->where('id', $intento)->update([
                'estado' => $resultado['estado'], 'mensajes' => json_encode($resultado['mensajes'], JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE),
            ]);
            $consulta = $resultado['estado'] === 'RECEPCION_CONFIRMADA' ? $this->autorizacion->consultar($id, $identidad['clave']) : null;

            return $this->estado($id) + ['resultado_consulta' => $consulta];
        }) ?: abort(409, 'Ya hay un envío en curso para esta factura.');
    }

    public function descargar(int $id): string
    {
        $factura = $this->documentos->facturaParaCobro($id);
        abort_if($factura->anulado_at !== null, 409, 'La factura fue anulada.');

        return $this->consulta($id)->value('xml_firmado') ?? abort(404, 'Todavía no hay un XML firmado localmente.');
    }

    private function consulta(int $id): Builder
    {
        return DB::table('com_firmas_facturas')->where('id_empresa', $this->empresa->id())->where('id_documento', $id);
    }

    private function xml(stdClass $factura): string
    {
        $fiscal = json_decode($factura->datos_factura, true, flags: JSON_THROW_ON_ERROR);
        $emisor = $fiscal['emisor'] ?? [];
        $cliente = $fiscal['cliente'] ?? [];
        $serie = explode('-', $factura->numero);
        if (count($serie) !== 3 || empty($emisor['razon_social']) || empty($emisor['ruc']) || empty($emisor['direccion'])
            || empty($cliente['nombre']) || empty($cliente['identificacion']) || empty($cliente['tipo_sri']) || empty($fiscal['forma_pago'])
            || $factura->lineas->isEmpty() || count($fiscal['lineas'] ?? []) !== $factura->lineas->count()) {
            throw ValidationException::withMessages(['firma' => 'La factura no conserva todos los datos fiscales necesarios para generar su XML.']);
        }

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><factura id="comprobante" version="1.1.0"/>');
        $this->campos($xml->addChild('infoTributaria'), [
            'ambiente' => $fiscal['ambiente'], 'tipoEmision' => $fiscal['tipo_emision'], 'razonSocial' => $emisor['razon_social'],
            'ruc' => $emisor['ruc'], 'claveAcceso' => $fiscal['clave_acceso'], 'codDoc' => '01',
            'estab' => $serie[0], 'ptoEmi' => $serie[1], 'secuencial' => $serie[2], 'dirMatriz' => $emisor['direccion'],
        ]);

        $impuestos = [];
        $descuento = 0;
        $detalles = new SimpleXMLElement('<detalles/>');
        foreach ($factura->lineas->values() as $indice => $linea) {
            $datos = $fiscal['lineas'][$indice];
            if (! isset($datos['iva_codigo'])) {
                throw ValidationException::withMessages(['firma' => 'Una línea de la factura no conserva el código fiscal de IVA.']);
            }
            $codigo = (string) $datos['iva_codigo'];
            $rebaja = CantidadInventario::unidades($datos['descuento'] ?? 0, 2);
            $descuento += $rebaja;
            $detalle = $detalles->addChild('detalle');
            $this->campos($detalle, [
                'codigoPrincipal' => $datos['codigo_producto'] ?? null, 'descripcion' => $linea->descripcion,
                'cantidad' => CantidadInventario::decimal($linea->cantidad, 3),
                'precioUnitario' => CantidadInventario::decimal($linea->precio, 4), 'descuento' => $this->moneda($rebaja),
                'precioTotalSinImpuesto' => $this->moneda($linea->subtotal),
            ]);
            $this->campos($detalle->addChild('impuestos')->addChild('impuesto'), [
                'codigo' => '2', 'codigoPorcentaje' => $codigo, 'tarifa' => $this->moneda($linea->tasa),
                'baseImponible' => $this->moneda($linea->subtotal), 'valor' => $this->moneda($linea->impuesto),
            ]);
            $impuestos[$codigo] ??= ['base' => 0, 'valor' => 0];
            $impuestos[$codigo]['base'] += $linea->subtotal;
            $impuestos[$codigo]['valor'] += $linea->impuesto;
        }

        $info = $xml->addChild('infoFactura');
        $this->campos($info, [
            'fechaEmision' => CarbonImmutable::parse($factura->fecha)->format('d/m/Y'),
            'tipoIdentificacionComprador' => $cliente['tipo_sri'], 'razonSocialComprador' => $cliente['nombre'],
            'identificacionComprador' => $cliente['identificacion'], 'direccionComprador' => $cliente['direccion'] ?? null,
            'totalSinImpuestos' => $this->moneda($factura->subtotal), 'totalDescuento' => $this->moneda($descuento),
        ]);
        $totales = $info->addChild('totalConImpuestos');
        foreach ($impuestos as $codigo => $impuesto) {
            $this->campos($totales->addChild('totalImpuesto'), [
                'codigo' => '2', 'codigoPorcentaje' => $codigo, 'baseImponible' => $this->moneda($impuesto['base']),
                'valor' => $this->moneda($impuesto['valor']),
            ]);
        }
        $this->campos($info, [
            'propina' => $this->moneda($factura->propina ?? 0), 'importeTotal' => $this->moneda($factura->total), 'moneda' => 'DOLAR',
        ]);
        $plazo = (int) ($fiscal['plazo_dias'] ?? 0);
        $this->campos($info->addChild('pagos')->addChild('pago'), [
            'formaPago' => $fiscal['forma_pago'], 'total' => $this->moneda($factura->total),
            'plazo' => $plazo > 0 ? $plazo : null, 'unidadTiempo' => $plazo > 0 ? 'dias' : null,
        ]);
        $raiz = dom_import_simplexml($xml);
        $raiz->appendChild($raiz->ownerDocument->importNode(dom_import_simplexml($detalles), true));
        if (! empty($fiscal['informacion_adicional'])) {
            $adicional = $xml->addChild('infoAdicional');
            foreach ($fiscal['informacion_adicional'] as $campo) {
                $elemento = $adicional->addChild('campoAdicional', htmlspecialchars($campo['valor'], ENT_XML1, 'UTF-8'));
                $elemento->addAttribute('nombre', $campo['nombre']);
            }
        }

        return $xml->asXML();
    }

    private function moneda(int $centavos): string
    {
        return CantidadInventario::decimal($centavos, 2);
    }

    private function campos(SimpleXMLElement $nodo, array $campos): void
    {
        foreach ($campos as $nombre => $valor) {
            if ($valor !== null && $valor !== '') {
                $nodo->addChild($nombre, htmlspecialchars((string) $valor, ENT_XML1, 'UTF-8'));
            }
        }
    }
}
