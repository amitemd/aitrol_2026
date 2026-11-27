<?php

namespace App\Http\Controllers;

use App\Services\EmpresaActiva;
use App\Services\VickyConsultaOperativa;
use App\Services\VickyCore;
use App\Services\VickyModelo;
use App\Services\VickyReportes;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

final class VickyController extends Controller
{
    private const MESES = [
        'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6,
        'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'setiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12,
    ];

    public function __invoke(
        Request $request,
        VickyReportes $reportes,
        VickyModelo $modelo,
        VickyConsultaOperativa $operativa,
        VickyCore $core,
        EmpresaActiva $empresaActiva,
    ): JsonResponse {
        $datos = $request->validate([
            'pregunta' => ['required', 'string', 'max:1000'],
            'tema_anterior' => ['nullable', 'string', Rule::in(array_keys(VickyReportes::PERMISOS))],
        ]);
        $empresa = $empresaActiva->id();
        $inicio = hrtime(true);
        $estado = 'rejected';
        $ambito = null;

        try {
            return $this->responder($datos, $reportes, $modelo, $operativa, $core, $empresa, $estado, $ambito);
        } catch (AuthorizationException $error) {
            $estado = 'rejected';

            throw $error;
        } catch (Throwable $error) {
            $estado = 'failed';

            throw $error;
        } finally {
            $core->registrar($empresa, $estado, (int) ((hrtime(true) - $inicio) / 1_000_000), $ambito);
        }
    }

    /**
     * @param  array{pregunta: string, tema_anterior?: string|null}  $datos
     * @param  array{workflow: string, operation: string}|null  $ambito
     */
    private function responder(
        array $datos,
        VickyReportes $reportes,
        VickyModelo $modelo,
        VickyConsultaOperativa $operativa,
        VickyCore $core,
        int $empresa,
        string &$estado,
        ?array &$ambito,
    ): JsonResponse {
        $pregunta = trim($datos['pregunta']);
        $texto = Str::lower(Str::ascii($pregunta));
        if (preg_match('/\b(?:users|usuarios?|contrasenas?|passwords?|tokens? de acceso|sesiones? activas|tabla personas)\b/', $texto)) {
            return response()->json(['message' => 'La información de usuarios y acceso está fuera de los reportes de Vicky.'], 403);
        }
        $guia = $core->guiaSolicitada($texto);
        if ($guia !== null && Gate::allows('permiso', [$guia['permiso'], 'leer'])) {
            $respuesta = $core->consultarGuia($empresa, $guia['workflow']);
            if ($respuesta !== null) {
                $estado = 'completed';

                return response()->json(['respuesta' => $respuesta]);
            }
        }
        $hoy = CarbonImmutable::now('America/Guayaquil')->startOfDay();
        $fechas = $this->periodo($texto, $hoy);
        $tipo = $this->tema($texto);
        $tercero = null;
        $plan = $operativa->consultaDirecta($pregunta);
        if ($plan !== null) {
            $tipo = 'operativa';
        }

        if ($tipo === null && $fechas !== [] && preg_match('/^\s*¿?\s*(?:y\s+)?(?:en |del? |desde |el |la |este |esta |ultimos |ayer|hoy|\d)/', $texto)) {
            $tipo = $datos['tema_anterior'] ?? null;
        }
        if ($tipo === null || $tipo === 'desglose') {
            $interpretacion = $modelo->interpretar($pregunta, $hoy->toDateString(), $operativa->catalogoVisible());
            if ($interpretacion === null) {
                $estado = 'failed';

                return response()->json(['message' => 'El modelo de Vicky no está disponible. Puedes usar los accesos rápidos o pedir directamente un listado de clientes, proveedores, productos o existencias.'], 503);
            }
            $tipo = $interpretacion['tipo'] ?? 'otro';
            $tercero = $interpretacion['tercero'] ?? null;
            $plan = $interpretacion['reporte'] ?? null;
            if ($fechas === [] && is_string($interpretacion['desde'] ?? null) && is_string($interpretacion['hasta'] ?? null)
                && ($interpretacion['desde'] !== '' || $interpretacion['hasta'] !== '')) {
                $fechas = $this->rango($interpretacion['desde'], $interpretacion['hasta']);
            }
        }

        if ($tipo === 'estrategia') {
            $respuesta = $modelo->estrategia($pregunta);
            if ($respuesta === null) {
                $estado = 'failed';

                return response()->json(['respuesta' => "El modelo de Vicky está temporalmente fuera de alcance. Puedes empezar con estas acciones:\n\n1. Contacta a clientes que dejaron de comprar y registra por qué se alejaron.\n2. Da seguimiento a las cotizaciones abiertas y mide cuántas terminan en venta.\n3. Prueba una oferta complementaria y mide su efecto en el importe promedio por venta."]);
            }

            $estado = 'completed';

            return response()->json(['respuesta' => "Sugerencias comerciales\n\n".$respuesta]);
        }
        if ($tipo !== 'operativa' && (! is_string($tipo) || ! array_key_exists($tipo, VickyReportes::PERMISOS))) {
            return response()->json(['respuesta' => 'Prueba con una sola consulta de ventas, compras, saldo bancario actual, cuentas por cobrar o cuentas por pagar. También puedes pedirme ideas para vender más.']);
        }
        if ($fechas === null) {
            return response()->json(['respuesta' => 'Indica un rango de fechas válido. Ejemplo: del 2026-01-01 al 2026-01-31.']);
        }
        if ($tipo === 'banco' && $fechas !== [] && $fechas !== $this->fechas($hoy, $hoy)) {
            return response()->json(['respuesta' => 'Puedo consultar el saldo bancario actual en libros. El saldo histórico queda fuera de este informe.']);
        }
        if (in_array($tipo, ['cobrar', 'pagar'], true) && preg_match('/\bvencid(?:[oa]s?)\b/', $texto)) {
            $ayer = $hoy->subDay()->toDateString();
            $fechas = ['desde' => $fechas['desde'] ?? '0001-01-01', 'hasta' => min($fechas['hasta'] ?? $ayer, $ayer)];
        }
        if ($fechas === [] && in_array($tipo, ['ventas', 'compras', 'cobrar'], true)) {
            $fechas = $this->fechas($hoy->startOfMonth(), $hoy->endOfMonth());
        }
        $tercero = $this->tercero($tipo, $pregunta, $tercero);
        $plan = is_array($plan) ? $plan : [];
        $ambito = $core->ambito($tipo, $plan['fuente'] ?? null, $plan['operacion'] ?? null);

        try {
            $resultado = $tipo === 'operativa'
                ? $operativa->consultar($plan, $fechas)
                : $reportes->consultar($tipo, $fechas, $tercero) + ['tema' => $tipo];
            $estado = $resultado['estado_informe'] ?? 'completed';
            unset($resultado['estado_informe']);

            return response()->json($resultado);
        } catch (QueryException) {
            $estado = 'failed';

            return response()->json(['message' => 'La fuente de lectura del informe no está disponible. Revisa la conexión y sus permisos.'], 503);
        }
    }

