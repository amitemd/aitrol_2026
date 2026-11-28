<?php

namespace App\Services;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Aitrol\Clientes\Infrastructure\Persistence\ClienteModel;
use Aitrol\Comercial\Application\DocumentosComerciales;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class FacturaVenta
{
    public const IVA = [0 => 0, 2 => 12, 3 => 14, 4 => 15, 5 => 5, 6 => 0, 7 => 0, 10 => 13];

    public static function etiquetaIva(int $codigo): string
    {
        return match ($codigo) {
            6 => 'No objeto de IVA',
            7 => 'Exento de IVA',
            default => self::IVA[$codigo].' %',
        };
    }

    public const PAGOS = [
        '01' => 'Sin utilización del sistema financiero',
        '15' => 'Compensación de deudas',
        '16' => 'Tarjeta de débito',
        '17' => 'Dinero electrónico',
        '18' => 'Tarjeta prepago',
        '19' => 'Tarjeta de crédito',
        '20' => 'Otros con utilización del sistema financiero',
        '21' => 'Endoso de títulos',
    ];

    public const FRECUENCIAS = ['semanal' => 'Semanal', 'mensual' => 'Mensual', 'anual' => 'Anual'];

    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto, private DocumentosComerciales $documentos) {}

    public function registrar(array $datos): stdClass
    {
        return $this->empresa->transaccion(function () use ($datos): stdClass {
            $borrador = isset($datos['borrador']) ? $this->pendientes()->where('p.id', $datos['borrador'])
                ->lockForUpdate()->first(['p.id']) : null;
            if (isset($datos['borrador']) && $borrador === null) {
                throw ValidationException::withMessages(['borrador' => 'La factura programada ya se registró o no está disponible.']);
            }

            $emisor = DB::table('erp_empresas')->where('id', $this->empresa->id())->where('activo', true)
                ->first(['ruc', 'razon_social', 'direccion']);
            if ($emisor === null || ! preg_match('/\A[0-9]{13}\z/', $emisor->ruc) || blank($emisor->razon_social) || blank($emisor->direccion)) {
                throw ValidationException::withMessages(['emisor' => 'Configura una empresa activa con RUC de 13 dígitos, razón social y dirección de matriz.']);
            }

            $cliente = $this->cliente((int) $datos['id_tercero']);
            $tipo = TipoIdentificacion::tryFrom($cliente->tipo_identificacion ?? '');
            try {
                if ($tipo === null || blank($cliente->nombre) || $cliente->identificacion === '9999999999999') {
                    throw new \InvalidArgumentException;
                }
                $identificacion = new IdentificacionFiscal($tipo, $cliente->identificacion);
            } catch (\InvalidArgumentException) {
                throw ValidationException::withMessages(['id_tercero' => 'Elige un cliente activo con cédula de 10 dígitos, RUC de 13 dígitos o pasaporte válido.']);
            }

            $tarifas = $this->tarifas($datos['fecha']);
            $productos = DB::table('inv_productos')->where('id_empresa', $this->empresa->id())->where('activo', true)
                ->whereIn('id', array_filter(array_column($datos['lineas'], 'id_producto')))
                ->get(['id', 'codigo', 'tipo_producto', 'iva_codigo'])->keyBy('id');
            $bodegas = DB::table('inv_bodegas')->where('id_empresa', $this->empresa->id())
                ->where('id_sucursal', $this->contexto->sucursal()->id)->where('activo', true)
                ->whereIn('id', array_filter(array_column($datos['lineas'], 'id_bodega')))->pluck('id')->flip();
            foreach ($datos['lineas'] as $indice => &$linea) {
                $esFisico = false;
                if (isset($linea['id_producto'])) {
                    $producto = $productos->get($linea['id_producto']);
                    if ($producto === null) {
                        throw ValidationException::withMessages(["lineas.$indice.id_producto" => 'Selecciona un producto o servicio activo de esta empresa.']);
                    }
                    if ($producto->iva_codigo === null) {
                        throw ValidationException::withMessages(["lineas.$indice.iva_codigo" => 'Configura el tratamiento de IVA en la ficha de este producto o servicio.']);
                    }
                    $linea['iva_codigo'] = $producto->iva_codigo;
                    $esFisico = $producto->tipo_producto === 'producto';
                    if ($esFisico && ! $bodegas->has($linea['id_bodega'] ?? 0)) {
                        throw ValidationException::withMessages(["lineas.$indice.id_bodega" => 'Selecciona una bodega activa de esta sucursal para el producto.']);
                    }
                }
                if (! $esFisico && isset($linea['id_bodega'])) {
                    throw ValidationException::withMessages(["lineas.$indice.id_bodega" => 'Solo los productos inventariables pueden salir de una bodega.']);
                }
                $codigo = (int) $linea['iva_codigo'];
                if (! isset($tarifas[$codigo])) {
                    $fecha = Carbon::parse($datos['fecha'])->format('d/m/Y');
                    $nombreIva = self::IVA[$codigo] ?? null;
                    $mensaje = $nombreIva === null
                        ? 'El tratamiento de IVA de esta línea no corresponde al catálogo. Revisa la ficha del producto o servicio.'
                        : 'La línea '.($indice + 1)." usa IVA {$nombreIva} % (código {$codigo}). Registra o corrige esa tarifa en Configuración de empresa → Parámetros fiscales con vigencia al {$fecha}.";
                    throw ValidationException::withMessages(["lineas.$indice.iva_codigo" => $mensaje]);
                }
                $linea['tasa'] = $tarifas[$codigo];
            }
            unset($linea);
            $lineas = array_map(fn (array $linea): array => array_diff_key($linea, ['tasa' => true]), $datos['lineas']);

            $fiscal = [
                'emisor' => (array) $emisor,
                'cliente' => [
                    'nombre' => $cliente->nombre, 'identificacion' => $identificacion->numero,
                    'tipo_sri' => match ($tipo) {
                        TipoIdentificacion::Ruc => '04',
                        TipoIdentificacion::Cedula => '05',
                        TipoIdentificacion::Pasaporte => '06',
                    },
                    'direccion' => $cliente->direccion, 'telefono' => $cliente->telefono, 'email' => $cliente->email,
                ],
                'forma_pago' => $datos['forma_pago'], 'plazo_dias' => (int) ($datos['plazo_dias'] ?? 0),
                'informacion_adicional' => $datos['informacion_adicional'] ?? [],
                'lineas' => array_map(fn (array $linea): array => [
                    'iva_codigo' => $linea['iva_codigo'], 'descuento' => $linea['descuento'] ?? 0,
                    'codigo_producto' => $productos->get($linea['id_producto'] ?? 0)?->codigo,
                ], $lineas),
            ];
            $identidad = $this->identidadElectronica($datos['fecha'], $emisor->ruc);
            $datos['numero'] = $identidad['numero'];
            $documento = $this->documentos->registrar('ventas', 'documentos', $datos, $fiscal + $identidad);
            if ($borrador !== null) {
                DB::table('com_facturas_programadas')->where('id', $borrador->id)
                    ->update(['id_documento' => $documento->id, 'updated_at' => now()]);
            }
            if (isset($datos['frecuencia'])) {
                DB::table('com_recurrencias')->insert([
                    'id_empresa' => $this->empresa->id(), 'id_sucursal' => $this->contexto->sucursal()->id,
                    'id_documento' => $documento->id, 'frecuencia' => $datos['frecuencia'],
                    'primera_fecha' => $datos['primera_fecha'], 'proxima_fecha' => $datos['primera_fecha'],
                    'datos' => json_encode([
                        ...array_intersect_key($datos, array_flip(['id_tercero', 'forma_pago', 'plazo_dias', 'observacion', 'aplicar_propina', 'informacion_adicional'])),
                        'lineas' => $lineas,
                    ], JSON_THROW_ON_ERROR),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            return $documento;
        });
    }

    public function cliente(int $id): ?stdClass
    {
        return DB::table('erp_clientes as cliente')->leftJoin('personas as persona', 'persona.id', '=', 'cliente.id_persona')
            ->where('cliente.id_empresa', $this->empresa->id())->where('cliente.id', $id)
            ->where('cliente.activo', true)->whereNull('cliente.deleted_at')
            ->select(['cliente.id', 'cliente.nombre', 'cliente.tipo_identificacion', 'cliente.identificacion'])
            ->selectRaw('case when persona.id is null then cliente.direccion else persona.direccion end as direccion')
            ->selectRaw('case when persona.id is null then cliente.telefono else persona.telefono end as telefono')
            ->selectRaw('case when persona.id is null then cliente.email else persona.email end as email')->first();
    }

    public function actualizarContacto(int $id, array $contacto): void
    {
        $this->empresa->transaccion(function () use ($id, $contacto): void {
            $cliente = ClienteModel::query()->where('activo', true)->lockForUpdate()->findOrFail($id);
            $persona = $cliente->persona()->lockForUpdate()->first();
            $persona?->fill($contacto)->save();
            $cliente->fill($contacto)->save();
        });
    }

    /** @return array{numero: string, clave_acceso: string, ambiente: string, tipo_emision: string} */
    public function identidadElectronica(string $fecha, string $ruc, bool $retencion = false): array
    {
        $sucursal = $this->contexto->sucursal();
        $establecimiento = (string) ($sucursal->establecimiento_sri ?? '');
        $punto = (string) ($sucursal->punto_emision_sri ?? '');
        if (! preg_match('/\A(?!000)[0-9]{3}\z/', $establecimiento) || ! preg_match('/\A(?!000)[0-9]{3}\z/', $punto)) {
            throw ValidationException::withMessages(['emisor' => 'En Configuración → Sucursales, edita la sucursal activa y completa el establecimiento y el punto de emisión SRI con tres dígitos cada uno.']);
        }
        $configuracion = config('comercial.ventas.facturacion_electronica');
        if (! is_array($configuracion)) {
            throw ValidationException::withMessages(['emisor' => 'La configuración de facturación electrónica no está cargada. Pide al administrador que actualice la caché de configuración del servidor.']);
        }
        $ambiente = (string) ($configuracion['ambiente'] ?? '');
        $inicio = filter_var($retencion ? config('comercial.compras.secuencial_retencion', 1) : ($configuracion['secuencial_inicial'] ?? null), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 999999999],
        ]);
        if (! in_array($ambiente, ['1', '2'], true) || $inicio === false) {
            throw ValidationException::withMessages(['emisor' => 'Revisa el ambiente y el secuencial inicial del SRI en la configuración del servidor.']);
        }

        $serie = $establecimiento.'-'.$punto.'-';
        $numeros = DB::table('com_documentos')->where([
            'id_empresa' => $this->empresa->id(), 'modulo' => $retencion ? 'compras' : 'ventas',
            'tipo' => $retencion ? 'retenciones' : 'documentos',
        ])->when(! $retencion, fn (Builder $consulta) => $consulta->where('emisor', 0)
        )->whereBetween('numero', [$serie.'000000001', $serie.'999999999'])->orderByDesc('numero')->cursor();
        $ultimo = $numeros->first(fn (stdClass $fila): bool => str_starts_with($fila->numero, $serie)
            && preg_match('/\A[0-9]{9}\z/', substr($fila->numero, strlen($serie))));
        $siguiente = max($inicio, 1 + (int) substr($ultimo?->numero ?? '', strlen($serie)));
        if ($siguiente > 999999999) {
            throw ValidationException::withMessages(['emisor' => 'Se agotó el secuencial de este punto de emisión. Configura otro punto autorizado.']);
        }

        $secuencial = sprintf('%09d', $siguiente);
        $tipoEmision = '1';
        $base = Carbon::parse($fecha)->format('dmY').($retencion ? '07' : '01').$ruc.$ambiente.$establecimiento.$punto
            .$secuencial.sprintf('%08d', random_int(0, 99999999)).$tipoEmision;
        $digito = AutorizacionSri::digitoVerificador($base);

        return ['numero' => $serie.$secuencial, 'clave_acceso' => $base.$digito, 'ambiente' => $ambiente, 'tipo_emision' => $tipoEmision];
    }

    public function tarifas(string $fecha): array
    {
        $tarifas = [0 => 0, 6 => 0, 7 => 0];
        $vigentes = DB::table('erp_parametros_fiscales')->where('id_empresa', $this->empresa->id())
            ->where('concepto', 'iva')->where('unidad', 'porcentaje')->where('vigente_desde', '<=', $fecha)
            ->orderByDesc('vigente_desde')->orderByDesc('id')->get(['codigo', 'valor', 'activo'])
            ->filter(fn (stdClass $tarifa): bool => ctype_digit($tarifa->codigo) && array_key_exists((int) $tarifa->codigo, self::IVA))
            ->unique(fn (stdClass $tarifa): int => (int) $tarifa->codigo);
        foreach ($vigentes as $tarifa) {
            $codigo = (int) $tarifa->codigo;
            if (! $tarifa->activo) {
                unset($tarifas[$codigo]);

                continue;
            }
            if ((float) $tarifa->valor === (float) self::IVA[$codigo]) {
                $tarifas[$codigo] = self::IVA[$codigo];
            }
        }

        return $tarifas;
    }

    public function pendientes(): Builder
    {
        return DB::table('com_facturas_programadas as p')->join('com_recurrencias as r', 'r.id', '=', 'p.id_recurrencia')
            ->where('r.id_empresa', $this->empresa->id())->where('r.id_sucursal', $this->contexto->sucursal()->id)
            ->whereNull('p.id_documento');
    }

    public function recurrencias(): Builder
    {
        return DB::table('com_recurrencias')->where('com_recurrencias.id_empresa', $this->empresa->id())
            ->where('com_recurrencias.id_sucursal', $this->contexto->sucursal()->id);
    }

    public function generarProgramadas(): int
    {
        $hoy = now('America/Guayaquil')->toDateString();
        $activas = DB::table('com_recurrencias')->where('activa', true)->where('proxima_fecha', '<=', $hoy);
        $cantidad = 0;
        foreach ((clone $activas)->orderBy('proxima_fecha')->orderBy('id')->limit(100)->pluck('id') as $id) {
            $cantidad += DB::transaction(function () use ($activas, $id): int {
                $recurrencia = (clone $activas)->where('id', $id)->lockForUpdate()->first();
                if ($recurrencia === null) {
                    return 0;
                }
                $fecha = Carbon::parse($recurrencia->proxima_fecha);
                $ancla = Carbon::parse($recurrencia->primera_fecha);
                $siguiente = match ($recurrencia->frecuencia) {
                    'semanal' => $fecha->copy()->addWeek(),
                    'mensual' => $fecha->copy()->startOfMonth()->addMonth(),
                    'anual' => $fecha->copy()->startOfMonth()->addYear(),
                };
                if ($recurrencia->frecuencia !== 'semanal') {
                    $siguiente->day(min($ancla->day, $siguiente->daysInMonth));
                }
                DB::table('com_facturas_programadas')->insert([
                    'id_recurrencia' => $id, 'fecha' => $fecha->toDateString(), 'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('com_recurrencias')->where('id', $id)
                    ->update(['proxima_fecha' => $siguiente->toDateString(), 'updated_at' => now()]);

                return 1;
            }, 3);
        }

        return $cantidad;
    }
}
