<?php

namespace App\Services;

use Aitrol\Inventarios\Application\RepositorioInventario;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

final readonly class PanelAdministrativo
{
    public const ORDEN = [
        'indicadores' => ['ventas', 'compras', 'bancos', 'cobrar', 'pagar'],
        'paneles' => ['tendencia', 'alertas', 'accesos', 'productos'],
    ];

    public const GRAFICOS = ['line' => 'Líneas', 'bar' => 'Barras', 'scatter' => 'Dispersión', 'area' => 'Área'];

    public function __construct(private EmpresaActiva $empresa) {}

    public function preferencias(int $usuario): array
    {
        $fila = $this->personalizable() ? DB::table('dashboard_preferencias')
            ->where('id_empresa', $this->empresa->id())->where('id_usuario', $usuario)->first() : null;
        $guardado = $fila ? json_decode($fila->distribucion, true) : [];
        $distribucion = [];
        foreach (self::ORDEN as $grupo => $ids) {
            $vigentes = array_intersect($guardado[$grupo] ?? [], $ids);
            $distribucion[$grupo] = array_values(array_unique([...$vigentes, ...$ids]));
        }

        return [
            'distribucion' => $distribucion,
            'grafico' => isset(self::GRAFICOS[$fila->grafico ?? '']) ? $fila->grafico : 'line',
        ];
    }

    public function personalizable(): bool
    {
        return Schema::hasTable('dashboard_preferencias');
    }

    public function datos(int $periodo, int $sucursal): array
    {
        $indicadores = [];
        $series = [];
        $alertas = [];
        $accesos = [];
        $acciones = [];
        $fecha = now('America/Guayaquil')->toDateString();
        $desde = "$periodo-01-01";
        $hasta = "$periodo-12-31";
        $meses = array_map(fn (int $mes): string => CarbonImmutable::create($periodo, $mes, 1)
            ->locale(app()->getLocale())->isoFormat('MMM'), range(1, 12));

        foreach (['ventas' => ['Ventas netas', 'bi-cart-check'], 'compras' => ['Compras netas', 'bi-bag-check']] as $modulo => [$titulo, $icono]) {
            $ruta = "$modulo.reportes.index";
            if (! $this->permite($ruta)) {
                continue;
            }
            $mensual = Schema::hasColumn('com_documentos', 'id_sucursal')
                ? $this->mensual($modulo, $sucursal, $desde, $hasta) : [];
            if ($mensual !== []) {
                $series[] = ['id' => $modulo, 'titulo' => __($titulo), 'valores' => array_map(fn (int $valor): float => $valor / 100, $mensual)];
            }
            $valores = $mensual === [] ? [] : ['USD' => array_sum($mensual)];
            $indicadores[$modulo] = [$titulo, $icono, $valores, (string) $periodo, route($ruta, compact('desde', 'hasta'))];
        }

        if ($this->permite('caja-bancos.cuentas.index')) {
            $saldos = Schema::hasColumn('cb_cuentas', 'id_sucursal')
                ? DB::table('cb_cuentas')->where('id_empresa', $this->empresa->id())->where('id_sucursal', $sucursal)
                    ->where('activo', true)->selectRaw('moneda, SUM(saldo) AS saldo')->groupBy('moneda')
                    ->orderBy('moneda')->pluck('saldo', 'moneda')->all()
                : [];
            $indicadores['bancos'] = ['Caja y bancos', 'bi-bank', $saldos, $fecha, route('caja-bancos.cuentas.index')];
        }

        foreach (['cobrar' => ['Cartera vencida', 'bi-receipt'], 'pagar' => ['Cuentas por pagar', 'bi-wallet2']] as $tipo => [$titulo, $icono]) {
            if (! Gate::allows('permiso', [VickyReportes::PERMISOS[$tipo], 'leer'])) {
                continue;
            }
            $tabla = "vicky_cartera_$tipo";
            $valores = [];
            $nota = __('Fuente de cartera pendiente de conectar');
            if (config("services.vicky.cartera_$tipo") && (Schema::hasTable($tabla) || Schema::hasView($tabla))) {
                $consulta = DB::table($tabla)->where('id_empresa', $this->empresa->id())->where('saldo_centavos', '>', 0);
                if ($tipo === 'cobrar') {
                    $consulta->where('fecha_vencimiento', '<', $fecha);
                }
                $valores = $consulta->selectRaw('moneda, SUM(saldo_centavos) AS saldo')->groupBy('moneda')
                    ->orderBy('moneda')->pluck('saldo', 'moneda')->all() ?: ['USD' => 0];
                $nota = __('Consolidado de empresa').' · '.$fecha;
            }
            $indicadores[$tipo] = [$titulo, $icono, $valores, $nota, null];
        }

        if ($this->permite('inventarios.existencias.index') && Schema::hasColumn('inv_bodegas', 'id_sucursal') && Schema::hasTable('inv_existencias')) {
            $filtros = ['bajo_minimo' => true];
            $cantidad = app(RepositorioInventario::class)->listar('existencias', $filtros + ['per_page' => 1])->total();
            $alertas[] = ['Existencias en mínimo o por debajo', $cantidad, route('inventarios.existencias.index', $filtros)];
        }
        if ($this->permite('caja-bancos.cierres.index') && Schema::hasTable('cb_cierres') && Schema::hasColumn('cb_cuentas', 'id_sucursal')) {
            $cantidad = DB::table('cb_cierres as c')->join('cb_cuentas as a', fn ($join) => $join
                ->on('a.id', '=', 'c.id_cuenta')->on('a.id_empresa', '=', 'c.id_empresa'))
                ->where('a.id_empresa', $this->empresa->id())->where('a.id_sucursal', $sucursal)
                ->whereNull('c.cerrado_at')->count();
            $alertas[] = ['Cajas con cierre pendiente', $cantidad, route('caja-bancos.cierres.index')];
        }

        foreach ([
            'ventas.documentos.index' => ['Ventas', 'bi-cart-check'],
            'compras.ordenes-compra.index' => ['Órdenes de compra', 'bi-clipboard-check'],
            'caja-bancos.cuentas.index' => ['Caja y bancos', 'bi-bank'],
            'inventarios.productos.index' => ['Productos', 'bi-box-seam'],
            'clientes.index' => ['Clientes', 'bi-people'],
        ] as $ruta => [$titulo, $icono]) {
            if ($this->permite($ruta)) {
                $accesos[$ruta] = [$titulo, $icono];
            }
        }
        foreach ([
            'ventas.documentos' => ['Nueva venta', 'bi-cart-plus'],
            'compras.ordenes-compra' => ['Nueva orden de compra', 'bi-clipboard-plus'],
            'caja-bancos.movimientos' => ['Nuevo movimiento', 'bi-cash-stack'],
        ] as $ruta => [$titulo, $icono]) {
            if (Route::has($ruta.'.create') && Gate::allows('permiso', [$ruta.'.index', 'escribir'])) {
                $acciones[$ruta.'.create'] = [$titulo, $icono];
            }
        }

        return compact('indicadores', 'series', 'meses', 'alertas', 'accesos', 'acciones');
    }

    private function permite(string $ruta): bool
    {
        return Route::has($ruta) && Gate::allows('permiso', [$ruta, 'leer']);
    }

    /** @return list<int> */
    private function mensual(string $modulo, int $sucursal, string $desde, string $hasta): array
    {
        $filas = DB::table('com_documentos')->where('id_empresa', $this->empresa->id())
            ->where('id_sucursal', $sucursal)->where('modulo', $modulo)->whereNull('anulado_at')
            ->whereBetween('fecha', [$desde, $hasta])->whereIn('tipo', ['documentos', 'notas-debito', 'notas-credito'])
            ->select('tipo')->selectRaw('SUBSTR(fecha, 6, 2) AS mes, SUM(total) AS importe')
            ->groupBy('tipo', 'mes')->get();
        if ($filas->isEmpty()) {
            return [];
        }

        $meses = array_fill(0, 12, 0);
        foreach ($filas as $fila) {
            $meses[(int) $fila->mes - 1] += $fila->tipo === 'notas-credito' ? -(int) $fila->importe : (int) $fila->importe;
        }

        return $meses;
    }
}