    private function tema(string $texto): ?string
    {
        if (preg_match('/\b(?:estrategia[s]?|ideas|consejos|vender mas|aumentar (?:las )?ventas)\b/', $texto)) {
            return 'estrategia';
        }
        if (preg_match('/\b(?:borra|elimina|modifica|actualiza|registra|ejecuta|transfiere|paga|inserta|saldo historico)\b/', $texto)) {
            return 'otro';
        }
        if (preg_match('/\b(?:por producto|por vendedor|por cliente|por proveedor|por provincia|por categoria|por bodega|compara|comparativo|vs|versus|top|ranking|lista|listado|listar|detalle)\b/', $texto)) {
            return 'desglose';
        }

        $patrones = [
            'cobrar' => '/\b(?:cobrar|(?:cuanto|que) cobro|me deben|cartera (?:de clientes|vencida))\b/',
            'pagar' => '/\b(?:por pagar|pagos vencidos|(?:debo|deuda|adeudo|debe)\b.*\bproveedor(?:es)?|(?:cuanto|que) (?:le )?debo(?=\s+a(?:l)?\b|\s*[?!.]?\s*$))\b/',
            'banco' => '/\b(?:bancari[oa]s?|bancos?)\b/',
            'compras' => '/\b(?:compras?|compre)\b/',
            'ventas' => '/\b(?:ventas?|vendi|vendimos|vendido|facture|facturamos)\b/',
        ];
        $temas = array_keys(array_filter($patrones, fn (string $patron): bool => preg_match($patron, $texto) === 1));
        if ($temas === ['ventas'] && preg_match('/\ba(?:l)? cliente\b/', $texto)) {
            return 'desglose';
        }

        return match (count($temas)) {
            0 => null,
            1 => $temas[0],
            default => 'otro',
        };
    }

