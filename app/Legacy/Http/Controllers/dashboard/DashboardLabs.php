<?php

namespace Sis_medico\Http\Controllers\dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Seguro;

class DashboardLabs extends Controller
{
    const ESTADO_FACTURADO = 1;
    const ESTADO_COTIZACION = -1;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filtros = $this->filtros($request);
        $metricas = $this->metricas($filtros);
        $porSeguro = $this->valoresPorSeguro($filtros);
        $tendencia = $this->tendencia($filtros);

        $datos = [
            'filtros' => $filtros,
            'metricas' => $metricas,
            'seguros' => Seguro::where('inactivo', '1')->orderBy('nombre')->get(['id', 'nombre']),
            'doctores' => $this->catalogoUsuarios($filtros, 'id_doctor_ieced'),
            'creadores' => $this->catalogoUsuarios($filtros, 'id_usuariocrea'),
            'porSeguro' => $porSeguro,
            'tendencia' => $tendencia,
            'oportunidades' => $this->oportunidades($porSeguro),
            'inconsistencias' => $this->totalInconsistencias($filtros),
        ];

        if ($request->ajax()) {
            $secciones = view('dashboard.labs.index', $datos)->renderSections();

            return response(isset($secciones['action-content']) ? $secciones['action-content'] : '', 200)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        return response()->view('dashboard.labs.index', $datos)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function detalle(Request $request)
    {
        $query = $this->consultaDetalle($request);
        $orden = $request->input('orden', 'fecha');
        $direccion = strtolower($request->input('direccion', 'desc')) === 'asc' ? 'asc' : 'desc';
        $columnas = [
            'fecha' => 'eo.fecha_orden',
            'orden' => 'eo.id',
            'paciente' => 'paciente',
            'seguro' => 'seguro',
            'doctor' => 'doctor',
            'subtotal' => 'eo.valor',
            'descuento' => 'eo.descuento_valor',
            'total' => 'eo.total_valor',
            'factura' => 'eo.comprobante',
            'estado' => 'estado_texto',
            'creador' => 'creador',
        ];

        $query->orderBy(isset($columnas[$orden]) ? $columnas[$orden] : 'eo.fecha_orden', $direccion);
        $ordenes = $query->paginate(15);
        $ordenes->appends($request->except('page'));

        return view('dashboard.labs._detalle', [
            'ordenes' => $ordenes,
            'ordenActual' => $orden,
            'direccionActual' => $direccion,
            'tipoDetalle' => $request->input('tipo', 'todos'),
        ]);
    }

    public function exportar(Request $request)
    {
        $request->merge(['busqueda' => $request->input('busqueda', '')]);
        $ordenes = $this->consultaDetalle($request)->orderBy('eo.fecha_orden', 'desc')->get();
        $nombre = 'ordenes-laboratorio-' . date('Ymd-His') . '.csv';

        return Response::stream(function () use ($ordenes) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, [
                'Fecha', 'Orden', 'Paciente', 'Identificacion', 'Seguro', 'Doctor',
                'Subtotal', 'Descuento', 'Total', 'Factura', 'Estado', 'Creador', 'Inconsistencia'
            ], ';');

            foreach ($ordenes as $orden) {
                fputcsv($salida, [
                    $orden->fecha_orden,
                    $orden->id,
                    $orden->paciente,
                    $orden->identificacion,
                    $orden->seguro,
                    $orden->doctor,
                    number_format((float) $orden->subtotal, 2, '.', ''),
                    number_format((float) $orden->descuento, 2, '.', ''),
                    number_format((float) $orden->total, 2, '.', ''),
                    $orden->factura,
                    $orden->estado_texto,
                    $orden->creador,
                    $orden->inconsistencia,
                ], ';');
            }

            fclose($salida);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
        ]);
    }

    private function filtros(Request $request)
    {
        $hoy = Carbon::today();
        $desde = $this->fechaValida($request->input('fecha_desde'), $hoy->copy()->startOfMonth()->toDateString());
        $hasta = $this->fechaValida($request->input('fecha_hasta'), $hoy->toDateString());

        if ($desde > $hasta) {
            $temporal = $desde;
            $desde = $hasta;
            $hasta = $temporal;
        }

        $estado = (string) $request->input('estado', '');
        if (!in_array($estado, ['', 'facturado', 'emitido', 'cotizacion'])) {
            $estado = '';
        }

        return [
            'fecha_desde' => $desde,
            'fecha_hasta' => $hasta,
            'seguro' => (string) $request->input('seguro', ''),
            'doctor' => (string) $request->input('doctor', ''),
            'creador' => (string) $request->input('creador', ''),
            'estado' => $estado,
        ];
    }

    private function fechaValida($fecha, $predeterminada)
    {
        if (!is_string($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return $predeterminada;
        }

        try {
            $carbon = Carbon::createFromFormat('Y-m-d', $fecha);
            return $carbon->format('Y-m-d') === $fecha ? $fecha : $predeterminada;
        } catch (\Exception $e) {
            return $predeterminada;
        }
    }

    private function catalogoUsuarios(array $filtros, $campo)
    {
        $campo = $campo === 'id_doctor_ieced' ? 'id_doctor_ieced' : 'id_usuariocrea';
        $query = DB::table('users as u')
            ->join('examen_orden as eo', 'eo.' . $campo, '=', 'u.id')
            ->whereBetween('eo.fecha_orden', [
                $filtros['fecha_desde'] . ' 00:00:00',
                $filtros['fecha_hasta'] . ' 23:59:59',
            ]);

        if ($filtros['seguro'] !== '') {
            $query->where('eo.id_seguro', $filtros['seguro']);
        }

        return $query->select('u.id', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2')
            ->groupBy('u.id', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2')
            ->orderBy('u.apellido1')
            ->get();
    }

    private function consultaBase(array $filtros)
    {
        $query = DB::table('examen_orden as eo')
            ->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro');

        $this->aplicarFiltrosComunes($query, $filtros);
        $this->aplicarUniverso($query, $filtros['estado']);

        return $query;
    }

    private function aplicarFiltrosComunes($query, array $filtros)
    {
        $query->whereBetween('eo.fecha_orden', [
            $filtros['fecha_desde'] . ' 00:00:00',
            $filtros['fecha_hasta'] . ' 23:59:59',
        ]);

        if ($filtros['seguro'] === '__NULL__') {
            $query->whereNull('eo.id_seguro');
        } elseif ($filtros['seguro'] !== '') {
            $query->where('eo.id_seguro', $filtros['seguro']);
        }
        if ($filtros['doctor'] !== '') {
            $query->where('eo.id_doctor_ieced', $filtros['doctor']);
        }
        if ($filtros['creador'] !== '') {
            $query->where('eo.id_usuariocrea', $filtros['creador']);
        }
    }

    private function aplicarUniverso($query, $estado)
    {
        $query->where(function ($estados) {
            $estados->where(function ($facturada) {
                $facturada->where('eo.estado', self::ESTADO_FACTURADO)
                    ->where('eo.realizado', 1);
            })->orWhere('eo.estado', self::ESTADO_COTIZACION);
        });

        $this->aplicarFiltroEstado($query, $estado);
    }

    private function aplicarFiltroEstado($query, $estado)
    {
        if ($estado === 'facturado') {
            $query->where('eo.estado', self::ESTADO_FACTURADO)
                ->where('eo.realizado', 1)
                ->whereRaw($this->condicionFacturaValida());
        } elseif ($estado === 'emitido') {
            $query->where('eo.estado', self::ESTADO_FACTURADO)
                ->where('eo.realizado', 1)
                ->whereRaw($this->condicionSinFactura());
        } elseif ($estado === 'cotizacion') {
            $query->where('eo.estado', self::ESTADO_COTIZACION);
        }
    }

    private function condicionFacturaValida()
    {
        return "eo.comprobante IS NOT NULL AND TRIM(eo.comprobante) NOT IN ('', '0')";
    }

    private function condicionSinFactura()
    {
        return "(eo.comprobante IS NULL OR TRIM(eo.comprobante) IN ('', '0'))";
    }

    private function metricas(array $filtros)
    {
        $facturaValida = $this->condicionFacturaValida();
        $sinFactura = $this->condicionSinFactura();
        $fila = $this->consultaBase($filtros)->selectRaw(
            "COALESCE(SUM(CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$facturaValida} THEN eo.total_valor ELSE 0 END), 0) AS total_facturado,
             COALESCE(SUM(CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$sinFactura} THEN eo.total_valor ELSE 0 END), 0) AS total_emitido,
             COALESCE(SUM(CASE WHEN eo.estado = ? THEN eo.total_valor ELSE 0 END), 0) AS total_cotizacion,
             COUNT(DISTINCT CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$facturaValida} THEN eo.id END) AS numero_facturadas,
             COUNT(DISTINCT CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$sinFactura} THEN eo.id END) AS numero_emitidas,
             COUNT(DISTINCT CASE WHEN eo.estado = ? THEN eo.id END) AS numero_cotizaciones",
            [self::ESTADO_FACTURADO, self::ESTADO_FACTURADO, self::ESTADO_COTIZACION, self::ESTADO_FACTURADO, self::ESTADO_FACTURADO, self::ESTADO_COTIZACION]
        )->first();

        $facturado = round((float) $fila->total_facturado, 2);
        $emitido = round((float) $fila->total_emitido, 2);
        $cotizacion = round((float) $fila->total_cotizacion, 2);
        $total = round($facturado + $cotizacion, 2);
        $totalMonitoreado = round($total + $emitido, 2);
        $porcentajeFacturado = $total > 0 ? round(($facturado / $total) * 100, 2) : 0;

        return [
            'total_facturado' => $facturado,
            'total_emitido' => $emitido,
            'total_cotizacion' => $cotizacion,
            'porcentaje_facturado' => $porcentajeFacturado,
            'porcentaje_cotizacion' => $total > 0 ? round(100 - $porcentajeFacturado, 2) : 0,
            'numero_facturadas' => (int) $fila->numero_facturadas,
            'numero_emitidas' => (int) $fila->numero_emitidas,
            'numero_cotizaciones' => (int) $fila->numero_cotizaciones,
            'total_general' => $total,
            'total_monitoreado' => $totalMonitoreado,
            'porcentaje_visual_facturado' => $totalMonitoreado > 0 ? round(($facturado / $totalMonitoreado) * 100, 2) : 0,
            'porcentaje_visual_emitido' => $totalMonitoreado > 0 ? round(($emitido / $totalMonitoreado) * 100, 2) : 0,
            'porcentaje_visual_cotizacion' => $totalMonitoreado > 0 ? round(($cotizacion / $totalMonitoreado) * 100, 2) : 0,
            'ticket_promedio' => $fila->numero_facturadas > 0 ? round($facturado / $fila->numero_facturadas, 2) : 0,
        ];
    }

    private function valoresPorSeguro(array $filtros)
    {
        $facturaValida = $this->condicionFacturaValida();
        $sinFactura = $this->condicionSinFactura();

        return $this->consultaBase($filtros)
            ->selectRaw(
                "eo.id_seguro, COALESCE(s.nombre, 'Sin seguro') AS seguro,
                 COALESCE(SUM(CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$facturaValida} THEN eo.total_valor ELSE 0 END), 0) AS facturado,
                 COALESCE(SUM(CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$sinFactura} THEN eo.total_valor ELSE 0 END), 0) AS emitido,
                 COALESCE(SUM(CASE WHEN eo.estado = ? THEN eo.total_valor ELSE 0 END), 0) AS cotizado,
                 COUNT(DISTINCT CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$facturaValida} THEN eo.id END) AS facturadas,
                 COUNT(DISTINCT CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$sinFactura} THEN eo.id END) AS emitidas,
                 COUNT(DISTINCT CASE WHEN eo.estado = ? THEN eo.id END) AS cotizaciones,
                 COALESCE(SUM(CASE
                    WHEN eo.estado = ? AND eo.realizado = 1 THEN eo.total_valor
                    WHEN eo.estado = ? THEN eo.total_valor
                    ELSE 0
                 END), 0) AS total",
                [
                    self::ESTADO_FACTURADO,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_COTIZACION,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_COTIZACION,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_COTIZACION,
                ]
            )
            ->groupBy('eo.id_seguro', 's.nombre')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function tendencia(array $filtros)
    {
        $facturaValida = $this->condicionFacturaValida();
        $sinFactura = $this->condicionSinFactura();
        $dias = Carbon::parse($filtros['fecha_desde'])->diffInDays(Carbon::parse($filtros['fecha_hasta']));
        if ($dias <= 31) {
            $periodoSql = 'DATE(eo.fecha_orden)';
            $escala = 'diaria';
        } elseif ($dias <= 120) {
            $periodoSql = 'YEARWEEK(eo.fecha_orden, 3)';
            $escala = 'semanal';
        } else {
            $periodoSql = "DATE_FORMAT(eo.fecha_orden, '%Y-%m')";
            $escala = 'mensual';
        }

        $filas = $this->consultaBase($filtros)
            ->selectRaw(
                $periodoSql . " AS periodo, MIN(DATE(eo.fecha_orden)) AS fecha,
                 COALESCE(SUM(CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$facturaValida} THEN eo.total_valor ELSE 0 END), 0) AS facturado,
                 COALESCE(SUM(CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$sinFactura} THEN eo.total_valor ELSE 0 END), 0) AS emitido,
                 COALESCE(SUM(CASE WHEN eo.estado = ? THEN eo.total_valor ELSE 0 END), 0) AS cotizado,
                 COUNT(DISTINCT CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$facturaValida} THEN eo.id END) AS facturadas,
                 COUNT(DISTINCT CASE WHEN eo.estado = ? AND eo.realizado = 1 AND {$sinFactura} THEN eo.id END) AS emitidas,
                 COUNT(DISTINCT CASE WHEN eo.estado = ? THEN eo.id END) AS cotizaciones",
                [
                    self::ESTADO_FACTURADO,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_COTIZACION,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_FACTURADO,
                    self::ESTADO_COTIZACION,
                ]
            )
            ->groupBy(DB::raw($periodoSql))
            ->orderBy('periodo')
            ->get();

        foreach ($filas as $fila) {
            if ($escala === 'semanal') {
                $fila->etiqueta = 'Sem. ' . Carbon::parse($fila->fecha)->format('d/m');
                $inicio = Carbon::parse($fila->fecha)->startOfWeek()->toDateString();
                $fin = Carbon::parse($fila->fecha)->endOfWeek()->toDateString();
            } elseif ($escala === 'mensual') {
                $mes = Carbon::parse($fila->periodo . '-01');
                $fila->etiqueta = $mes->format('m/Y');
                $inicio = $mes->copy()->startOfMonth()->toDateString();
                $fin = $mes->copy()->endOfMonth()->toDateString();
            } else {
                $fila->etiqueta = Carbon::parse($fila->periodo)->format('d/m');
                $inicio = $fila->periodo;
                $fin = $fila->periodo;
            }
            $fila->desde = $inicio < $filtros['fecha_desde'] ? $filtros['fecha_desde'] : $inicio;
            $fila->hasta = $fin > $filtros['fecha_hasta'] ? $filtros['fecha_hasta'] : $fin;
        }

        return ['escala' => $escala, 'filas' => $filas];
    }

    private function oportunidades($porSeguro)
    {
        $oportunidades = [];
        foreach ($porSeguro as $fila) {
            $volumen = (int) $fila->facturadas + (int) $fila->emitidas + (int) $fila->cotizaciones;

            $base = (float) $fila->facturado + (float) $fila->cotizado;
            $oportunidades[] = (object) [
                'id_seguro' => $fila->id_seguro,
                'seguro' => $fila->seguro,
                'facturado' => (float) $fila->facturado,
                'emitido' => (float) $fila->emitido,
                'cotizado' => (float) $fila->cotizado,
                'conversion' => $base > 0 ? round(((float) $fila->facturado / $base) * 100, 2) : 0,
                'volumen' => $volumen,
                'total' => (float) $fila->facturado + (float) $fila->emitido + (float) $fila->cotizado,
            ];
        }

        usort($oportunidades, function ($a, $b) {
            if ($a->total == $b->total) {
                return strcasecmp($a->seguro, $b->seguro);
            }
            return $a->total < $b->total ? 1 : -1;
        });

        return $oportunidades;
    }

    private function totalInconsistencias(array $filtros)
    {
        $query = DB::table('examen_orden as eo');
        $this->aplicarFiltrosComunes($query, $filtros);
        $this->aplicarFiltroEstado($query, $filtros['estado']);
        $this->aplicarControlCalidad($query, $this->facturasDuplicadas($filtros));

        return (int) $query->distinct()->count('eo.id');
    }

    private function consultaDetalle(Request $request)
    {
        $filtros = $this->filtros($request);
        if ($request->input('seguro_sin_asignar') === '1') {
            $filtros['seguro'] = '__NULL__';
        } elseif ($request->input('seguro_detalle', '') !== '') {
            $filtros['seguro'] = $request->input('seguro_detalle');
        }

        $query = DB::table('examen_orden as eo')
            ->leftJoin('paciente as p', 'p.id', '=', 'eo.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro')
            ->leftJoin('users as d', 'd.id', '=', 'eo.id_doctor_ieced')
            ->leftJoin('users as u', 'u.id', '=', 'eo.id_usuariocrea');

        $this->aplicarFiltrosComunes($query, $filtros);
        $tipo = $request->input('tipo', 'todos');
        $facturasDuplicadas = $this->facturasDuplicadas($filtros);

        if ($tipo === 'inconsistencias') {
            $this->aplicarFiltroEstado($query, $filtros['estado']);
            $this->aplicarControlCalidad($query, $facturasDuplicadas);
        } else {
            $this->aplicarUniverso($query, $filtros['estado']);
            if ($tipo === 'facturado') {
                $query->where('eo.estado', self::ESTADO_FACTURADO)
                    ->where('eo.realizado', 1)
                    ->whereRaw($this->condicionFacturaValida());
            } elseif ($tipo === 'emitido') {
                $query->where('eo.estado', self::ESTADO_FACTURADO)
                    ->where('eo.realizado', 1)
                    ->whereRaw($this->condicionSinFactura());
            } elseif ($tipo === 'cotizacion') {
                $query->where('eo.estado', self::ESTADO_COTIZACION);
            }
        }

        $busqueda = trim($request->input('busqueda', ''));
        if ($busqueda !== '') {
            $query->where(function ($buscar) use ($busqueda) {
                $termino = '%' . $busqueda . '%';
                $buscar->where('eo.id', 'like', $termino)
                    ->orWhere('p.id', 'like', $termino)
                    ->orWhere('s.nombre', 'like', $termino)
                    ->orWhere('eo.comprobante', 'like', $termino)
                    ->orWhereRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) LIKE ?", [$termino])
                    ->orWhereRaw("CONCAT_WS(' ', d.nombre1, d.nombre2, d.apellido1, d.apellido2) LIKE ?", [$termino])
                    ->orWhereRaw("CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) LIKE ?", [$termino]);
            });
        }

        $marcadoresDuplicadas = count($facturasDuplicadas) > 0
            ? implode(',', array_fill(0, count($facturasDuplicadas), '?'))
            : "''";

        return $query->selectRaw(
            "eo.id, eo.fecha_orden,
             COALESCE(NULLIF(TRIM(CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2)), ''), 'Sin paciente') AS paciente,
             COALESCE(NULLIF(TRIM(p.id), ''), 'Sin identificacion') AS identificacion,
             COALESCE(s.nombre, 'Sin seguro') AS seguro,
             COALESCE(NULLIF(TRIM(CONCAT_WS(' ', d.nombre1, d.nombre2, d.apellido1, d.apellido2)), ''), NULLIF(TRIM(eo.doctor_txt), ''), 'Sin doctor') AS doctor,
             COALESCE(eo.valor, 0) AS subtotal,
             COALESCE(eo.descuento_valor, 0) AS descuento,
             COALESCE(eo.total_valor, 0) AS total,
             COALESCE(NULLIF(NULLIF(TRIM(eo.comprobante), ''), '0'), 'Sin factura') AS factura,
             CASE
                WHEN eo.estado = -1 THEN 'COTIZACION'
                WHEN eo.estado = 1 AND eo.realizado = 1 AND " . $this->condicionFacturaValida() . " THEN 'FACTURADA'
                WHEN eo.estado = 1 AND eo.realizado = 1 THEN 'EMITIDA'
                WHEN eo.estado = 0 THEN 'ANULADA'
                ELSE 'INCONSISTENTE'
             END AS estado_texto,
             COALESCE(NULLIF(TRIM(CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2)), ''), 'Sin creador') AS creador,
             " . $this->expresionInconsistencia($marcadoresDuplicadas) . ' AS inconsistencia',
            $facturasDuplicadas
        );
    }

    private function facturasDuplicadas(array $filtros)
    {
        $query = DB::table('examen_orden as eo')
            ->select('eo.comprobante')
            ->whereNotNull('eo.comprobante')
            ->whereRaw("TRIM(eo.comprobante) NOT IN ('', '0')");

        $this->aplicarFiltrosComunes($query, $filtros);

        return $query->groupBy('eo.comprobante')
            ->havingRaw('COUNT(DISTINCT eo.id) > 1')
            ->pluck('eo.comprobante')
            ->all();
    }

    private function aplicarControlCalidad($query, array $facturasDuplicadas)
    {
        $query->where(function ($calidad) use ($facturasDuplicadas) {
            $calidad->whereRaw('eo.total_valor IS NULL OR eo.valor IS NULL OR eo.total_valor < 0 OR eo.valor < 0 OR COALESCE(eo.descuento_valor, 0) < 0')
                ->orWhereRaw('ABS(COALESCE(eo.total_valor, 0) - (COALESCE(eo.valor, 0) - COALESCE(eo.descuento_valor, 0) + COALESCE(eo.recargo_valor, 0))) > 0.02')
                ->orWhereRaw("eo.id_paciente IS NULL OR TRIM(eo.id_paciente) = ''")
                ->orWhereNull('eo.id_seguro')
                ->orWhereRaw("(eo.id_doctor_ieced IS NULL OR TRIM(eo.id_doctor_ieced) = '') AND (eo.doctor_txt IS NULL OR TRIM(eo.doctor_txt) = '')")
                ->orWhereRaw("eo.id_usuariocrea IS NULL OR TRIM(eo.id_usuariocrea) = ''")
                ->orWhereRaw('(eo.estado = 1 AND COALESCE(eo.realizado, 0) <> 1) OR (eo.estado = -1 AND COALESCE(eo.realizado, 0) <> 0) OR eo.estado IS NULL OR eo.estado NOT IN (-1, 0, 1)');
            if (count($facturasDuplicadas) > 0) {
                $calidad->orWhereIn('eo.comprobante', $facturasDuplicadas);
            }
        });
    }

    private function expresionInconsistencia($marcadoresDuplicadas)
    {
        return "CONCAT_WS('; ',
            CASE WHEN eo.comprobante IN (" . $marcadoresDuplicadas . ") THEN 'Factura repetida entre ordenes' END,
            CASE WHEN eo.total_valor IS NULL OR eo.valor IS NULL THEN 'Valor obligatorio faltante' END,
            CASE WHEN eo.total_valor < 0 OR eo.valor < 0 OR COALESCE(eo.descuento_valor, 0) < 0 THEN 'Valor negativo' END,
            CASE WHEN ABS(COALESCE(eo.total_valor, 0) - (COALESCE(eo.valor, 0) - COALESCE(eo.descuento_valor, 0) + COALESCE(eo.recargo_valor, 0))) > 0.02 THEN 'Total no concilia' END,
            CASE WHEN eo.id_paciente IS NULL OR TRIM(eo.id_paciente) = '' THEN 'Paciente obligatorio faltante' END,
            CASE WHEN eo.id_seguro IS NULL THEN 'Seguro obligatorio faltante' END,
            CASE WHEN (eo.id_doctor_ieced IS NULL OR TRIM(eo.id_doctor_ieced) = '') AND (eo.doctor_txt IS NULL OR TRIM(eo.doctor_txt) = '') THEN 'Doctor obligatorio faltante' END,
            CASE WHEN eo.id_usuariocrea IS NULL OR TRIM(eo.id_usuariocrea) = '' THEN 'Creador obligatorio faltante' END,
            CASE WHEN (eo.estado = 1 AND COALESCE(eo.realizado, 0) <> 1) OR (eo.estado = -1 AND COALESCE(eo.realizado, 0) <> 0) OR eo.estado IS NULL OR eo.estado NOT IN (-1, 0, 1) THEN 'Estado inconsistente' END
        )";
    }
}
