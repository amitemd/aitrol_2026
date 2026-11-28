<?php

namespace Aitrol\Inventarios\Infrastructure\Http;

use Aitrol\Inventarios\Application\Inventario;
use Aitrol\Inventarios\Application\RepositorioInventario;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use Aitrol\Inventarios\Infrastructure\Persistence\GaleriaProducto;
use App\Services\ContextoOperativo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class InventarioController
{
    public function __construct(
        private RepositorioInventario $repositorio,
        private Inventario $inventario,
        private GaleriaProducto $galeria,
    ) {}

    public function index(Request $request)
    {
        $seccion = $request->route('seccion');
        $filtros = $this->filtros($request, $seccion);
        $registros = $this->repositorio->listar($seccion, $filtros)->through($this->presentar(...));

        if ($request->routeIs('api.*')) {
            return response()->json($registros);
        }

        $opciones = [];
        foreach (['id_producto' => 'productos', 'id_bodega' => 'bodegas'] as $campo => $catalogo) {
            if (isset($filtros[$campo])) {
                $opciones[$campo] = $this->repositorio->catalogo($catalogo, (int) $filtros[$campo]);
            }
        }

        return view('inventarios::index', compact('seccion', 'registros', 'filtros', 'opciones'));
    }

    public function show(Request $request)
    {
        $seccion = $request->route('seccion');
        $id = (int) $request->route('registro');
        $registro = in_array($seccion, config('inventarios.catalogos', ['productos', 'bodegas']), true)
            ? $this->repositorio->catalogo($seccion, $id)
            : $this->repositorio->operacion($id);

        if (in_array($seccion, ['movimientos', 'ajustes'], true)) {
            abort_unless(str_starts_with($registro->tipo, 'ajuste_') === ($seccion === 'ajustes'), 404);
        }

        $fila = $this->presentar($registro);
        if ($seccion === 'productos') {
            $fila += $this->repositorio->cuentasProducto($registro);
        }
        if ($request->routeIs('api.*')) return response()->json(['data' => $fila]);

        $ficha = in_array($seccion, ['productos', 'plantillas-contables'], true)
            ? $this->repositorio->opcionesFicha() : [];
        $cuentas = isset($ficha['cuentas']) ? $ficha['cuentas']->keyBy('id') : collect();
        $categoriasPlantilla = $seccion === 'plantillas-contables'
            ? $ficha['categorias']->where('id_plantilla_contable', $id) : collect();

        $galeria = $seccion === 'productos' ? $this->galeria->ficha($id) : null;

        return view('inventarios::detalle', compact('seccion', 'fila', 'cuentas', 'categoriasPlantilla', 'galeria'));
    }

    public function formulario(Request $request)
    {
        $seccion = $request->route('seccion');
        $id = $request->route('registro');
        if ($seccion === 'categorias' && $id === null && ! $request->ajax()) {
            return to_route('inventarios.categorias.index', ['nueva' => 1]);
        }

        $registro = $id ? $this->presentar($this->repositorio->catalogo($seccion, (int) $id)) : null;
        $tipos = $this->tipos($seccion);
        $selecciones = [];
        if (in_array($seccion, ['movimientos', 'ajustes'], true)) {
            foreach (['id_producto' => 'productos', 'id_bodega' => 'bodegas', 'id_bodega_destino' => 'bodegas'] as $campo => $catalogo) {
                $valor = filter_var($request->old($campo), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($valor !== false) {
                    try {
                        $selecciones[$campo] = $this->repositorio->catalogo($catalogo, $valor);
                    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                        // El registro ya no está disponible para esta empresa.
                    }
                }
            }
        }

        $ficha = in_array($seccion, ['productos', 'categorias', 'plantillas-contables'], true)
            ? $this->repositorio->opcionesFicha() : [];
        $galeria = $seccion === 'productos' ? $this->galeria->ficha($id ? (int) $id : null) : null;
        $codigoSugerido = $seccion === 'productos' && $id === null ? $this->repositorio->sugerirCodigoProducto() : null;

        return view('inventarios::formulario', compact('seccion', 'registro', 'tipos', 'selecciones', 'ficha', 'galeria', 'codigoSugerido'));
    }

    public function imagen(int $producto, int $imagen)
    {
        return $this->galeria->imagen($producto, $imagen);
    }

    public function historialProducto(int $producto, ContextoOperativo $contexto): View
    {
        $ficha = $this->repositorio->catalogo('productos', $producto);
        $consulta = DB::table('com_lineas as linea')
            ->join('com_documentos as documento', function (JoinClause $join): void {
                $join->on('documento.id', '=', 'linea.id_documento')->on('documento.id_empresa', '=', 'linea.id_empresa');
            })
            ->where('linea.id_empresa', $ficha->id_empresa)->where('linea.id_producto', $producto)
            ->where('documento.id_sucursal', $contexto->sucursal()->id)->where('documento.tipo', 'documentos');
        $contexto->aplicarPeriodo($consulta, 'documento.fecha');
        $movimientos = $consulta->orderByDesc('documento.fecha')->orderByDesc('linea.id')->paginate(25, [
            'documento.id', 'documento.modulo', 'documento.numero', 'documento.fecha', 'documento.anulado_at', 'linea.cantidad',
        ]);
        $kardex = Gate::allows('permiso', ['inventarios.kardex.index', 'leer'])
            ? $this->repositorio->listar('kardex', ['id_producto' => $producto, 'page' => 1, 'per_page' => 10])->through($this->presentar(...))
            : null;
        $periodo = $contexto->periodo();

        return view('inventarios::historial-producto', compact('ficha', 'movimientos', 'kardex', 'periodo'));
    }

    public function store(Request $request)
    {
        $seccion = $request->route('seccion');
        if (in_array($seccion, config('inventarios.catalogos', ['productos', 'bodegas']), true)) {
            return $this->guardarCatalogo($request, $seccion, null);
        }

        $datos = $request->validate([
            'tipo' => ['required', Rule::in(array_keys($this->tipos($seccion)))],
            'id_producto' => ['required', 'integer', 'min:1'],
            'id_bodega' => ['required', 'integer', 'min:1'],
            'id_bodega_destino' => ['exclude_unless:tipo,transferencia', 'required', 'integer', 'min:1', 'different:id_bodega'],
            'cantidad' => ['required', 'numeric', 'decimal:0,3', 'min:0.001', 'max:1000000'],
            'costo_unitario' => ['exclude_unless:tipo,entrada,ajuste_entrada', 'required_if:tipo,entrada', 'nullable', 'numeric', 'decimal:0,4', 'min:0', 'max:100000'],
            'motivo' => ['required', 'string', 'max:250'],
            'referencia' => ['nullable', 'string', 'max:120'],
            'clave_operacion' => ['nullable', 'uuid'],
        ]);
        $resultado = $this->inventario->registrar($datos);

        return $this->respuesta($request, $seccion, $resultado['operacion'], $resultado['repetida'] ? 200 : 201,
            $resultado['repetida'] ? 'La operación ya estaba registrada.' : 'Operación registrada en Kardex.');
    }

    public function update(Request $request)
    {
        return $this->guardarCatalogo($request, $request->route('seccion'), (int) $request->route('registro'));
    }

    public function estado(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate(['activo' => ['required', 'boolean']]);
        $seccion = $request->route('seccion');
        $activo = $request->boolean('activo');
        $registro = $this->repositorio->cambiarEstadoCatalogo($seccion, (int) $request->route('registro'), $activo);

        return $this->respuesta($request, $seccion, $registro, 200, $activo ? 'Registro activado.' : 'Registro desactivado.');
    }

    public function opciones(Request $request)
    {
        $seccion = $request->route('seccion');
        $permiso = 'inventarios.'.$seccion.'.index';
        abort_unless(Gate::allows('permiso', [$permiso, 'leer']) || (
            in_array($seccion, ['movimientos', 'ajustes'], true) && Gate::allows('permiso', [$permiso, 'escribir'])
        ), 403);

        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'activo' => ['nullable', 'boolean']]);
        $catalogo = $request->route('catalogo');
        if ($catalogo === 'productos' && in_array($seccion, ['movimientos', 'ajustes'], true)) {
            $filtros['solo_inventariables'] = true;
        }
        $resultados = $this->repositorio->listar($catalogo, $filtros);

        return response()->json([
            'results' => $resultados->getCollection()->map(fn (stdClass $fila) => ['id' => $fila->id, 'text' => $fila->codigo.' · '.$fila->nombre]),
            'pagination' => ['more' => $resultados->hasMorePages()],
        ]);
    }

    public function exportar(Request $request)
    {
        $filtros = $this->filtros($request, 'reportes');

        return response()->streamDownload(function () use ($filtros): void {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Código', 'Producto', 'Bodega', 'Unidad', 'Existencia', 'Stock mínimo', 'Costo promedio', 'Valor de existencia'], ';', '"', '');
            foreach ($this->repositorio->reporte($filtros) as $registro) {
                $fila = $this->presentar($registro);
                fputcsv($salida, [
                    $this->celdaCsv($fila['codigo_producto']), $this->celdaCsv($fila['producto']),
                    $this->celdaCsv($fila['bodega']), $this->celdaCsv($fila['unidad']),
                    $fila['cantidad'], $fila['stock_minimo'], $fila['costo_promedio'], $fila['valor_existencia'],
                ], ';', '"', '');
            }
            fclose($salida);
        }, 'inventario-existencias.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function guardarCatalogo(Request $request, string $seccion, ?int $id)
    {
        $reglas = [
            'codigo' => ['required', 'string', 'max:60'],
            'nombre' => ['required', 'string', 'max:'.match ($seccion) {
                'unidades' => 30,
                'categorias', 'marcas' => 100,
                default => 160,
            }],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'activo' => [$id ? 'required' : 'sometimes', 'boolean'],
        ];
        if ($seccion === 'productos') {
            if ($id !== null) {
                array_unshift($reglas['codigo'], 'sometimes');
            }
            $reglas += [
                'unidad' => ['required_without:id_unidad', 'nullable', 'string', 'max:30'],
                'id_unidad' => ['sometimes', 'nullable', 'integer', 'min:1'],
                'categoria' => ['nullable', 'string', 'max:100'],
                'id_categoria' => ['sometimes', 'nullable', 'integer', 'min:1'],
                'codigo_barras' => ['nullable', 'string', 'max:80'],
                'stock_minimo' => ['nullable', 'numeric', 'decimal:0,3', 'min:0', 'max:1000000'],
                'tipo_producto' => ['sometimes', 'required', Rule::in(array_keys(config('inventarios.tipos_producto')))],
                'iva_codigo' => ['sometimes', 'nullable', Rule::in(array_keys(\App\Services\FacturaVenta::IVA))],
                'marca' => ['nullable', 'string', 'max:100'],
                'id_marca' => ['sometimes', 'nullable', 'integer', 'min:1'],
                'modelo' => ['nullable', 'string', 'max:100'],
                'ficha_tecnica_url' => ['nullable', 'url:https', 'max:500'],
                'version_fotos' => ['required_with:orden_fotos,fotos_nuevas', 'string', 'size:64'],
                'orden_fotos' => ['required_with:version_fotos,fotos_nuevas', 'string', 'json', 'max:300'],
                'fotos_nuevas' => ['sometimes', 'array', 'max:'.GaleriaProducto::MAXIMO],
                'fotos_nuevas.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            ];
            foreach (config('inventarios.valores_producto') as $campo => [, $maximo, $decimales]) {
                $reglas[$campo] = ['nullable', 'numeric', 'decimal:0,'.$decimales, 'min:0', 'max:'.$maximo];
            }
        }
        if ($seccion === 'categorias') {
            $reglas['id_plantilla_contable'] = ['sometimes', 'nullable', 'integer', 'min:1'];
        }
        if ($seccion === 'plantillas-contables' && ! $request->routeIs('api.*')) {
            $reglas['id_categorias'] = ['sometimes', 'array', 'list'];
            $reglas['id_categorias.*'] = ['nullable', 'integer', 'min:1', 'distinct'];
        }
        if (in_array($seccion, ['productos', 'plantillas-contables'], true)) {
            $reglas['cuentas'] = [$seccion === 'plantillas-contables' ? 'required' : 'sometimes',
                'array:'.implode(',', array_keys(config('inventarios.cuentas_producto')))];
            $reglas['cuentas.*'] = ['nullable', 'integer', 'min:1'];
        }
        $datos = $request->validate($reglas);
        if (array_key_exists('id_categorias', $datos)) {
            Gate::authorize('permiso', ['inventarios.categorias.index', 'actualizar']);
            $datos['id_categorias'] = array_values(array_filter($datos['id_categorias'], fn ($idCategoria) => $idCategoria !== null));
        }
        if (isset($datos['codigo'])) {
            $datos['codigo'] = Str::upper($datos['codigo']);
            if (Str::length($datos['codigo']) > 60) {
                throw ValidationException::withMessages(['codigo' => 'El código debe tener hasta 60 caracteres en mayúsculas.']);
            }
        }
        $datos['activo'] ??= true;
        if ($seccion === 'productos' && ($id === null || array_key_exists('stock_minimo', $datos))) {
            $datos['stock_minimo'] = Cantidad::unidades($datos['stock_minimo'] ?? 0, 3);
        }
        $galeria = array_intersect_key($datos, array_flip(['version_fotos', 'orden_fotos', 'fotos_nuevas']));
        $campos = array_diff_key($datos, $galeria);
        $guardar = fn () => $this->repositorio->guardarCatalogo($seccion, $id, $campos);
        if (isset($galeria['orden_fotos'])) {
            $orden = json_decode($galeria['orden_fotos'], true);
            if (! is_array($orden) || ! array_is_list($orden)
                || array_filter($orden, fn ($clave) => ! is_string($clave) || ! preg_match('/^[en]:[0-9]{1,19}$/D', $clave))) {
                throw \Illuminate\Validation\ValidationException::withMessages(['orden_fotos' => 'El orden de las imágenes no es válido.']);
            }
            $registro = $this->galeria->guardar($guardar, $id, $galeria['version_fotos'], $orden, $galeria['fotos_nuevas'] ?? []);
        } else {
            $registro = $guardar();
        }

        return $this->respuesta($request, $seccion, $registro, $id ? 200 : 201, 'Registro guardado.');
    }

    private function filtros(Request $request, string $seccion): array
    {
        $reglas = [
            'q' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
        if (in_array($seccion, config('inventarios.catalogos', ['productos', 'bodegas']), true)) {
            $reglas['activo'] = ['nullable', 'boolean'];
        } else {
            $reglas['id_producto'] = $reglas['id_bodega'] = ['nullable', 'integer', 'min:1'];
        }
        if (in_array($seccion, ['existencias', 'reportes'], true)) {
            $reglas['bajo_minimo'] = ['nullable', 'boolean'];
        }
        if (in_array($seccion, ['movimientos', 'ajustes', 'kardex'], true)) {
            $reglas['desde'] = ['nullable', 'date_format:Y-m-d'];
            $reglas['hasta'] = ['nullable', 'date_format:Y-m-d'];
            if ($request->filled('desde')) $reglas['hasta'][] = 'after_or_equal:desde';
        }

        return array_filter($request->validate($reglas), fn ($valor) => $valor !== null);
    }

    private function tipos(string $seccion): array
    {
        return array_filter(config('inventarios.tipos'), fn ($tipo) => str_starts_with($tipo, 'ajuste_') === ($seccion === 'ajustes'), ARRAY_FILTER_USE_KEY);
    }

    private function presentar(stdClass $registro): array
    {
        $datos = (array) $registro;
        unset($datos['huella']);
        if (isset($datos['cuentas'])) $datos['cuentas'] = json_decode($datos['cuentas'], true, flags: JSON_THROW_ON_ERROR);
        if (isset($datos['activo'])) $datos['activo'] = (bool) $datos['activo'];
        if (isset($datos['stock_minimo'], $datos['cantidad'])) {
            $datos['bajo_minimo'] = $datos['cantidad'] <= $datos['stock_minimo'];
            $datos['valor_existencia'] = Cantidad::decimal($datos['cantidad'] * $datos['costo_promedio'], 7);
        }
        foreach (['cantidad' => 3, 'stock_minimo' => 3, 'variacion' => 3, 'saldo_anterior' => 3, 'saldo' => 3, 'costo_unitario' => 4, 'costo_promedio' => 4] as $campo => $escala) {
            if (isset($datos[$campo])) $datos[$campo] = Cantidad::decimal((int) $datos[$campo], $escala);
        }
        if (isset($datos['lineas'])) $datos['lineas'] = $datos['lineas']->map($this->presentar(...));

        return $datos;
    }

    private function respuesta(Request $request, string $seccion, stdClass $registro, int $estado, string $mensaje)
    {
        if ($request->routeIs('api.*')) {
            $cabeceras = $estado === 201 ? ['Location' => route('api.v1.inventarios.'.$seccion.'.show', $registro->id)] : [];

            $datos = $this->presentar($registro);
            if ($seccion === 'productos') $datos += $this->repositorio->cuentasProducto($registro);

            return response()->json(['data' => $datos], $estado, $cabeceras);
        }
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensaje], $estado);
        }

        return redirect()->route('inventarios.'.$seccion.'.index')->with('status', $mensaje);
    }

    private function celdaCsv(string $valor): string
    {
        return preg_match('/^[\x00-\x20]*[=+@\-]|^[\t\r\n]/', $valor) ? "'".$valor : $valor;
    }
}