    private function periodo(string $texto, CarbonImmutable $hoy): ?array
    {
        if (preg_match('/\bultimos (\d{1,3}) dias\b/', $texto, $dias)) {
            $cantidad = (int) $dias[1];

            return $cantidad > 0 ? $this->fechas($hoy->subDays($cantidad - 1), $hoy) : null;
        }
        preg_match_all('/\b\d{4}-\d{2}-\d{2}\b/', $texto, $dias);
        if ($dias[0] !== []) {
            return count($dias[0]) <= 2 ? $this->rango($dias[0][0], $dias[0][1] ?? $dias[0][0]) : null;
        }
        if (preg_match('/\b(\d{4})-(\d{2})\b/', $texto, $mes)) {
            return $this->mes((int) $mes[1], (int) $mes[2]);
        }
        $nombres = implode('|', array_keys(self::MESES));
        $cantidad = preg_match_all('/(?:^|\b(?:en|de|desde|entre|hasta|al|y|ventas|compras)\s+)('.$nombres.')\b(?:\s+(?:de(?:l)?\s+)?(\d{4})\b)?/', $texto, $meses, PREG_SET_ORDER);
        if ($cantidad > 0) {
            return $cantidad === 1 ? $this->mes((int) ($meses[0][2] ?? $hoy->year), self::MESES[$meses[0][1]]) : null;
        }
        if (preg_match('/\b(20\d{2})\b/', $texto, $anio)) {
            return $this->rango($anio[1].'-01-01', $anio[1].'-12-31');
        }
        if (preg_match('/\b(ultima semana|semana pasada)\b/', $texto)) {
            return $this->fechas($hoy->subWeek()->startOfWeek(), $hoy->subWeek()->endOfWeek());
        }
        if (str_contains($texto, 'esta semana')) {
            return $this->fechas($hoy->startOfWeek(), $hoy->endOfWeek());
        }
        if (preg_match('/\b(mes pasado|mes anterior)\b/', $texto)) {
            return $this->fechas($hoy->subMonthNoOverflow()->startOfMonth(), $hoy->subMonthNoOverflow()->endOfMonth());
        }
        if (preg_match('/\b(este mes|del mes|mes actual)\b/', $texto)) {
            return $this->fechas($hoy->startOfMonth(), $hoy->endOfMonth());
        }
        if (preg_match('/\b(ano pasado|ano anterior)\b/', $texto)) {
            return $this->fechas($hoy->subYear()->startOfYear(), $hoy->subYear()->endOfYear());
        }
        if (preg_match('/\b(este ano|del ano|ano actual)\b/', $texto)) {
            return $this->fechas($hoy->startOfYear(), $hoy->endOfYear());
        }
        if (preg_match('/\b(anteayer|ayer|hoy)\b/', $texto, $dia)) {
            $fecha = match ($dia[1]) {
                'anteayer' => $hoy->subDays(2),
                'ayer' => $hoy->subDay(),
                'hoy' => $hoy,
            };

            return $this->fechas($fecha, $fecha);
        }

        return [];
    }

    private function mes(int $anio, int $mes): ?array
    {
        if (! checkdate($mes, 1, $anio)) {
            return null;
        }
        $inicio = CarbonImmutable::create($anio, $mes, 1, 0, 0, 0, 'America/Guayaquil');

        return $this->fechas($inicio, $inicio->endOfMonth());
    }

    private function rango(string $desde, string $hasta): ?array
    {
        foreach ([$desde, $hasta] as $fecha) {
            if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $fecha, $partes)
                || ! checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1])) {
                return null;
            }
        }

        return $desde <= $hasta ? compact('desde', 'hasta') : null;
    }

    private function fechas(CarbonImmutable $desde, CarbonImmutable $hasta): array
    {
        return ['desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()];
    }

    private function tercero(string $tipo, string $pregunta, mixed $interpretado): ?string
    {
        $patron = match ($tipo) {
            'banco' => '/\bbanco\s+(.+?)(?:\s+(?:hoy|actualmente|ahora)\b|[?.!,;]|$)/iu',
            'pagar' => '/\b(?:proveedor\s+|le debo a\s+|debo a\s+)(.+?)(?:\s+(?:este mes|el mes|en \d|desde|entre)\b|[?.!,;]|$)/iu',
            'cobrar' => '/\bcliente\s+(.+?)(?:\s+(?:este mes|el mes|en \d|desde|entre)\b|[?.!,;]|$)/iu',
            default => null,
        };
        $tercero = $patron !== null && preg_match($patron, $pregunta, $captura) ? $captura[1] : $interpretado;

        return is_string($tercero) && trim($tercero) !== '' && mb_strlen(trim($tercero)) <= 160 ? trim($tercero) : null;
    }
}
