<?php

namespace App\Http\Controllers;

use App\Services\EmpresaActiva;
use App\Services\VickyConsultaOperativa;
use App\Services\VickyCore;
use App\Services\VickyGuia;
use App\Services\VickyGuiaAitrol;
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
use Illuminate\Validation\ValidationException;
use Throwable;

final class VickyController extends Controller
{
    private const SESION_GUIAS = 'vicky.guias';

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
        VickyGuiaAitrol $guiaAitrol,
    ): JsonResponse {
        $datos = $request->validate([
            'pregunta' => ['required', 'string', 'max:1000'],
            'tema_anterior' => ['nullable', 'string', Rule::in(array_keys(VickyReportes::PERMISOS))],
            'guia_anterior' => ['nullable', 'uuid'],
        ]);
        $empresa = $empresaActiva->id();
        $inicio = hrtime(true);
        $id = (string) Str::uuid();
        $estado = 'rejected';
        $ambito = null;

        try {
            return $this->responder($request, $datos, $reportes, $modelo, $operativa, $core, $guiaAitrol, $empresa, $id, $estado, $ambito);
        } catch (AuthorizationException $error) {
            $estado = 'rejected';

            throw $error;
        } catch (Throwable $error) {
            $estado = 'failed';

            throw $error;
        } finally {
            $core->registrar($id, $empresa, $estado, (int) ((hrtime(true) - $inicio) / 1_000_000), $ambito);
        }
    }

    public function valorar(Request $request, VickyCore $core, EmpresaActiva $empresaActiva): JsonResponse
    {
        $datos = $request->validate([
            'consulta_id' => ['required', 'uuid'],
            'valoracion' => ['required', Rule::in(['helpful', 'needs_review'])],
            'correccion' => ['sometimes', 'required', 'array:step,relation,relatedStep', 'required_array_keys:step,relation'],
        ]);
        $empresa = $empresaActiva->id();
        $recibo = $this->reciboGuia($request, $datos['consulta_id'], $empresa);
        abort_if($recibo === null, 404, 'Esta guía ya no está disponible para valorar.');
        $this->autorizarGuia($recibo);
        $correccion = null;
        if (isset($datos['correccion'])) {
            abort_unless($this->puedeCorregir($request, $recibo['permiso']), 403);
            $pasos = array_keys(VickyGuia::pasos($recibo['workflow']));
            $confirmados = $request->validate([
                'confirmada' => ['accepted'],
                'correccion.step' => ['required', Rule::in($pasos)],
                'correccion.relation' => ['required', Rule::in(['before', 'after', 'requires_approval', 'requires_review'])],
                'correccion.relatedStep' => ['required_if:correccion.relation,before,after', 'prohibited_unless:correccion.relation,before,after', 'different:correccion.step', Rule::in($pasos)],
            ]);
            if (! isset($confirmados['confirmada']) || $datos['valoracion'] !== 'needs_review') {
                throw ValidationException::withMessages(['correccion' => 'Confirma la regla antes de guardar una corrección.']);
            }
            $correccion = $confirmados['correccion'];
        }
        $estado = $core->valorarGuia($empresa, $datos['consulta_id'], $datos['valoracion'], $correccion);
        if ($estado >= 200 && $estado < 300) {
            return response()->json(['guardada' => true]);
        }

        return match ($estado) {
            404 => response()->json(['message' => 'Esta guía ya no está disponible para valorar.'], 404),
            409, 422 => response()->json(['message' => 'La valoración o la regla no se pudo guardar. Revisa los pasos seleccionados.'], 422),
            default => response()->json(['message' => 'Vicky no pudo guardar la valoración. Intenta de nuevo.'], 503),
        };
    }

    /**
     * @param  array{pregunta: string, tema_anterior?: string|null, guia_anterior?: string|null}  $datos
     * @param  array{workflow: string, operation: string}|null  $ambito
     */
    private function responder(
        Request $request,
        array $datos,
        VickyReportes $reportes,
        VickyModelo $modelo,
        VickyConsultaOperativa $operativa,
        VickyCore $core,
        VickyGuiaAitrol $guiaAitrol,
        int $empresa,
        string $id,
        string &$estado,
        ?array &$ambito,
    ): JsonResponse {
        $pregunta = trim($datos['pregunta']);
        $texto = Str::lower(Str::ascii($pregunta));
        if (preg_match('/\b(?:users|contrasenas?|passwords?|tokens? de acceso|sesiones? activas|tabla personas)\b/', $texto)) {
            return response()->json(['message' => 'La información de usuarios y acceso está fuera de los reportes de Vicky.'], 403);
        }
        $social = Str::squish(preg_replace('/[^a-z ]+/', ' ', $texto));
        $saludo = '(?:hola(?: vicky)?|buenos dias|buenas tardes|buenas noches)';
        $ayuda = '(?:(?:en )?que (?:puedes hacer|me puedes ayudar)|como (?:me )?puedes ayudar)';
        if (preg_match('/\A(?:'.$saludo.'(?: '.$ayuda.')?|'.$ayuda.')\z/', $social)) {
            $estado = 'completed';

            return response()->json(['respuesta' => 'Puedo orientarte sobre módulos de Aitrol, darte ejemplos y pasos, preparar listas de verificación y redactar borradores de agenda o actas. También puedo consultar reportes permitidos. ¿Qué necesitas?']);
        }
        if (preg_match('/\A\s*(?:muchas )?gracias(?: vicky| por la ayuda)?\s*\z/', $social)) {
            $estado = 'completed';

            return response()->json(['respuesta' => 'Con gusto. ¿Qué más necesitas revisar en Aitrol?']);
        }
        $anterior = $this->reciboGuia($request, $datos['guia_anterior'] ?? null, $empresa);
        $guia = VickyGuia::detectar($texto, $anterior === null ? null : array_intersect_key($anterior, array_flip(['workflow', 'permiso', 'topic'])));
        if (isset($guia['aclaracion'])) {
            $estado = 'unclassified';

            return response()->json(['respuesta' => $guia['aclaracion']]);
        }
        if ($guia !== null) {
            $this->autorizarGuia($guia);
            $respuesta = $core->consultarGuia($empresa, $guia['workflow'], $guia['goal'], $guia['topic'] ?? null, $guia['relatedTopic'] ?? null);
            if ($respuesta === null) {
                $estado = 'failed';

                return response()->json(['message' => 'El especialista de Vicky no está disponible. Intenta de nuevo.'], 503);
            }
            $estado = 'completed';
            if (isset($respuesta['consulta_id'])) {
                $recibos = $request->session()->get(self::SESION_GUIAS, []);
                $recibos[$respuesta['consulta_id']] = $guia + [
                    'empresa' => $empresa,
                    'usuario' => $request->user()->getAuthIdentifier(),
                    'fecha' => now()->timestamp,
                ];
                $request->session()->put(self::SESION_GUIAS, array_slice($recibos, -20, null, true));
                if ($this->puedeCorregir($request, $guia['permiso'])) {
                    $respuesta['pasos_correccion'] = VickyGuia::pasos($guia['workflow']);
                }
            }

            return response()->json($respuesta);
        }
        if (preg_match('/\busuarios?\b/', $texto)) {
            return response()->json(['message' => 'La información de usuarios y acceso está fuera de los reportes de Vicky.'], 403);
        }
        $destino = $guiaAitrol->responder($pregunta);
        if ($destino !== null) {
            $estado = 'completed';

            return response()->json($destino);
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
        $actividadIniciada = false;
        $iniciarActividad = function (?array $ambito = null) use ($core, $id, $empresa, &$actividadIniciada): void {
            if ($actividadIniciada) {
                return;
            }
            $actividadIniciada = true;
            $core->iniciar($id, $empresa, $ambito);
        };

        if ($tipo === null || $tipo === 'desglose') {
            $iniciarActividad();
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
            $iniciarActividad();
            $respuesta = $modelo->estrategia($pregunta);
            if ($respuesta === null) {
                $estado = 'failed';

                return response()->json(['respuesta' => "El modelo de Vicky está temporalmente fuera de alcance. Puedes empezar con estas acciones:\n\n1. Contacta a clientes que dejaron de comprar y registra por qué se alejaron.\n2. Da seguimiento a las cotizaciones abiertas y mide cuántas terminan en venta.\n3. Prueba una oferta complementaria y mide su efecto en el importe promedio por venta."]);
            }

            $estado = 'completed';

            return response()->json(['respuesta' => "Sugerencias comerciales\n\n".$respuesta]);
        }
        if ($tipo !== 'operativa' && (! is_string($tipo) || ! array_key_exists($tipo, VickyReportes::PERMISOS))) {
            if (! preg_match('/\b(?:borra|elimina|modifica|actualiza|registra|ejecuta|transfiere|paga|inserta|saldo historico)\b/u', $texto)) {
                $estado = 'unclassified';
            }

            return response()->json(['respuesta' => 'No pude identificar el proceso de Aitrol en tu pregunta. Dime el módulo y qué necesitas hacer. Por ejemplo: «Dame un ejemplo de libro diario», «¿Qué reviso antes del cierre contable?» o «Abre proveedores».']);
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
            $iniciarInforme = fn () => $iniciarActividad($ambito);
            $resultado = $tipo === 'operativa'
                ? $operativa->consultar($plan, $fechas, $iniciarInforme)
                : $reportes->consultar($tipo, $fechas, $tercero, $iniciarInforme) + ['tema' => $tipo];
            $estado = $resultado['estado_informe'] ?? 'completed';
            unset($resultado['estado_informe']);
            if ($estado === 'completed' && isset($resultado['reporte'])) {
                $resultado['reporte']['origen'] = 'base_datos_autorizada';
            }

            return response()->json($resultado);
        } catch (QueryException) {
            $estado = 'failed';

            return response()->json(['message' => 'La fuente de lectura del informe no está disponible. Revisa la conexión y sus permisos.'], 503);
        }
    }

    /** @return array{workflow: string, permiso: ?string, topic?: string}|null */
    private function reciboGuia(Request $request, ?string $id, int $empresa): ?array
    {
        if ($id === null) {
            return null;
        }
        $recibo = $request->session()->get(self::SESION_GUIAS, [])[$id] ?? null;
        if ($recibo === null || $recibo['empresa'] !== $empresa || $recibo['usuario'] !== $request->user()->getAuthIdentifier()
            || $recibo['fecha'] < now()->subHour()->timestamp) {
            return null;
        }

        return $recibo;
    }

    private function puedeCorregir(Request $request, ?string $permiso): bool
    {
        return $permiso !== null && $request->user()->is_admin && ($permiso === 'administrar-configuracion'
            ? Gate::allows($permiso)
            : Gate::allows('permiso', [$permiso, 'actualizar']));
    }

    /** @param array{permiso: ?string, permisoRelacionado?: ?string} $guia */
    private function autorizarGuia(array $guia): void
    {
        foreach (array_unique([$guia['permiso'], $guia['permisoRelacionado'] ?? null]) as $permiso) {
            if ($permiso === 'administrar-configuracion') {
                Gate::authorize($permiso);
            } elseif ($permiso !== null) {
                Gate::authorize('permiso', [$permiso, 'leer']);
            }
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
