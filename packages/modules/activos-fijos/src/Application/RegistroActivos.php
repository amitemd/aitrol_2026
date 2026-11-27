<?php

namespace Aitrol\ActivosFijos\Application;

use App\Events\OperacionContabilizable;
use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class RegistroActivos
{
    private const HISTORIA = [
        'movimientos' => 'af_movimientos',
        'depreciaciones' => 'af_depreciaciones',
        'ordenes' => 'af_ordenes',
    ];

    public function __construct(private EmpresaActiva $empresa) {}

    public function listado(string $seccion, array $filtros): Builder
    {
        if (in_array($seccion, config('activos-fijos.catalogos'), true)) {
            $consulta = $this->tabla('af_catalogos')->where('af_catalogos.clase', $seccion)
                ->leftJoin('af_catalogos as padre', fn ($join) => $join->on('padre.id', '=', 'af_catalogos.id_padre')->on('padre.id_empresa', '=', 'af_catalogos.id_empresa'))
                ->select('af_catalogos.*', 'padre.nombre as padre')->orderBy('af_catalogos.nombre')->orderBy('af_catalogos.id');
            if (isset($filtros['activo'])) $consulta->where('af_catalogos.activo', $filtros['activo']);

            return $this->buscar($consulta, $filtros, ['af_catalogos.codigo', 'af_catalogos.nombre']);
        }
        if (isset(self::HISTORIA[$seccion])) {
            $tabla = self::HISTORIA[$seccion];
            $consulta = $this->tabla($tabla)
                ->join('af_activos as bien', fn ($join) => $join->on('bien.id', '=', $tabla.'.id_activo')->on('bien.id_empresa', '=', $tabla.'.id_empresa'))
                ->select($tabla.'.*', 'bien.codigo as codigo_activo', 'bien.nombre as activo')->orderByDesc($tabla.'.id');
            if (isset($filtros['id_activo'])) $consulta->where($tabla.'.id_activo', $filtros['id_activo']);
            if ($seccion === 'ordenes' && isset($filtros['estado'])) $consulta->where($tabla.'.estado', $filtros['estado']);
            if ($seccion === 'movimientos') {
                $consulta->leftJoin('af_catalogos as anterior', 'anterior.id', '=', 'af_movimientos.id_ubicacion_anterior')
                    ->leftJoin('af_catalogos as destino', 'destino.id', '=', 'af_movimientos.id_ubicacion_destino')
                    ->leftJoin('users as previo', fn ($join) => $join->on('previo.id', '=', 'af_movimientos.id_responsable_anterior')->on('previo.id_empresa', '=', 'af_movimientos.id_empresa'))
                    ->leftJoin('users as nuevo', fn ($join) => $join->on('nuevo.id', '=', 'af_movimientos.id_responsable_destino')->on('nuevo.id_empresa', '=', 'af_movimientos.id_empresa'))
                    ->addSelect('anterior.nombre as ubicacion_anterior', 'destino.nombre as ubicacion_destino', 'previo.name as responsable_anterior', 'nuevo.name as responsable_destino');
            }

            return $this->buscar($consulta, $filtros, ['bien.codigo', 'bien.nombre']);
        }
        $consulta = $this->tabla('af_activos')
            ->join('af_catalogos as subtipo', 'subtipo.id', '=', 'af_activos.id_subtipo')
            ->join('af_catalogos as tipo', 'tipo.id', '=', 'subtipo.id_padre')
            ->join('af_catalogos as grupo', 'grupo.id', '=', 'tipo.id_padre')
            ->join('af_catalogos as ubicacion', 'ubicacion.id', '=', 'af_activos.id_ubicacion')
            ->leftJoin('com_documentos as compra', 'compra.id', '=', 'af_activos.id_compra')
            ->leftJoin('users as responsable', fn ($join) => $join->on('responsable.id', '=', 'af_activos.id_responsable')->on('responsable.id_empresa', '=', 'af_activos.id_empresa'))
            ->select('af_activos.*', 'subtipo.nombre as subtipo', 'tipo.nombre as tipo', 'grupo.nombre as grupo', 'ubicacion.nombre as ubicacion',
                'responsable.name as responsable', 'compra.numero as factura', 'compra.tercero as proveedor', 'compra.anulado_at as compra_anulada_at');
        if (isset($filtros['activo'])) {
            $filtros['activo'] ? $consulta->whereNull('af_activos.fecha_baja') : $consulta->whereNotNull('af_activos.fecha_baja');
        }
        if (isset($filtros['desde'])) $consulta->where('af_activos.fecha_adquisicion', '>=', $filtros['desde']);
        if (isset($filtros['hasta'])) $consulta->where('af_activos.fecha_adquisicion', '<=', $filtros['hasta']);

        return $this->buscar($consulta, $filtros, ['af_activos.codigo', 'af_activos.nombre', 'af_activos.serie', 'compra.numero', 'compra.tercero'])
            ->orderByDesc('af_activos.id');
    }

    public function registro(string $seccion, int $id): stdClass
    {
        $tabla = self::HISTORIA[$seccion] ?? (in_array($seccion, config('activos-fijos.catalogos'), true) ? 'af_catalogos' : 'af_activos');

        return $this->listado($seccion, [])->where($tabla.'.id', $id)->first() ?? abort(404, 'Registro de activos fijos no encontrado.');
    }

    public function guardarCatalogo(string $clase, array $datos, ?int $id): stdClass
    {
        try {
            return DB::transaction(function () use ($clase, $datos, $id): stdClass {
                $padre = config('activos-fijos.padres.'.$clase);
                if ($padre !== null) {
                    $this->catalogoActivo($padre, (int) $datos['id_padre'], 'id_padre');
                }
                if ($id !== null) {
                    $anterior = $this->fila('af_catalogos', $id, $clase);
                    if ($padre !== null && (int) $anterior->id_padre !== (int) $datos['id_padre']) {
                        throw ValidationException::withMessages(['id_padre' => 'La clasificación superior se fija al crear el registro.']);
                    }
                    if ($anterior->activo && ! $datos['activo']) {
                        $hijos = $this->tabla('af_catalogos')->where('id_padre', $id)->where('activo', true)->exists();
                        $columna = $clase === 'ubicaciones' ? 'id_ubicacion' : 'id_subtipo';
                        $usado = in_array($clase, ['subtipos', 'ubicaciones'], true)
                            && $this->tabla('af_activos')->where($columna, $id)->whereNull('fecha_baja')->exists();
                        abort_if($hijos || $usado, 409, 'Hay clasificaciones o activos en uso vinculados a este registro.');
                    }
                }

                return $this->registro($clase, $this->guardar('af_catalogos', $datos + ['clase' => $clase], $id));
            }, 3);
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El código ya existe en este catálogo.');
        }
    }

    public function guardarActivo(array $datos, ?int $id): stdClass
    {
        try {
            return DB::transaction(function () use ($datos, $id): stdClass {
                $anterior = $id === null ? null : $this->fila('af_activos', $id);
                abort_if($anterior?->fecha_baja !== null, 409, 'El activo está dado de baja.');
                if ($anterior === null) {
                    $subtipo = $this->catalogoActivo('subtipos', (int) $datos['id_subtipo'], 'id_subtipo');
                    $this->catalogoActivo('ubicaciones', (int) $datos['id_ubicacion'], 'id_ubicacion');
                    $this->responsable($datos['id_responsable'] ?? null);
                    if (isset($datos['id_compra']) && ! $this->compras()->where('id', $datos['id_compra'])->exists()) {
                        throw ValidationException::withMessages(['id_compra' => 'Selecciona una compra vigente de tu empresa.']);
                    }
                    $datos['vida_util_meses'] ??= $subtipo->vida_util_meses;
                    if ($datos['vida_util_meses'] === null) {
                        throw ValidationException::withMessages(['vida_util_meses' => 'Indica la vida útil o configúrala en el subtipo. Usa 0 para un bien no depreciable.']);
                    }
                } else {
                    $datos['vida_util_meses'] ??= $anterior->vida_util_meses;
                }
                $datos['costo'] = isset($datos['costo']) ? Decimal::unidades($datos['costo'], 2) : $anterior->costo;
                $datos['valor_residual'] = isset($datos['valor_residual']) ? Decimal::unidades($datos['valor_residual'], 2) : ($anterior->valor_residual ?? 0);
                $fechaAdquisicion = $datos['fecha_adquisicion'] ?? $anterior->fecha_adquisicion;
                $inicio = $datos['inicio_depreciacion'] ?? $anterior?->inicio_depreciacion ?? $fechaAdquisicion;
                if ($datos['valor_residual'] > $datos['costo']) {
                    throw ValidationException::withMessages(['valor_residual' => 'El valor residual no puede superar el costo.']);
                }
                if ($inicio < $fechaAdquisicion) {
                    throw ValidationException::withMessages(['inicio_depreciacion' => 'La depreciación debe iniciar en la fecha de adquisición o después.']);
                }
                $datos['inicio_depreciacion'] = (int) $datos['vida_util_meses'] === 0 ? null : $inicio;
                $financiero = ['vida_util_meses', 'costo', 'valor_residual', 'inicio_depreciacion'];
                if ($anterior !== null && $this->tabla('af_depreciaciones')->where('id_activo', $id)->exists()) {
                    foreach ($financiero as $campo) {
                        abort_if((string) $datos[$campo] !== (string) $anterior->$campo, 409, 'Los valores de depreciación ya tienen períodos registrados.');
                    }
                }

                if ($anterior !== null && (int) $anterior->costo !== (int) $datos['costo']) {
                    event(new OperacionContabilizable('activos.costo', $id));
                }
                $activoId = $this->guardar('af_activos', $datos, $id);
                if ($id === null) event(new OperacionContabilizable('activos.alta', $activoId));

                return $this->registro('activos', $activoId);
            }, 3);
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El código de activo ya existe en tu empresa.');
        }
    }

    public function mover(array $datos): stdClass
    {
        return DB::transaction(function () use ($datos): stdClass {
            $activo = $this->fila('af_activos', (int) $datos['id_activo']);
            abort_if($activo->fecha_baja !== null, 409, 'El activo está dado de baja.');
            $ultima = $this->tabla('af_movimientos')->where('id_activo', $activo->id)->max('fecha') ?? $activo->fecha_adquisicion;
            if ($datos['fecha'] < $ultima) {
                throw ValidationException::withMessages(['fecha' => 'La fecha debe ser igual o posterior al último movimiento o adquisición.']);
            }
            $cambios = match ($datos['tipo']) {
                'traslado' => $this->traslado($activo, (int) $datos['id_ubicacion_destino']),
                'asignacion' => $this->asignacion($activo, $datos['id_responsable_destino'] ?? null),
                'baja' => $this->baja($activo, $datos['fecha']),
            };
            $datos['id_ubicacion_anterior'] = $activo->id_ubicacion;
            $datos['id_responsable_anterior'] = $activo->id_responsable;
            $datos['id_ubicacion_destino'] = $cambios['id_ubicacion'] ?? $activo->id_ubicacion;
            $datos['id_responsable_destino'] = array_key_exists('id_responsable', $cambios) ? $cambios['id_responsable'] : $activo->id_responsable;
            $this->guardar('af_activos', $cambios, $activo->id);

            $id = $this->guardar('af_movimientos', $datos);
            if ($datos['tipo'] === 'baja') event(new OperacionContabilizable('activos.baja', $id));

            return $this->registro('movimientos', $id);
        }, 3);
    }

    public function depreciar(array $datos): stdClass
    {
        try {
            return DB::transaction(function () use ($datos): stdClass {
                $activo = $this->fila('af_activos', (int) $datos['id_activo']);
                $meses = (int) $activo->vida_util_meses;
                abort_if($meses === 0 || $activo->costo <= $activo->valor_residual, 409, 'Este activo no tiene base depreciable.');
                $periodo = $datos['periodo'].'-01';
                if ($activo->fecha_baja !== null && $periodo >= substr($activo->fecha_baja, 0, 7).'-01') {
                    abort(409, 'El período debe ser anterior al mes de baja.');
                }
                $libro = $this->tabla('af_depreciaciones')->where('id_activo', $activo->id);
                $ultimo = (clone $libro)->max('periodo');
                $siguiente = $this->proximoPeriodo($activo->inicio_depreciacion, $ultimo);
                abort_if($datos['periodo'] !== $siguiente, 409, 'El siguiente período disponible es '.$siguiente.'.');
                $numero = $libro->count() + 1;
                abort_if($numero > $meses, 409, 'La vida útil de este activo ya se completó.');
                $acumulado = intdiv(($activo->costo - $activo->valor_residual) * $numero, $meses);
                $this->guardar('af_activos', ['depreciado' => $acumulado], $activo->id);
                $id = $this->guardar('af_depreciaciones', [
                    'id_activo' => $activo->id, 'periodo' => $periodo, 'importe' => $acumulado - $activo->depreciado,
                    'acumulado' => $acumulado, 'valor_libros' => $activo->costo - $acumulado,
                ]);
                event(new OperacionContabilizable('activos.depreciacion', $id));

                return $this->registro('depreciaciones', $id);
            }, 3);
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El período ya está registrado.');
        }
    }

    public function guardarOrden(array $datos, ?int $id): stdClass
    {
        return DB::transaction(function () use ($datos, $id): stdClass {
            if ($id === null) {
                $activo = $this->fila('af_activos', (int) $datos['id_activo']);
                abort_if($activo->fecha_baja !== null, 409, 'El activo está dado de baja.');
                if ($datos['fecha_programada'] < $activo->fecha_adquisicion) {
                    throw ValidationException::withMessages(['fecha_programada' => 'La fecha debe ser igual o posterior a la adquisición del activo.']);
                }
                $datos['estado'] = 'pendiente';
            } else {
                $orden = $this->fila('af_ordenes', $id);
                $transiciones = ['pendiente' => ['en_proceso', 'completada', 'cancelada'], 'en_proceso' => ['completada', 'cancelada']];
                abort_unless(in_array($datos['estado'], $transiciones[$orden->estado] ?? [], true), 409, 'El cambio de estado no está disponible para esta orden.');
                if (in_array($datos['estado'], ['completada', 'cancelada'], true)) {
                    $datos['finalizada_at'] = now();
                }
                if (isset($datos['costo_mantenimiento'])) {
                    $datos['costo_mantenimiento'] = Decimal::unidades($datos['costo_mantenimiento'], 2);
                }
            }

            return $this->registro('ordenes', $this->guardar('af_ordenes', $datos, $id));
        }, 3);
    }

    public function opciones(string $origen, ?string $q): Builder
    {
        $consulta = match ($origen) {
            'compras' => $this->compras()->select('id', 'numero as codigo', 'tercero as nombre'),
            'responsables' => DB::table('users')->where('id_empresa', $this->empresa->id())->select('id', 'email as codigo', 'name as nombre'),
            'activos' => $this->tabla('af_activos')->whereNull('fecha_baja')->select('id', 'codigo', 'nombre'),
            'depreciables' => $this->tabla('af_activos')->whereRaw('costo - valor_residual > depreciado')->where('vida_util_meses', '>', 0)
                ->select('id', 'codigo', 'nombre', 'inicio_depreciacion')
                ->selectSub(DB::table('af_depreciaciones')->whereColumn('af_depreciaciones.id_empresa', 'af_activos.id_empresa')
                    ->whereColumn('af_depreciaciones.id_activo', 'af_activos.id')->selectRaw('max(periodo)'), 'ultimo_periodo'),
            default => $this->tabla('af_catalogos')->where('clase', $origen)->where('activo', true)->select('id', 'codigo', 'nombre'),
        };
        $columnas = match ($origen) {
            'compras' => ['numero', 'tercero'],
            'responsables' => ['email', 'name'],
            default => ['codigo', 'nombre'],
        };

        return $this->buscar($consulta, ['q' => $q], $columnas)->orderBy('nombre')->orderBy('id');
    }

    public function proximoPeriodo(string $inicio, ?string $ultimo): string
    {
        return $ultimo === null ? substr($inicio, 0, 7) : Carbon::parse($ultimo)->addMonth()->format('Y-m');
    }

    public function resumen(array $filtros): array
    {
        $consulta = $this->listado('reportes', $filtros)->reorder();
        $totales = $consulta->select([])->selectRaw(
            'count(*) as cantidad, coalesce(sum(af_activos.costo), 0) as costo, coalesce(sum(af_activos.depreciado), 0) as depreciado'
        )->first();

        return ['cantidad' => (int) $totales->cantidad, 'costo' => (int) $totales->costo, 'depreciado' => (int) $totales->depreciado,
            'valor_libros' => (int) $totales->costo - (int) $totales->depreciado];
    }

    private function baja(stdClass $activo, string $fecha): array
    {
        $ultimoPeriodo = $this->tabla('af_depreciaciones')->where('id_activo', $activo->id)->max('periodo');
        abort_if($ultimoPeriodo !== null && $ultimoPeriodo >= substr($fecha, 0, 7).'-01', 409, 'Ya existe depreciación en el mes de baja o en un período posterior.');
        abort_if($this->tabla('af_ordenes')->where('id_activo', $activo->id)->whereIn('estado', ['pendiente', 'en_proceso'])->exists(),
            409, 'Completa o cancela primero las órdenes de mantenimiento abiertas.');

        return ['fecha_baja' => $fecha, 'id_responsable' => null];
    }

    private function traslado(stdClass $activo, int $destino): array
    {
        $this->catalogoActivo('ubicaciones', $destino, 'id_ubicacion_destino');
        if ($destino === (int) $activo->id_ubicacion) {
            throw ValidationException::withMessages(['id_ubicacion_destino' => 'Selecciona una ubicación distinta de la actual.']);
        }

        return ['id_ubicacion' => $destino];
    }

    private function asignacion(stdClass $activo, int|string|null $destino): array
    {
        $this->responsable($destino, 'id_responsable_destino');
        if ((int) $destino === (int) $activo->id_responsable) {
            throw ValidationException::withMessages(['id_responsable_destino' => 'La asignación debe cambiar el responsable actual.']);
        }

        return ['id_responsable' => $destino];
    }

    private function catalogoActivo(string $clase, int $id, string $campo): stdClass
    {
        return $this->tabla('af_catalogos')->where('id', $id)->where('clase', $clase)->where('activo', true)->lockForUpdate()->first()
            ?? throw ValidationException::withMessages([$campo => 'Selecciona un registro activo de tu empresa.']);
    }

    private function responsable(int|string|null $id, string $campo = 'id_responsable'): void
    {
        if ($id !== null && ! DB::table('users')->where('id_empresa', $this->empresa->id())->where('id', $id)->exists()) {
            throw ValidationException::withMessages([$campo => 'Selecciona un usuario de tu empresa.']);
        }
    }

    private function compras(): Builder
    {
        return $this->tabla('com_documentos')->where('modulo', 'compras')->where('tipo', 'documentos')->whereNull('anulado_at');
    }

    private function tabla(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresa->id());
    }

    private function fila(string $tabla, int $id, ?string $clase = null): stdClass
    {
        $consulta = $this->tabla($tabla)->where('id', $id);
        if ($clase !== null) $consulta->where('clase', $clase);

        return $consulta->lockForUpdate()->first() ?? abort(404, 'Registro de activos fijos no encontrado.');
    }

    private function guardar(string $tabla, array $datos, ?int $id = null): int
    {
        if ($id === null) {
            return (int) DB::table($tabla)->insertGetId($datos + [
                'id_empresa' => $this->empresa->id(), 'id_usuario_creacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->tabla($tabla)->where('id', $id)->update($datos + ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()]);

        return $id;
    }

    private function buscar(Builder $consulta, array $filtros, array $columnas): Builder
    {
        if (isset($filtros['q']) && $filtros['q'] !== '') {
            $consulta->where(function (Builder $q) use ($filtros, $columnas): void {
                foreach ($columnas as $columna) {
                    $q->orWhereLike($columna, '%'.$filtros['q'].'%');
                }
            });
        }

        return $consulta;
    }
}
