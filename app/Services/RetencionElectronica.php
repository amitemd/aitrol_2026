<?php

namespace App\Services;

use Aitrol\Comercial\Application\DocumentosComerciales;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;
use stdClass;

final readonly class RetencionElectronica
{
    public const SUSTENTOS = [
        '01' => 'Crédito tributario de IVA',
        '02' => 'Costo o gasto para impuesto a la renta',
        '03' => 'Activo fijo con crédito tributario de IVA',
        '04' => 'Activo fijo como costo o gasto',
        '06' => 'Inventario con crédito tributario de IVA',
        '07' => 'Inventario como costo o gasto',
    ];

    private const IMPUESTOS = [1 => 'renta', 2 => 'iva', 6 => 'otra'];

    public function __construct(
        private EmpresaActiva $empresa,
        private ContextoOperativo $contexto,
        private DocumentosComerciales $documentos,
        private FacturaVenta $facturas,
    ) {}

    public function importar(string $contenido, ?int $idOrigen, ?string $claveConsultada = null): stdClass
    {
        $xml = AutorizacionSri::leerXml($contenido);
        $autorizacion = $xml?->getName() === 'autorizacion' ? $xml : null;
        if ($autorizacion !== null) {
            $this->validar((string) $autorizacion->estado === 'AUTORIZADO', 'El XML contiene una retención sin autorización.');
            $xml = AutorizacionSri::leerXml((string) $autorizacion->comprobante);
        }
        $this->validar($xml?->getName() === 'comprobanteRetencion', 'Carga el XML de un comprobante de retención.');
        $version = (string) $xml['version'];
        $this->validar(in_array($version, ['1.0.0', '2.0.0'], true), 'La versión de la retención debe ser 1.0.0 o 2.0.0.');
        $emisor = $xml->infoTributaria;
        $retencion = $xml->infoCompRetencion;
        $fecha = $this->fecha((string) $retencion->fechaEmision);
        $clave = (string) $emisor->claveAcceso;
        $ruc = (string) $emisor->ruc;
        $serie = (string) $emisor->estab.(string) $emisor->ptoEmi.(string) $emisor->secuencial;
        $prefijo = str_replace('/', '', (string) $retencion->fechaEmision).'07'.$ruc.$emisor->ambiente.$serie;
        $this->validar(preg_match('/\A[0-9]{49}\z/', $clave) === 1 && preg_match('/\A[0-9]{13}\z/', $ruc) === 1
            && preg_match('/\A[0-9]{15}\z/', $serie) === 1 && (string) $emisor->codDoc === '07'
            && in_array((string) $emisor->ambiente, ['1', '2'], true) && (string) $emisor->tipoEmision === '1'
            && str_starts_with($clave, $prefijo) && $clave[47] === '1'
            && (int) $clave[48] === AutorizacionSri::digitoVerificador(substr($clave, 0, 48))
            && ($autorizacion === null || (string) $autorizacion->numeroAutorizacion === $clave)
            && ($claveConsultada === null || $claveConsultada === $clave), 'La clave de acceso, la serie o el emisor del XML son incorrectos.');

        $retenido = (string) $retencion->identificacionSujetoRetenido;
        if ((string) $retencion->tipoIdentificacionSujetoRetenido === '05' && preg_match('/\A[0-9]{10}\z/', $retenido)) {
            $retenido .= '001';
        }
        $empresa = $this->empresaFiscal();
        $this->validar($retenido === $empresa->ruc, 'La retención corresponde a otro contribuyente.');
        if ($version === '2.0.0') {
            $sustentos = $xml->docsSustento->docSustento ?? [];
            $this->validar(count($sustentos) === 1, 'Importa una retención asociada a una sola factura de venta.');
            $fuente = $sustentos[0];
            $lineas = $fuente->retenciones->retencion ?? [];
        } else {
            $lineas = $xml->impuestos->impuesto ?? [];
            $fuente = $lineas[0] ?? null;
        }
        $this->validar($fuente !== null && count($lineas) >= 1 && count($lineas) <= 100, 'El XML debe incluir entre 1 y 100 líneas de retención.');
        $numeroOrigen = self::numeroFactura((string) $fuente->numDocSustento);
        $fechaOrigen = $this->fecha((string) $fuente->fechaEmisionDocSustento);
        $this->validar($numeroOrigen !== null && (string) $fuente->codDocSustento === '01', 'El sustento debe identificar una factura de venta de 15 dígitos.');

        $lineasRegistro = [];
        foreach ($lineas as $linea) {
            if ($version === '1.0.0') {
                $this->validar(self::numeroFactura((string) $linea->numDocSustento) === $numeroOrigen
                    && (string) $linea->codDocSustento === '01'
                    && (string) $linea->fechaEmisionDocSustento === (string) $fuente->fechaEmisionDocSustento,
                    'Todas las líneas deben corresponder a la misma factura de venta.');
            }
            $tipo = self::IMPUESTOS[(string) $linea->codigo] ?? null;
            $codigo = (string) $linea->codigoRetencion;
            $base = $this->importe((string) $linea->baseImponible);
            $tasa = $this->importe((string) $linea->porcentajeRetener);
            $valor = $this->importe((string) $linea->valorRetenido);
            $this->validar($tipo !== null && preg_match('/\A[0-9A-Z]{1,5}\z/', $codigo) === 1 && $base > 0
                && $tasa <= 10000 && $valor === intdiv($base * $tasa + 5000, 10000),
                'El código, la base o el importe de una línea de retención es incorrecto.');
            $lineasRegistro[] = [
                'descripcion' => 'Retención '.strtoupper($tipo).' '.$codigo, 'tipo_retencion' => $tipo, 'codigo_retencion' => $codigo,
                'base_retencion' => Cantidad::decimal($base, 2), 'porcentaje_retencion' => Cantidad::decimal($tasa, 2),
            ];
        }

        return $this->empresa->transaccion(function () use ($idOrigen, $numeroOrigen, $fechaOrigen, $ruc, $clave, $serie, $fecha, $lineasRegistro, $contenido, $claveConsultada, $fuente): stdClass {
            $origen = DB::table('com_documentos as factura')->join('erp_clientes as cliente', fn ($join) => $join
                ->on('cliente.id', '=', 'factura.id_cliente')->on('cliente.id_empresa', '=', 'factura.id_empresa'))
                ->join('personas as persona', 'persona.id', '=', 'cliente.id_persona')
                ->where('factura.id_empresa', $this->empresa->id())->where('factura.id_sucursal', $this->contexto->sucursal()->id)
                ->where('factura.modulo', 'ventas')->where('factura.tipo', 'documentos')->whereNull('factura.anulado_at')
                ->where('factura.numero', $numeroOrigen)->where('factura.fecha', $fechaOrigen)
                ->when($idOrigen !== null, fn ($q) => $q->where('factura.id', $idOrigen))
                ->first(['factura.id', 'factura.id_cliente', 'factura.datos_factura', 'persona.identificacion']);
            $fiscalOrigen = json_decode($origen?->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);
            $identificacion = $fiscalOrigen['cliente']['identificacion'] ?? $origen?->identificacion;
            $autorizacionOrigen = (string) $fuente->numAutDocSustento;
            $this->validar($origen !== null && ($identificacion === $ruc || $identificacion.'001' === $ruc)
                && ($autorizacionOrigen === '' || ! isset($fiscalOrigen['clave_acceso']) || $autorizacionOrigen === $fiscalOrigen['clave_acceso']),
                'El emisor o la factura de sustento de la retención no coincide con una venta vigente de esta sucursal.');
            $numero = self::numeroFactura($serie);
            $duplicado = DB::table('com_documentos')->where('id_empresa', $this->empresa->id())
                ->where('modulo', 'ventas')->where('tipo', 'retenciones')->where('numero', $numero)
                ->where('id_cliente', $origen->id_cliente)->exists();
            $this->validar(! $duplicado, 'Ya existe una retención de este emisor con el mismo número.');

            return $this->documentos->registrar('ventas', 'retenciones', [
                'id_documento_origen' => $origen->id, 'emisor_retencion' => $origen->id_cliente,
                'numero' => $numero, 'fecha' => $fecha, 'lineas' => $lineasRegistro, 'permitir_retencion_cero' => true,
                'datos_compra' => [
                    'clave_acceso' => $clave, 'ambiente' => $clave[23], 'xml_retencion' => $contenido,
                    'estado_retencion' => $claveConsultada === null ? 'XML recibido' : 'Autorizada en el SRI',
                ],
            ], conservarBasesXml: true);
        });
    }

    public function generar(array $datos): stdClass
    {
        return $this->empresa->transaccion(function () use ($datos): stdClass {
            $empresa = $this->empresaFiscal();
            $compra = $this->documentos->consultar('compras', 'documentos', (int) $datos['id_documento_origen']);
            $datos['lineas'] = $this->documentos->aplicarBasesRetencion($datos['lineas'], $compra);
            $proveedor = DB::table('erp_proveedores')->where('id_empresa', $this->empresa->id())
                ->where('id', $compra->id_proveedor)->first(['nombre', 'identificacion', 'tipo_identificacion']);
            $identificacion = match ($proveedor?->tipo_identificacion) {
                'ruc' => preg_match('/\A[0-9]{13}\z/', $proveedor->identificacion) ? '04' : null,
                'cedula' => preg_match('/\A[0-9]{10}\z/', $proveedor->identificacion) ? '05' : null,
                'pasaporte' => preg_match('/\A[A-Za-z0-9]{1,20}\z/', $proveedor->identificacion) ? '06' : null,
                default => null,
            };
            $this->validar($identificacion !== null, 'Completa la identificación fiscal del proveedor.', 'id_documento_origen');
            $this->validar(self::numeroFactura($compra->numero) !== null, 'La factura de compra debe tener un número de comprobante de 15 dígitos.', 'id_documento_origen');
            $fiscal = json_decode($compra->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);
            $identidad = $this->facturas->identidadElectronica($datos['fecha'], $empresa->ruc, retencion: true);
            $datos['numero'] = $identidad['numero'];
            $datos['emisor_retencion'] = 0;
            $datos['permitir_retencion_cero'] = true;
            $datos['lineas'] = array_map(fn (array $fila) => $fila + ['descripcion' => 'Retención '.strtoupper($fila['tipo_retencion']).' '.$fila['codigo_retencion']], $datos['lineas']);
            $datos['datos_compra'] = $identidad + [
                'xml_retencion' => $this->xmlEmitido($datos, $identidad, $compra, $fiscal, $empresa, $proveedor, $identificacion),
                'estado_retencion' => 'Generada · pendiente de firma y envío',
            ];

            return $this->documentos->registrar('compras', 'retenciones', $datos);
        });
    }

    public function emitir(int $id, FirmaSri $firma, AutorizacionSri $sri): string
    {
        $mensaje = Cache::lock('retencion-sri-'.$this->empresa->id().'-'.$id, 90)->get(function () use ($id, $firma, $sri): string {
            $documento = $this->documentos->consultar('compras', 'retenciones', $id);
            abort_if($documento->anulado_at !== null, 409, 'La retención está anulada.');
            $fiscal = json_decode($documento->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);
            $identidad = $sri->identidad($documento->datos_factura);
            $this->validar($identidad !== null && ! empty($fiscal['xml_retencion']), 'Esta retención no tiene un XML electrónico.', 'sri');
            $clave = $identidad['clave'];

            if (! isset($fiscal['xml_firmado'])) {
                $fiscal['xml_firmado'] = $firma->firmar($fiscal['xml_retencion']);
                $fiscal['estado_retencion'] = 'Firmada · pendiente de recepción';
                $this->empresa->transaccion(function () use ($id, $fiscal): void {
                    $actual = DB::table('com_documentos')->where('id_empresa', $this->empresa->id())->where('id', $id)
                        ->lockForUpdate()->first(['anulado_at']);
                    abort_if($actual?->anulado_at !== null, 409, 'La retención está anulada.');
                    $this->guardarEstado($id, $fiscal);
                });
            } elseif (empty($fiscal['recibida_sri'])) {
                $estado = $sri->consultar($id, $clave);
                if (in_array($estado, ['AUTORIZADO', 'EN_PROCESO', 'NO_AUTORIZADO'], true)) {
                    $fiscal['recibida_sri'] = true;
                }
            }
            if (empty($fiscal['recibida_sri'])) {
                $sri->recibir($fiscal['xml_firmado'], $identidad['ambiente']);
                $fiscal['recibida_sri'] = true;
                $fiscal['estado_retencion'] = 'Recibida · pendiente de autorización';
                $this->guardarEstado($id, $fiscal);
            }
            $estado ??= $sri->consultar($id, $clave);
            $fiscal['estado_retencion'] = match ($estado) {
                'AUTORIZADO' => 'Autorizada en el SRI',
                'NO_AUTORIZADO' => 'No autorizada en el SRI',
                default => 'Recibida · pendiente de autorización',
            };
            $this->guardarEstado($id, $fiscal);

            return $fiscal['estado_retencion'];
        });
        abort_if($mensaje === false, 409, 'Ya se está procesando esta retención.');

        return $mensaje;
    }

    private function guardarEstado(int $id, array $fiscal): void
    {
        DB::table('com_documentos')->where('id_empresa', $this->empresa->id())->where('id', $id)
            ->update(['datos_factura' => json_encode($fiscal, JSON_THROW_ON_ERROR), 'updated_at' => now()]);
    }

    private function xmlEmitido(array $datos, array $identidad, stdClass $compra, array $fiscal, stdClass $empresa, stdClass $proveedor, string $tipoId): string
    {
        $sucursal = $this->contexto->sucursal();
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><comprobanteRetencion id="comprobante" version="2.0.0"/>');
        [$estab, $punto, $secuencial] = explode('-', $identidad['numero']);
        $this->agregar($xml->addChild('infoTributaria'), [
            'ambiente' => $identidad['ambiente'], 'tipoEmision' => '1', 'razonSocial' => $empresa->razon_social,
            'ruc' => $empresa->ruc, 'claveAcceso' => $identidad['clave_acceso'], 'codDoc' => '07',
            'estab' => $estab, 'ptoEmi' => $punto, 'secuencial' => $secuencial, 'dirMatriz' => $empresa->direccion,
        ]);
        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $datos['fecha']);
        $this->agregar($xml->addChild('infoCompRetencion'), [
            'fechaEmision' => $fecha->format('d/m/Y'), 'dirEstablecimiento' => $sucursal->direccion,
            'tipoIdentificacionSujetoRetenido' => $tipoId, 'parteRel' => $datos['parte_relacionada'],
            'razonSocialSujetoRetenido' => $proveedor->nombre, 'identificacionSujetoRetenido' => $proveedor->identificacion,
            'periodoFiscal' => $fecha->format('m/Y'),
        ]);
        $sustento = $xml->addChild('docsSustento')->addChild('docSustento');
        $this->agregar($sustento, [
            'codSustento' => $datos['codigo_sustento'], 'codDocSustento' => '01',
            'numDocSustento' => str_replace('-', '', $compra->numero),
            'fechaEmisionDocSustento' => DateTimeImmutable::createFromFormat('!Y-m-d', $compra->fecha)->format('d/m/Y'),
            'numAutDocSustento' => $fiscal['clave_acceso'] ?? null, 'pagoLocExt' => '01',
            'totalSinImpuestos' => Cantidad::decimal((int) $compra->subtotal, 2),
            'importeTotal' => Cantidad::decimal((int) $compra->total, 2),
        ]);
        $impuestos = $sustento->addChild('impuestosDocSustento');
        foreach ($this->impuestosSustento($compra, $fiscal, $datos['iva_cero'] ?? null) as $impuesto) {
            $this->agregar($impuestos->addChild('impuestoDocSustento'), $impuesto);
        }
        $retenciones = $sustento->addChild('retenciones');
        foreach ($datos['lineas'] as $fila) {
            $base = Cantidad::unidades($fila['base_retencion'], 2);
            $tasa = Cantidad::unidades($fila['porcentaje_retencion'], 2);
            $this->agregar($retenciones->addChild('retencion'), [
                'codigo' => (string) array_search($fila['tipo_retencion'], self::IMPUESTOS, true),
                'codigoRetencion' => $fila['codigo_retencion'],
                'baseImponible' => Cantidad::decimal($base, 2), 'porcentajeRetener' => Cantidad::decimal($tasa, 2),
                'valorRetenido' => Cantidad::decimal(intdiv($base * $tasa + 5000, 10000), 2),
            ]);
        }
        $pagos = $fiscal['pagos'] ?? [];
        if ($pagos === []) {
            $pago = $fiscal['forma_pago'] ?? $datos['forma_pago'] ?? null;
            $this->validar(isset(FacturaVenta::PAGOS[$pago]), 'Indica la forma de pago del comprobante de sustento.', 'forma_pago');
            $pagos = [['codigo' => $pago, 'importe' => Cantidad::decimal((int) $compra->total, 2)]];
        }
        $nodoPago = $sustento->addChild('pagos');
        foreach ($pagos as $pago) {
            $this->agregar($nodoPago->addChild('pago'), ['formaPago' => $pago['codigo'], 'total' => $pago['importe']]);
        }

        return $xml->asXML();
    }

    private function impuestosSustento(stdClass $compra, array $fiscal, ?string $ivaCero): array
    {
        $xml = AutorizacionSri::leerXml($fiscal['xml_recibido'] ?? '');
        if ($xml?->getName() === 'autorizacion') {
            $xml = AutorizacionSri::leerXml((string) $xml->comprobante);
        }
        $lineasXml = $xml?->detalles->detalle;
        $impuestos = [];
        foreach ($compra->lineas as $indice => $linea) {
            $codigo = isset($lineasXml[$indice]->impuestos->impuesto->codigoPorcentaje)
                ? (string) $lineasXml[$indice]->impuestos->impuesto->codigoPorcentaje
                : ((int) $linea->tasa === 0 ? $ivaCero : array_search((int) $linea->tasa / 100, FacturaVenta::IVA));
            $this->validar($codigo !== null && $codigo !== false && isset(FacturaVenta::IVA[$codigo]),
                'Indica el tratamiento de IVA 0 % de la compra manual o revisa el código de IVA del sustento.', 'iva_cero');
            $impuestos[$codigo] ??= ['base' => 0, 'impuesto' => 0, 'tasa' => (int) $linea->tasa];
            $impuestos[$codigo]['base'] += (int) $linea->subtotal;
            $impuestos[$codigo]['impuesto'] += (int) $linea->impuesto;
        }
        $resultado = [];
        foreach ($impuestos as $codigo => $impuesto) {
            $resultado[] = [
                'codImpuestoDocSustento' => '2', 'codigoPorcentaje' => (string) $codigo,
                'baseImponible' => Cantidad::decimal($impuesto['base'], 2), 'tarifa' => Cantidad::decimal($impuesto['tasa'], 2),
                'valorImpuesto' => Cantidad::decimal($impuesto['impuesto'], 2),
            ];
        }

        return $resultado;
    }

    private function agregar(SimpleXMLElement $nodo, array $campos): void
    {
        foreach ($campos as $nombre => $valor) {
            if (filled($valor)) {
                $nodo->addChild($nombre, htmlspecialchars($valor, ENT_XML1, 'UTF-8'));
            }
        }
    }

    private function empresaFiscal(): stdClass
    {
        $empresa = DB::table('erp_empresas')->where('id', $this->empresa->id())->first(['ruc', 'razon_social', 'direccion']);
        $this->validar($empresa !== null && preg_match('/\A[0-9]{13}\z/', $empresa->ruc)
            && filled($empresa->razon_social) && filled($empresa->direccion), 'Completa el RUC, la razón social y la dirección de la empresa.');

        return $empresa;
    }

    private static function numeroFactura(string $numero): ?string
    {
        $numero = str_replace('-', '', $numero);

        return preg_match('/\A[0-9]{15}\z/', $numero) ? substr($numero, 0, 3).'-'.substr($numero, 3, 3).'-'.substr($numero, 6) : null;
    }

    private function fecha(string $fecha): string
    {
        $dia = DateTimeImmutable::createFromFormat('!d/m/Y', $fecha);
        $this->validar($dia !== false && $dia->format('d/m/Y') === $fecha, 'La fecha del XML no es válida.');

        return $dia->format('Y-m-d');
    }

    private function importe(string $valor): int
    {
        $this->validar(preg_match('/\A[0-9]{1,9}(?:\.[0-9]{1,2})?\z/', $valor) === 1, 'Un importe de la retención tiene un formato incorrecto.');

        return Cantidad::unidades($valor, 2);
    }

    private function validar(bool $valido, string $mensaje, string $campo = 'xml_retencion'): void
    {
        if (! $valido) {
            throw ValidationException::withMessages([$campo => $mensaje]);
        }
    }
}
