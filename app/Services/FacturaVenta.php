<?php

namespace App\Services;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Aitrol\Comercial\Application\DocumentosComerciales;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class FacturaVenta
{
    public const IVA = [0 => 0, 2 => 12, 3 => 14, 4 => 15, 5 => 5, 6 => 0, 7 => 0, 10 => 13];

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

            $lineas = $datos['lineas'];
            $tarifas = $this->tarifas($datos['fecha']);
            foreach ($datos['lineas'] as $indice => &$linea) {
                $codigo = (int) $linea['iva_codigo'];
                if (! isset($tarifas[$codigo])) {
                    throw ValidationException::withMessages(["lineas.$indice.iva_codigo" => 'Selecciona una tarifa de IVA configurada y vigente para la fecha.']);
                }
                $linea['tasa'] = $tarifas[$codigo];
            }
            unset($linea);

            $fiscal = [
                'emisor' => (array) $emisor,
                'cliente' => [
                    'nombre' => $cliente->nombre, 'identificacion' => $identificacion->numero,
                    'tipo_sri' => match ($tipo) {
                        TipoIdentificacion::Ruc => '04',
                        TipoIdentificacion::Cedula => '05',
                        TipoIdentificacion::Pasaporte => '06',
                    },
                    'direccion' => $cliente->direccion, 'email' => $cliente->email,
                ],
                'forma_pago' => $datos['forma_pago'], 'plazo_dias' => (int) ($datos['plazo_dias'] ?? 0),
                'lineas' => array_map(fn (array $linea): array => [
                    'iva_codigo' => $linea['iva_codigo'], 'descuento' => $linea['descuento'] ?? 0,
                ], $lineas),
            ];
            $documento = $this->documentos->registrar('ventas', 'documentos', $datos, $fiscal);
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
                        ...array_intersect_key($datos, array_flip(['id_tercero', 'id_bodega', 'forma_pago', 'plazo_dias', 'observacion'])),
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
        return DB::table('erp_clientes')->where('id_empresa', $this->empresa->id())->where('id', $id)
            ->where('activo', true)->whereNull('deleted_at')->first(['id', 'nombre', 'tipo_identificacion', 'identificacion', 'direccion', 'email']);
    }

    public function tarifas(string $fecha): array
    {
        $tarifas = [0 => 0, 6 => 0, 7 => 0];
        $vigentes = DB::table('erp_parametros_fiscales')->where('id_empresa', $this->empresa->id())
            ->where('concepto', 'iva')->where('unidad', 'porcentaje')->where('vigente_desde', '<=', $fecha)
            ->whereIn('codigo', array_map('strval', array_keys(self::IVA)))->orderByDesc('vigente_desde')->get(['codigo', 'valor']);
        foreach ($vigentes->unique('codigo') as $tarifa) {
            $codigo = (int) $tarifa->codigo;
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
