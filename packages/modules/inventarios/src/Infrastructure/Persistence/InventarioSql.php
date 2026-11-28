<?php

namespace Aitrol\Inventarios\Infrastructure\Persistence;

use Aitrol\Contabilidad\Infrastructure\Queries\ReferenciaAsiento;
use Aitrol\Inventarios\Application\RepositorioInventario;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class InventarioSql implements RepositorioInventario
{
    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto) {}

    public function listar(string $seccion, array $filtros): LengthAwarePaginator
    {
        $consulta = match ($seccion) {
            'productos', 'bodegas', 'categorias', 'plantillas-contables', 'marcas', 'unidades' => $this->catalogos($seccion, $filtros),
            'existencias', 'reportes' => $this->existencias($filtros),
            'movimientos', 'ajustes' => $this->operaciones($seccion, $filtros),
            'kardex' => $this->kardex($filtros),
        };

        return $consulta->paginate((int) ($filtros['per_page'] ?? 20), page: (int) ($filtros['page'] ?? 1))
            ->appends(array_diff_key($filtros, ['page' => true]));
    }

    public function reporte(array $filtros): iterable
    {
        return $this->existencias($filtros)->orderBy('inv_existencias.id')->cursor();
    }

    public function catalogo(string $seccion, int $id, bool $bloquear = false): stdClass
    {
        $consulta = $this->tabla('inv_'.str_replace('-', '_', $seccion))->where('id', $id);
        if ($bloquear) $consulta->lockForUpdate();

        return $consulta->first() ?? abort(404, 'Registro de inventario no encontrado.');
    }

    public function guardarCatalogo(string $seccion, ?int $id, array $datos): stdClass
    {
        try {
            return $this->transaccion(function () use ($seccion, $id, $datos): stdClass {
                $tabla = 'inv_'.str_replace('-', '_', $seccion);
                $actual = $id === null ? null : $this->catalogo($seccion, $id, bloquear: true);
                $categorias = $seccion === 'plantillas-contables' ? ($datos['id_categorias'] ?? null) : null;
                unset($datos['id_categorias']);
                if ($seccion === 'productos') {
                    $datos = $this->validarCodigoProducto($datos, $actual);
                }
                if ($seccion === 'categorias') {
                    $this->validarCategoriaUnica($id, $datos['codigo'], $datos['nombre']);
                }
                if (in_array($seccion, ['marcas', 'unidades'], true) && $this->tabla($tabla)
                    ->where('nombre', $datos['nombre'])->when($id !== null, fn (Builder $consulta) => $consulta->where('id', '<>', $id))->exists()) {
                    throw ValidationException::withMessages(['nombre' => 'Ya existe un registro con este nombre en tu empresa.']);
                }
                $datos = $this->prepararCatalogo($seccion, $datos, $actual);
                if ($id === null) {
                    if ($seccion === 'bodegas') {
                        $datos['id_sucursal'] = $this->contexto->sucursal()->id;
                    }
                    $id = DB::table($tabla)->insertGetId($datos + [
                        'id_empresa' => $this->empresa->id(), 'id_usuario_creacion' => auth()->id(),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                } else {
                    $this->verificarExistenciaCatalogo($seccion, $id, $datos);
                    $this->tabla($tabla)->where('id', $id)->update($datos + ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()]);
                    if ($seccion === 'categorias' && $actual->nombre !== $datos['nombre']) {
                        $this->tabla('inv_productos')->where('id_categoria', $id)->update(['categoria' => $datos['nombre']]);
                    }
                    if (in_array($seccion, ['marcas', 'unidades'], true) && $actual->nombre !== $datos['nombre']) {
                        $campo = $seccion === 'marcas' ? 'marca' : 'unidad';
                        $this->tabla('inv_productos')->where('id_'.$campo, $id)->update([$campo => $datos['nombre']]);
                    }
                }

                if ($categorias !== null) {
                    $this->asignarCategoriasPlantilla($id, $categorias, (bool) $datos['activo']);
                }

                return $this->catalogo($seccion, $id);
            });
        } catch (UniqueConstraintViolationException $error) {
            abort(409, 'El código o código de barras ya existe en esta empresa.');
        }
    }

    private function asignarCategoriasPlantilla(int $plantilla, array $ids, bool $activa): void
    {
        $categorias = $this->tabla('inv_categorias');
        $disponibles = (clone $categorias)->whereIn('id', $ids)->where(function (Builder $consulta) use ($plantilla, $activa): void {
            $consulta->where('id_plantilla_contable', $plantilla);
            if ($activa) {
                $consulta->orWhere(fn (Builder $libres) => $libres->whereNull('id_plantilla_contable')->where('activo', true));
            }
        })->count();
        if ($disponibles !== count($ids)) {
            throw ValidationException::withMessages([
                'id_categorias' => 'Elige categorías activas de tu empresa que estén libres o ya vinculadas a esta plantilla. La plantilla debe estar activa para agregar categorías.',
            ]);
        }

        $auditoria = ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()];
        (clone $categorias)->where('id_plantilla_contable', $plantilla)->whereNotIn('id', $ids)
            ->update(['id_plantilla_contable' => null] + $auditoria);
        $categorias->whereNull('id_plantilla_contable')->whereIn('id', $ids)
            ->update(['id_plantilla_contable' => $plantilla] + $auditoria);
    }

    public function cambiarEstadoCatalogo(string $seccion, int $id, bool $activo): stdClass
    {
        return $this->transaccion(function () use ($seccion, $id, $activo): stdClass {
            $actual = $this->catalogo($seccion, $id, bloquear: true);
            if ((bool) $actual->activo === $activo) {
                return $actual;
            }
            $this->verificarExistenciaCatalogo($seccion, $id, ['activo' => $activo]);
            $this->tabla('inv_'.str_replace('-', '_', $seccion))->where('id', $id)->update([
                'activo' => $activo, 'id_usuario_modificacion' => auth()->id(), 'updated_at' => now(),
            ]);

            return $this->catalogo($seccion, $id);
        });
    }

    private function verificarExistenciaCatalogo(string $seccion, int $id, array $datos): void
    {
        $columna = $seccion === 'productos' ? 'id_producto' : 'id_bodega';
        if (in_array($seccion, ['productos', 'bodegas'], true) && (! $datos['activo'] || ($datos['tipo_producto'] ?? null) === 'servicio')
            && $this->tabla('inv_existencias')->where($columna, $id)->where('cantidad', '>', 0)->exists()) {
            $campo = ! $datos['activo'] ? 'activo' : 'tipo_producto';
            throw ValidationException::withMessages([$campo => 'Lleva la existencia a cero antes de cambiar este campo.']);
        }
    }

    public function sugerirCodigoProducto(): string
    {
        $empresa = DB::table('erp_empresas')->where('id', $this->empresa->id())->first(['nombre_comercial', 'razon_social']);
        $nombre = $empresa?->nombre_comercial ?: ($empresa?->razon_social ?? '');
        preg_match_all('/[A-Z]+/', Str::upper(Str::ascii($nombre)), $palabras);
        $iniciales = implode('', array_map(fn (string $palabra): string => $palabra[0], array_slice($palabras[0], 0, 4)));
        $prefijo = ($iniciales ?: 'PRD').'-';

        $productos = $this->tabla('inv_productos');
        $codigos = (clone $productos)->select('codigo')->where('codigo', 'like', $prefijo.'%')
            ->orderByRaw('LENGTH(codigo) DESC')->orderByDesc('codigo')->cursor();
        $sufijoSugerido = '000001';
        foreach ($codigos as $producto) {
            $sufijo = substr($producto->codigo, strlen($prefijo));
            if (preg_match('/\A(?:[0-9]{6}|[1-9][0-9]{6,})\z/', $sufijo)) {
                $siguiente = str_increment($sufijo);
                if (strlen($prefijo.$siguiente) <= 60) {
                    return $prefijo.$siguiente;
                }
                while ((clone $productos)->where('codigo', $prefijo.$sufijoSugerido)->exists()) {
                    $sufijoSugerido = str_increment($sufijoSugerido);
                }
                break;
            }
        }

        return $prefijo.$sufijoSugerido;
    }

    private function validarCodigoProducto(array $datos, ?stdClass $actual): array
    {
        if ($actual !== null) {
            if (isset($datos['codigo']) && $datos['codigo'] !== Str::upper($actual->codigo)) {
                throw ValidationException::withMessages(['codigo' => 'El código del producto no se puede cambiar después de crearlo.']);
            }
            unset($datos['codigo']);
        } elseif ($this->tabla('inv_productos')->where('codigo', $datos['codigo'])->exists()) {
            throw ValidationException::withMessages(['codigo' => 'Ya existe un producto con este código en tu empresa. Elige otro código.']);
        }

        return $datos;
    }

    private function validarCategoriaUnica(?int $id, string $codigo, string $nombre): void
    {
        $categorias = $this->tabla('inv_categorias')
            ->when($id !== null, fn (Builder $consulta) => $consulta->where('id', '<>', $id));
        $errores = [];
        if ((clone $categorias)->where('codigo', $codigo)->exists()) {
            $errores['codigo'] = 'Ya existe una categoría con este código en tu empresa.';
        }

        $nombreNormalizado = Str::lower(Str::squish($nombre));
        $nombreRepetido = $categorias->select('nombre')->cursor()->contains(
            fn (stdClass $categoria): bool => Str::lower(Str::squish($categoria->nombre)) === $nombreNormalizado,
        );
        if ($nombreRepetido) {
            $errores['nombre'] = 'Ya existe una categoría con este nombre en tu empresa.';
        }
        if ($errores !== []) {
            throw ValidationException::withMessages($errores);
        }
    }

    public function opcionesFicha(): array
    {
        return [
            'marcas' => $this->tabla('inv_marcas')->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'activo']),
            'unidades' => $this->tabla('inv_unidades')->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'activo']),
            'categorias' => $this->tabla('inv_categorias')->orderBy('nombre')->get(['id', 'nombre', 'activo', 'id_plantilla_contable']),
            'plantillas' => $this->tabla('inv_plantillas_contables')->orderBy('nombre')->get(['id', 'nombre', 'activo', 'cuentas'])->keyBy('id'),
            'cuentas' => $this->tabla('co_cuentas')->where('auxiliar', true)->orderBy('codigo')->get(['id', 'codigo', 'nombre', 'clase', 'activo']),
        ];
    }

    public function cuentasProducto(stdClass $producto): array
    {
        $categoria = isset($producto->id_categoria) ? $this->catalogo('categorias', $producto->id_categoria) : null;
        $plantilla = isset($categoria->id_plantilla_contable) ? $this->catalogo('plantillas-contables', $categoria->id_plantilla_contable) : null;
        $cuentas = $plantilla ? $plantilla->cuentas : ($producto->cuentas ?? null);

        return [
            'plantilla_contable' => $plantilla?->nombre,
            'cuentas_efectivas' => (object) json_decode($cuentas ?? '{}', true, flags: JSON_THROW_ON_ERROR),
        ];
    }

    private function prepararCatalogo(string $seccion, array $datos, ?stdClass $actual): array
    {
        if ($seccion === 'productos') {
            $datos = $this->prepararMarcaYUnidad($datos, $actual);
        }
        $campo = match ($seccion) {
            'productos' => 'id_categoria',
            'categorias' => 'id_plantilla_contable',
            default => null,
        };
        $relacion = null;
        if ($campo !== null && ($id = $datos[$campo] ?? (! array_key_exists($campo, $datos) ? $actual?->{$campo} : null)) !== null) {
            $tabla = $seccion === 'productos' ? 'inv_categorias' : 'inv_plantillas_contables';
            $relacion = $this->tabla($tabla)->where('id', $id)->first();
            if (! $relacion || (! $relacion->activo && (int) $id !== (int) $actual?->{$campo})) {
                throw ValidationException::withMessages([$campo => 'Selecciona un registro activo de esta empresa.']);
            }
        }

        if ($seccion === 'productos' && $relacion) {
            $datos['categoria'] = $relacion->nombre;
            if ($relacion->id_plantilla_contable !== null) {
                $datos['cuentas'] = null;
            }
        } elseif ($seccion === 'productos' && array_key_exists('id_categoria', $datos) && ! array_key_exists('categoria', $datos)) {
            $datos['categoria'] = null;
        }

        if (isset($datos['cuentas'])) {
            $cuentas = array_filter($datos['cuentas'], fn ($valor) => $valor !== null);
            $validas = $this->tabla('co_cuentas')->where('activo', true)->where('auxiliar', true)
                ->whereIn('id', array_values($cuentas))->pluck('clase', 'id');
            foreach ($cuentas as $rol => $id) {
                if ($validas->get($id) !== config('inventarios.cuentas_producto.'.$rol.'.1')) {
                    throw ValidationException::withMessages(['cuentas.'.$rol => 'Selecciona una cuenta auxiliar activa, de la clase indicada y de esta empresa.']);
                }
            }
            if ($seccion === 'plantillas-contables' && $cuentas === []) {
                throw ValidationException::withMessages(['cuentas' => 'Selecciona al menos una cuenta contable.']);
            }
            $datos['cuentas'] = json_encode((object) $cuentas, JSON_THROW_ON_ERROR);
        }

        if ($seccion === 'productos' && ($datos['tipo_producto'] ?? $actual?->tipo_producto) === 'servicio') {
            $datos['stock_minimo'] = 0;
        }

        return $datos;
    }

    private function prepararMarcaYUnidad(array $datos, ?stdClass $actual): array
    {
        foreach (['marca' => 'inv_marcas', 'unidad' => 'inv_unidades'] as $campo => $tabla) {
            $llave = 'id_'.$campo;
            if (! array_key_exists($llave, $datos)) {
                if (array_key_exists($campo, $datos) && $datos[$campo] !== $actual?->{$campo}) {
                    $datos[$llave] = null;
                }

                continue;
            }

            if ($datos[$llave] === null) {
                if ($campo === 'marca') {
                    $datos[$campo] = null;
                }

                continue;
            }

            $opcion = $this->tabla($tabla)->where('id', $datos[$llave])->first(['nombre', 'activo']);
            if ($opcion === null || (! $opcion->activo && (int) $datos[$llave] !== (int) $actual?->{$llave})) {
                throw ValidationException::withMessages([$llave => 'Selecciona una opción activa de tu empresa.']);
            }
            $datos[$campo] = $opcion->nombre;
        }

        return $datos;
    }

    public function operacion(int $id): stdClass
    {
        $consulta = $this->tabla('inv_operaciones')->select('inv_operaciones.*')->where('id', $id)
            ->whereIn('id_bodega', $this->tabla('inv_bodegas')->select('id'));
        $operacion = ReferenciaAsiento::inventario($consulta, 'inv_operaciones.id')->first() ?? abort(404, 'Movimiento no encontrado.');
        $operacion->lineas = $this->tabla('inv_kardex')->where('id_operacion', $id)->orderBy('id')->get();

        return $operacion;
    }

    public function porClave(string $clave): ?stdClass
    {
        $id = $this->tabla('inv_operaciones')->where('clave_operacion', $clave)->value('id');

        return $id === null ? null : $this->operacion((int) $id);
    }

    public function saldo(int $producto, int $bodega): stdClass
    {
        return $this->tabla('inv_existencias')->where('id_producto', $producto)->where('id_bodega', $bodega)->lockForUpdate()->first()
            ?? (object) ['id_bodega' => $bodega, 'cantidad' => 0, 'costo_promedio' => 0];
    }

    public function registrar(array $operacion, array $lineas): stdClass
    {
        $id = DB::table('inv_operaciones')->insertGetId($operacion + [
            'id_empresa' => $this->empresa->id(), 'id_usuario' => auth()->id(), 'created_at' => now(),
        ]);
        foreach ($lineas as $linea) {
            $clave = ['id_empresa' => $this->empresa->id(), 'id_producto' => $operacion['id_producto'], 'id_bodega' => $linea['id_bodega']];
            DB::table('inv_existencias')->updateOrInsert($clave, [
                'cantidad' => $linea['saldo'], 'costo_promedio' => $linea['costo_promedio'],
            ]);
            DB::table('inv_kardex')->insert($clave + $linea + ['id_operacion' => $id]);
        }

        return $this->operacion((int) $id);
    }

    public function transaccion(Closure $proceso): mixed
    {
        return $this->empresa->transaccion($proceso);
    }

    private function tabla(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresa->id())
            ->when($tabla === 'inv_bodegas', fn (Builder $q) => $q->where('inv_bodegas.id_sucursal', $this->contexto->sucursal()->id));
    }

    private function catalogos(string $seccion, array $filtros): Builder
    {
        $consulta = $this->tabla('inv_'.str_replace('-', '_', $seccion))->orderBy('nombre')->orderBy('id');
        if (! empty($filtros['q'])) {
            $consulta->where(function (Builder $q) use ($filtros, $seccion): void {
                $patron = '%'.$filtros['q'].'%';
                $q->whereLike('nombre', $patron)->orWhereLike('codigo', $patron);
                if ($seccion === 'productos') $q->orWhereLike('codigo_barras', $patron)->orWhereLike('categoria', $patron);
            });
        }
        if (isset($filtros['activo'])) $consulta->where('activo', $filtros['activo']);
        if ($seccion === 'productos' && ($filtros['solo_inventariables'] ?? false)) {
            $consulta->where('tipo_producto', 'producto');
        }

        return $consulta;
    }

    private function existencias(array $filtros): Builder
    {
        $consulta = $this->conCatalogos('inv_existencias')
            ->addSelect('p.unidad', 'p.stock_minimo')->orderBy('p.nombre')->orderBy('b.nombre');
        if (! empty($filtros['bajo_minimo'])) $consulta->whereColumn('inv_existencias.cantidad', '<=', 'p.stock_minimo');

        return $this->filtrar($consulta, 'inv_existencias', $filtros);
    }

    private function operaciones(string $seccion, array $filtros): Builder
    {
        $tabla = 'inv_operaciones';
        $consulta = $this->conCatalogos($tabla)->leftJoin('inv_bodegas as destino', function ($join): void {
            $join->on('destino.id', '=', 'inv_operaciones.id_bodega_destino')->on('destino.id_empresa', '=', 'inv_operaciones.id_empresa');
        })->addSelect('destino.nombre as bodega_destino')->orderByDesc($tabla.'.id');
        $tipos = ['ajuste_entrada', 'ajuste_salida'];
        $seccion === 'ajustes' ? $consulta->whereIn($tabla.'.tipo', $tipos) : $consulta->whereNotIn($tabla.'.tipo', $tipos);
        ReferenciaAsiento::inventario($consulta, $tabla.'.id');

        return $this->filtrar($consulta, $tabla, $filtros, $tabla.'.created_at');
    }

    private function kardex(array $filtros): Builder
    {
        $consulta = $this->conCatalogos('inv_kardex')->join('inv_operaciones as o', function ($join): void {
            $join->on('o.id', '=', 'inv_kardex.id_operacion')->on('o.id_empresa', '=', 'inv_kardex.id_empresa');
        })->addSelect('o.tipo', 'o.motivo', 'o.referencia', 'o.created_at')->orderByDesc('inv_kardex.id');
        ReferenciaAsiento::inventario($consulta, 'inv_kardex.id_operacion');

        return $this->filtrar($consulta, 'inv_kardex', $filtros, 'o.created_at');
    }

    private function conCatalogos(string $tabla): Builder
    {
        return $this->tabla($tabla)
            ->join('inv_productos as p', fn ($join) => $join->on('p.id', '=', $tabla.'.id_producto')->on('p.id_empresa', '=', $tabla.'.id_empresa'))
            ->join('inv_bodegas as b', fn ($join) => $join->on('b.id', '=', $tabla.'.id_bodega')->on('b.id_empresa', '=', $tabla.'.id_empresa'))
            ->where('b.id_sucursal', $this->contexto->sucursal()->id)
            ->select($tabla.'.*', 'p.nombre as producto', 'p.codigo as codigo_producto', 'b.nombre as bodega');
    }

    private function filtrar(Builder $consulta, string $tabla, array $filtros, ?string $fecha = null): Builder
    {
        if ($fecha !== null) {
            $filtros = $this->contexto->acotarFechas($filtros);
        }
        if ($fecha !== null && isset($filtros['desde'])) {
            $consulta->where($fecha, '>=', $filtros['desde'].' 00:00:00');
        }
        if ($fecha !== null && isset($filtros['hasta'])) {
            $consulta->where($fecha, '<', \Illuminate\Support\Carbon::parse($filtros['hasta'])->addDay()->toDateString().' 00:00:00');
        }
        if (isset($filtros['id_producto'])) $consulta->where($tabla.'.id_producto', $filtros['id_producto']);
        if (isset($filtros['id_bodega'])) {
            $consulta->where(function (Builder $q) use ($tabla, $filtros): void {
                $q->where($tabla.'.id_bodega', $filtros['id_bodega']);
                if ($tabla === 'inv_operaciones') $q->orWhere($tabla.'.id_bodega_destino', $filtros['id_bodega']);
            });
        }
        if (! empty($filtros['q'])) {
            $patron = '%'.$filtros['q'].'%';
            $consulta->where(fn (Builder $q): Builder => $q->whereLike('p.nombre', $patron)->orWhereLike('p.codigo', $patron)->orWhereLike('b.nombre', $patron));
        }

        return $consulta;
    }
}
