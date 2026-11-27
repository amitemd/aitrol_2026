<?php

namespace App\Http\Controllers;

use App\Services\VickyModelo;
use App\Services\VickyReportes;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class VickyController extends Controller
{
    private const MESES = [
        'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6,
        'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'setiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12,
    ];

    public function __invoke(Request $request, VickyReportes $reportes, VickyModelo $modelo): JsonResponse
    {
        $datos = $request->validate([
            'pregunta' => ['required', 'string', 'max:1000'],
            'tema_anterior' => ['nullable', 'string', Rule::in(array_keys(VickyReportes::PERMISOS))],
        ]);
        $pregunta = trim($datos['pregunta']);
        $texto = Str::lower(Str::ascii($pregunta));
        $hoy = CarbonImmutable::now('America/Guayaquil')->startOfDay();
        $fechas = $this->periodo($texto, $hoy);
        $tipo = $this->tema($texto);
        $tercero = null;

        if ($tipo === null && $fechas !== [] && preg_match('/^\s*¿?\s*(?:y\s+)?(?:en |del? |desde |el |la |este |esta |ultimos |ayer|hoy|\d)/', $texto)) {
            $tipo = $datos['tema_anterior'] ?? null;
        }
        if ($tipo === null) {
            $interpretacion = $modelo->interpretar($pregunta, $hoy->toDateString());
            if ($interpretacion === null) {
                return response()->json(['message' => 'El modelo de Vicky no está disponible. Puedes usar los accesos rápidos o preguntar directamente por ventas, compras, saldo bancario o cartera.'], 503);
            }
            $tipo = $interpretacion['tipo'] ?? 'otro';
            $tercero = $interpretacion['tercero'] ?? null;
            if ($fechas === [] && is_string($interpretacion['desde'] ?? null) && is_string($interpretacion['hasta'] ?? null)
                && ($interpretacion['desde'] !== '' || $interpretacion['hasta'] !== '')) {
                $fechas = $this->rango($interpretacion['desde'], $interpretacion['hasta']);
            }
        }

        if ($tipo === 'estrategia') {
            $respuesta = $modelo->estrategia($pregunta);
            if ($respuesta === null) {
                return response()->json(['respuesta' => "El modelo de Vicky está temporalmente fuera de alcance. Puedes empezar con estas acciones:\n\n1. Contacta a clientes que dejaron de comprar y registra por qué se alejaron.\n2. Da seguimiento a las cotizaciones abiertas y mide cuántas terminan en venta.\n3. Prueba una oferta complementaria y mide su efecto en el importe promedio por venta."]);
            }

            return response()->json(['respuesta' => "Sugerencias comerciales\n\n".$respuesta]);
        }
        if (! is_string($tipo) || ! array_key_exists($tipo, VickyReportes::PERMISOS)) {
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

        try {
            return response()->json($reportes->consultar($tipo, $fechas, $tercero) + ['tema' => $tipo]);
        } catch (QueryException) {
            return response()->json(['message' => 'La fuente de lectura del informe no está disponible. Revisa la conexión y sus permisos.'], 503);
        }
    }

    private function tema(string $texto): ?string
    {
        if (preg_match('/\b(?:estrategia[s]?|ideas|consejos|vender mas|aumentar (?:las )?ventas)\b/', $texto)) {
            return 'estrategia';
        }
        if (preg_match('/\b(?:borra|elimina|modifica|actualiza|registra|ejecuta|transfiere|paga|inserta|por producto|por vendedor|por cliente|por provincia|compara|comparativo|vs|versus|saldo historico)\b/', $texto)) {
            return 'otro';
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
            return 'otro';
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
