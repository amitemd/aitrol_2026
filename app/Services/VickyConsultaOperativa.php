<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final readonly class VickyConsultaOperativa
{
    public const OPERACIONES = ['listar', 'contar', 'sumar', 'promediar', 'minimo', 'maximo'];

    public const COMPARACIONES = ['igual', 'contiene', 'mayor', 'menor'];

    private const NO_DISPONIBLE = ['respuesta' => 'No puedo formar ese informe con las fuentes y campos disponibles. Indica qué dato quieres consultar y cómo quieres agruparlo.'];

    public function __construct(private VickyReportes $reportes) {}

    public function catalogoVisible(): array
    {
        $catalogo = [];
        foreach (config('vicky-reportes') as $clave => $fuente) {
            if (Gate::allows('permiso', [$fuente['permiso'], 'leer']) && $this->disponible($clave)
                && ($clave !== 'existencias' || (Gate::allows('permiso', ['inventarios.productos.index', 'leer'])
                    && Gate::allows('permiso', ['inventarios.bodegas.index', 'leer'])))) {
                $catalogo[$clave] = [
                    'titulo' => $fuente['titulo'],
                    'campos' => $fuente['columnas'],
                    'numericos' => array_keys($fuente['numeros']),
                    'fecha' => $fuente['fecha'],
                ];
            }
        }

        return $catalogo;
    }

    public function consultaDirecta(string $pregunta): ?array
    {
        if (preg_match('/^\s*[¿¡]?\s*(?:cu[aá]l es |dame |busca )?(?:el |la )?(teléfono|telefono|correo|email|dirección|direccion|identificación|identificacion)\s+(?:del? )?(cliente|proveedor)\s+(.+?)\s*[?!.]?\s*$/iu', $pregunta, $contacto)) {
            $campo = match (Str::lower(Str::ascii($contacto[1]))) {
                'correo' => 'email',
                default => Str::lower(Str::ascii($contacto[1])),
            };

            return [
                'fuente' => mb_strtolower($contacto[2]) === 'cliente' ? 'clientes' : 'proveedores',
                'operacion' => 'listar',
                'campos' => ['nombre', $campo],
                'filtros' => [['campo' => 'nombre', 'comparacion' => 'contiene', 'valor' => $contacto[3]]],
            ];
        }

        if (preg_match('/^\s*[¿¡]?\s*busca(?:r)?\s+(cliente|proveedor|paciente)\s+(.+?)\s*[?!.]?\s*$/iu', $pregunta, $busqueda)) {
            $fuente = match (mb_strtolower($busqueda[1])) {
                'cliente' => 'clientes',
                'proveedor' => 'proveedores',
                'paciente' => 'pacientes',
            };

            return ['fuente' => $fuente, 'operacion' => 'listar',
                'filtros' => [['campo' => 'nombre', 'comparacion' => 'contiene', 'valor' => $busqueda[2]]]];
        }

        $texto = trim(Str::lower(Str::ascii($pregunta)), " \t\n?.!");
        if (preg_match('/^(?:reporte de )?(ventas por cliente|compras por proveedor)(?: (?:este mes|mes pasado|este ano|en (?:enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre)(?: de 20\d{2})?))?$/', $texto, $desglose)) {
            return [
                'fuente' => $desglose[1] === 'ventas por cliente' ? 'facturas_venta' : 'facturas_compra',
                'operacion' => 'sumar',
                'campo' => 'total',
                'agrupar' => 'tercero',
            ];
        }
        $patron = '/^(?:dame (?:un |una )?)?(cuant[oa]s|total de|cantidad de|lista de|listado de|listar|muestrame|reporte de)\s+(?:(?:tenemos|tengo|hay)\s+)?(?:los |las |de )?(clientes|proveedores|pacientes|productos|bodegas|sucursales|existencias|activos fijos|declaraciones)(?:\s+(activos|activas|inactivos|inactivas))?(?:\s+(?:tengo|tenemos|hay|registrados|registradas))?(?:\s+(?:hoy|ayer|este mes|este ano|mes pasado|esta semana|en (?:enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre)(?: de 20\d{2})?))?$/';
        if (! preg_match($patron, $texto, $partes)) {
            return null;
        }

        $fuente = str_replace(' ', '_', $partes[2]);
        $plan = [
            'fuente' => $fuente,
            'operacion' => preg_match('/^(cuant|total|cantidad)/', $partes[1]) ? 'contar' : 'listar',
        ];
        if (isset($partes[3]) && $partes[3] !== '') {
            if ($fuente !== 'pacientes' && ! in_array('activo', config("vicky-reportes.{$fuente}.columnas", []), true)) {
                return null;
            }
            $activo = str_starts_with($partes[3], 'activ');
            $plan['filtros'] = [[
                'campo' => $fuente === 'pacientes' ? 'estado' : 'activo',
                'comparacion' => 'igual',
                'valor' => $fuente === 'pacientes' ? ($activo ? 'activo' : 'inactivo') : ($activo ? '1' : '0'),
            ]];
        }

        return $plan;
    }

    /** @param array{desde?: string, hasta?: string} $fechas */
    public function consultar(array $plan, array $fechas): array
    {
        $clave = $plan['fuente'] ?? null;
        $catalogo = config('vicky-reportes');
        if (! is_string($clave) || ! isset($catalogo[$clave])) {
            return self::NO_DISPONIBLE;
        }
        $fuente = $catalogo[$clave];
        Gate::authorize('permiso', [$fuente['permiso'], 'leer']);
        if (! $this->disponible($clave)) {
            return ['respuesta' => 'El registro de pacientes aún no tiene una fuente habilitada para consultas por empresa en Aitrol.'];
        }

        $datos = $this->validar($plan, $fuente);
        if ($datos === null || ($fechas !== [] && $fuente['fecha'] === null)) {
            return self::NO_DISPONIBLE;
        }

        $consulta = $this->reportes->tablaOperativa($clave)->where($fuente['condiciones']);
        if ($clave === 'existencias') {
            Gate::authorize('permiso', ['inventarios.productos.index', 'leer']);
            Gate::authorize('permiso', ['inventarios.bodegas.index', 'leer']);
            $consulta = $this->existencias($consulta);
        } elseif (in_array($clave, ['lineas_venta', 'lineas_compra'], true)) {
            $consulta = $this->lineasComerciales($consulta, $clave === 'lineas_venta' ? 'ventas' : 'compras');
        } elseif ($clave === 'tesoreria_cierres') {
            $base = $consulta->join('cb_cuentas as c', function (JoinClause $union): void {
                $union->on('c.id_empresa', '=', 'cb_cierres.id_empresa')->on('c.id', '=', 'cb_cierres.id_cuenta');
            })->select('cb_cierres.id', 'c.moneda', 'cb_cierres.saldo_apertura', 'cb_cierres.saldo_cierre',
                'cb_cierres.efectivo_contado', 'cb_cierres.diferencia', 'cb_cierres.cerrado_at');
            $consulta = $consulta->getConnection()->query()->fromSub($base, 'cierres');
        }
        foreach ($datos['filtros'] ?? [] as $filtro) {
            $this->filtrar($consulta, $filtro, $fuente['numeros']);
        }
        if ($fechas !== []) {
            $consulta->whereBetween($fuente['fecha'], [$fechas['desde'], $fechas['hasta'].' 23:59:59.999999']);
        }

        return $this->resultado($consulta, $fuente, $datos, $fechas);
    }

    private function disponible(string $clave): bool
    {
        return $clave !== 'pacientes' || (bool) config('services.vicky.pacientes');
    }

    private function validar(array $plan, array $fuente): ?array
    {
        $columnas = $fuente['columnas'];
        $validador = Validator::make($plan, [
            'operacion' => ['required', 'string', Rule::in(self::OPERACIONES)],
            'campo' => ['nullable', 'string', Rule::in(array_keys($fuente['numeros']))],
            'campos' => ['sometimes', 'array', 'list', 'max:10'],
            'campos.*' => ['string', 'distinct', Rule::in($columnas)],
            'agrupar' => ['nullable', 'string', Rule::in(array_diff($columnas, array_keys($fuente['numeros'])))],
            'ordenar' => ['nullable', 'string', Rule::in($columnas)],
            'direccion' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'limite' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'filtros' => ['sometimes', 'array', 'list', 'max:5'],
            'filtros.*' => ['array:campo,comparacion,valor'],
            'filtros.*.campo' => ['required', 'string', Rule::in($columnas)],
            'filtros.*.comparacion' => ['required', 'string', Rule::in(self::COMPARACIONES)],
            'filtros.*.valor' => ['required', 'string', 'max:160'],
        ]);
        if ($validador->fails() || array_diff(array_keys($plan), ['fuente', 'operacion', 'campo', 'campos', 'agrupar', 'ordenar', 'direccion', 'limite', 'filtros']) !== []) {
            return null;
        }
        $datos = $validador->validated();
        $operacion = $datos['operacion'];
        if (($operacion === 'listar' && isset($datos['agrupar']))
            || (! in_array($operacion, ['listar', 'contar'], true) && ! isset($datos['campo']))) {
            return null;
        }
        foreach ($datos['filtros'] ?? [] as $filtro) {
            $campo = $filtro['campo'];
            if (isset($fuente['numeros'][$campo]) && (! is_numeric($filtro['valor']) || abs((float) $filtro['valor']) > 1e12)) {
                return null;
            }
            if (in_array($campo, ['activo', 'auxiliar', 'efectivo'], true) && ! in_array($filtro['valor'], ['0', '1'], true)) {
                return null;
            }
        }

        return $datos;
    }

    private function existencias(Builder $consulta): Builder
    {
        $base = $consulta->join('inv_productos as p', function (JoinClause $union): void {
            $union->on('p.id_empresa', '=', 'inv_existencias.id_empresa')->on('p.id', '=', 'inv_existencias.id_producto');
        })->join('inv_bodegas as b', function (JoinClause $union): void {
            $union->on('b.id_empresa', '=', 'inv_existencias.id_empresa')->on('b.id', '=', 'inv_existencias.id_bodega');
        })->select('p.nombre as producto', 'b.nombre as bodega', 'inv_existencias.cantidad', 'inv_existencias.costo_promedio',
            'inv_existencias.id_producto', 'inv_existencias.id_bodega');

        return $consulta->getConnection()->query()->fromSub($base, 'existencias');
    }

    private function lineasComerciales(Builder $consulta, string $modulo): Builder
    {
        $base = $consulta->join('com_documentos as d', function (JoinClause $union): void {
            $union->on('d.id_empresa', '=', 'com_lineas.id_empresa')->on('d.id', '=', 'com_lineas.id_documento');
        })->where('d.modulo', $modulo)->where('d.tipo', 'documentos')->whereNull('d.anulado_at')
            ->select('com_lineas.id', 'd.fecha', 'd.numero', 'd.tercero', 'com_lineas.descripcion',
                'com_lineas.cantidad', 'com_lineas.subtotal', 'com_lineas.impuesto', 'com_lineas.total');

        return $consulta->getConnection()->query()->fromSub($base, 'detalle');
    }

    private function filtrar(Builder $consulta, array $filtro, array $numeros): void
    {
        $campo = $filtro['campo'];
        $valor = $filtro['valor'];
        if ($filtro['comparacion'] === 'contiene') {
            $columna = $consulta->getGrammar()->wrap($campo);
            $consulta->whereRaw($columna." LIKE ? ESCAPE '!'", ['%'.strtr($valor, ['!' => '!!', '%' => '!%', '_' => '!_']).'%']);

            return;
        }

        $operador = match ($filtro['comparacion']) {
            'mayor' => '>',
            'menor' => '<',
            default => '=',
        };
        $valor = isset($numeros[$campo]) ? (int) round((float) $valor * (10 ** $numeros[$campo])) : $valor;
        $consulta->where($campo, $operador, $valor);
    }

    private function resultado(Builder $consulta, array $fuente, array $datos, array $fechas): array
    {
        $limite = (int) ($datos['limite'] ?? 25);
        $numeros = $fuente['numeros'];
        $etiquetas = [];
        if ($datos['operacion'] === 'listar') {
            $campos = ($datos['campos'] ?? []) ?: ($fuente['listado'] ?? $fuente['columnas']);
            $consulta->orderBy($datos['ordenar'] ?? $fuente['orden'], $datos['direccion'] ?? 'asc')
                ->orderBy($fuente['orden'])->select($campos);
            if ($fuente['tabla'] === 'inv_existencias') {
                $consulta->orderBy('id_bodega');
            }
        } else {
            $campo = $datos['campo'] ?? null;
            [$funcion, $etiqueta] = match ($datos['operacion']) {
                'sumar' => ['SUM', 'Suma'],
                'promediar' => ['AVG', 'Promedio'],
                'minimo' => ['MIN', 'Mínimo'],
                'maximo' => ['MAX', 'Máximo'],
                'contar' => ['COUNT', 'Cantidad'],
            };
            $grupos = isset($datos['agrupar']) ? [$datos['agrupar']] : [];
            if ($funcion !== 'COUNT' && in_array('moneda', $fuente['columnas'], true) && ! in_array('moneda', $grupos, true)) {
                $grupos[] = 'moneda';
            }
            $expresion = $funcion === 'COUNT' ? '*' : $consulta->getGrammar()->wrap($campo);
            $consulta->select($grupos)->selectRaw("{$funcion}({$expresion}) as resultado");
            if ($grupos !== []) {
                $consulta->groupBy(...$grupos)->orderBy('resultado', $datos['direccion'] ?? 'desc');
                foreach ($grupos as $grupo) {
                    $consulta->orderBy($grupo);
                }
            }
            $campos = [...$grupos, 'resultado'];
            $numeros['resultado'] = $funcion === 'COUNT' ? 0 : $numeros[$campo];
            $etiquetas['resultado'] = $funcion === 'COUNT' ? $etiqueta : $etiqueta.' · '.$this->etiqueta($campo);
        }

        $registros = $consulta->limit($limite + 1)->get();
        $columnas = array_map(fn (string $campo): string => $etiquetas[$campo] ?? $this->etiqueta($campo), $campos);
        $filas = $registros->take($limite)->map(fn ($fila): array => array_map(
            fn (string $campo): string|int|float => $this->formatear($fila->$campo, $campo, $numeros),
            $campos,
        ))->values()->all();
        $titulo = $fuente['titulo'];
        $resumen = match (true) {
            $filas === [] => 'No hay registros para ese filtro.',
            $campos === ['resultado'] => "{$titulo}. {$columnas[0]}: {$filas[0][0]}.",
            default => 'Informe de la empresa activa.',
        };
        if ($fechas !== []) {
            $resumen .= ' Período del '.$fechas['desde'].' al '.$fechas['hasta'].'.';
        }
        if ($registros->count() > $limite) {
            $resumen .= " Se muestran los primeros {$limite} resultados.";
        }

        return ['respuesta' => $resumen, 'reporte' => compact('titulo', 'columnas', 'filas')];
    }

    private function etiqueta(string $campo): string
    {
        return $campo === 'created_at' ? 'Fecha de registro' : Str::headline($campo);
    }

    private function formatear(mixed $valor, string $campo, array $numeros): string|int|float
    {
        if ($valor === null) {
            return '—';
        }
        if (in_array($campo, ['activo', 'auxiliar', 'efectivo'], true)) {
            return $valor ? 'Sí' : 'No';
        }
        $escala = $numeros[$campo] ?? 0;

        return $escala > 0 ? number_format($valor / (10 ** $escala), $escala, '.', '') : $valor;
    }
}
