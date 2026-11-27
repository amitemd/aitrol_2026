<?php

namespace App\Services;

use Aitrol\Comercial\Application\DocumentosComerciales;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class VickyReportes
{
    public const PERMISOS = [
        'ventas' => 'ventas.reportes.index',
        'compras' => 'compras.reportes.index',
        'banco' => 'caja-bancos.cuentas.index',
        'cobrar' => 'clientes.reportes.cuentas-por-cobrar',
        'pagar' => 'proveedores.reportes.cuentas-por-pagar',
    ];

    public function __construct(
        private EmpresaActiva $empresa,
        private DocumentosComerciales $documentos,
    ) {}

    /** @param array{desde?: string, hasta?: string} $fechas */
    public function consultar(string $tipo, array $fechas, ?string $tercero): array
    {
        Gate::authorize('permiso', [self::PERMISOS[$tipo], 'leer']);

        return match ($tipo) {
            'ventas', 'compras' => $this->comercial($tipo, $fechas),
            'banco' => $this->bancos($tercero),
            'cobrar', 'pagar' => $this->cartera($tipo, $fechas, $tercero),
        };
    }

    private function comercial(string $tipo, array $fechas): array
    {
        $datos = $this->documentos->reporte($tipo, $fechas, $this->conexion());
        $titulo = $tipo === 'ventas' ? 'Ventas netas' : 'Compras netas';
        $periodo = $this->periodo($fechas);
        $nombres = [
            'documentos' => $tipo === 'ventas' ? 'Ventas' : 'Compras',
            'notas-debito' => 'Notas de débito',
            'notas-credito' => 'Notas de crédito',
        ];
        $filas = $datos['filas']->filter(fn ($fila): bool => ! $fila->anulado && isset($nombres[$fila->tipo]))
            ->map(fn ($fila): array => [$nombres[$fila->tipo], (int) $fila->cantidad, $this->importe((int) $fila->total)])
            ->values()->all();
        $texto = $filas === [] ? "No hay documentos vigentes en el período {$periodo}."
            : "{$titulo} del {$periodo}: ".$this->importe($datos['neto']).'. Incluye impuestos y ajusta las notas de crédito y débito vigentes.';
        $filas[] = ['Total neto', '', $this->importe($datos['neto'])];

        return $this->reporte($texto, "{$titulo} · {$periodo}", ['Documento', 'Cantidad', 'Total'], $filas);
    }

    private function bancos(?string $banco): array
    {
        $consulta = $this->tabla('cb_cuentas')->where('tipo', 'banco')->where('activo', true);
        if ($banco !== null) {
            $consulta->where(function (Builder $query) use ($banco): void {
                $query->whereRaw("banco LIKE ? ESCAPE '!'", [$this->patron($banco)])
                    ->orWhereRaw("nombre LIKE ? ESCAPE '!'", [$this->patron($banco)]);
            });
        }

        $totales = (clone $consulta)->select('moneda')->selectRaw('SUM(saldo) AS saldo')
            ->groupBy('moneda')->orderBy('moneda')->get();
        if ($totales->isEmpty()) {
            return ['respuesta' => 'No encontré cuentas bancarias activas para esa consulta en tu empresa.'];
        }
        $cuentas = $consulta->orderBy('moneda')->orderBy('nombre')->orderBy('id')
            ->limit(101)->get(['banco', 'nombre', 'moneda', 'saldo']);
        $filas = $cuentas->take(100)->map(fn ($cuenta): array => [
            $cuenta->banco ?? '', $cuenta->nombre, $this->importe((int) $cuenta->saldo, $cuenta->moneda),
        ])->all();
        $saldos = $totales->map(fn ($total): string => $this->importe((int) $total->saldo, $total->moneda))->implode(' · ');
        $detalle = $cuentas->count() > 100 ? ' El detalle muestra las primeras 100 cuentas.' : '';

        return $this->reporte("Saldo en libros de cuentas bancarias activas: {$saldos}. Fuente: Caja y bancos de Aitrol.{$detalle}",
            'Saldo bancario en libros', ['Banco', 'Cuenta', 'Saldo'], $filas);
    }

    private function cartera(string $tipo, array $fechas, ?string $tercero): array
    {
        $titulo = $tipo === 'cobrar' ? 'Cuentas por cobrar' : 'Cuentas por pagar';
        if (! config('services.vicky.cartera_'.$tipo)) {
            return [
                'respuesta' => "{$titulo} aún no tiene una fuente de saldos y vencimientos conectada en Aitrol. El importe pendiente queda sin determinar.",
                'estado_informe' => 'failed',
            ];
        }

        $consulta = $this->tabla('vicky_cartera_'.$tipo)->where('saldo_centavos', '>', 0);
        if ($fechas !== []) {
            $consulta->whereBetween('fecha_vencimiento', [$fechas['desde'], $fechas['hasta']]);
        }
        if ($tercero !== null) {
            $consulta->whereRaw("tercero LIKE ? ESCAPE '!'", [$this->patron($tercero)]);
            $coincidencias = (clone $consulta)->select('tercero')->distinct()->orderBy('tercero')->limit(2)->pluck('tercero');
            if ($coincidencias->count() > 1) {
                return [
                    'respuesta' => 'Hay más de un tercero con ese nombre. Escribe la razón social completa para precisar la consulta.',
                    'estado_informe' => 'rejected',
                ];
            }
        }
        $totales = (clone $consulta)->select('moneda')->selectRaw('SUM(saldo_centavos) AS saldo')
            ->groupBy('moneda')->orderBy('moneda')->get();
        if ($totales->isEmpty()) {
            return ['respuesta' => "No encontré saldos pendientes en {$titulo} para ese filtro."];
        }
        $detalle = $consulta->select('tercero', 'moneda', 'fecha_vencimiento')->selectRaw('SUM(saldo_centavos) AS saldo')
            ->groupBy('tercero', 'moneda', 'fecha_vencimiento')->orderBy('fecha_vencimiento')
            ->orderBy('tercero')->orderBy('moneda')->limit(101)->get();
        $filas = $detalle->take(100)->map(fn ($fila): array => [
            $fila->tercero, $fila->fecha_vencimiento ?? 'Sin fecha', $this->importe((int) $fila->saldo, $fila->moneda),
        ])->all();
        $saldos = $totales->map(fn ($total): string => $this->importe((int) $total->saldo, $total->moneda))->implode(' · ');
        $periodo = $fechas === [] ? 'todos los vencimientos' : 'vencimientos del '.$this->periodo($fechas);
        $limite = $detalle->count() > 100 ? ' El detalle muestra las primeras 100 filas.' : '';

        return $this->reporte("{$titulo}, {$periodo}. Saldo pendiente actual: {$saldos}.{$limite}", "{$titulo} · {$periodo}",
            ['Tercero', 'Vencimiento', 'Pendiente'], $filas);
    }

    public function tablaOperativa(string $fuente): Builder
    {
        $tabla = config("vicky-reportes.{$fuente}.tabla");
        abort_unless(is_string($tabla), 404);

        return $this->tabla($tabla);
    }

    private function tabla(string $tabla): Builder
    {
        return $this->conexion()->table($tabla)->where($tabla.'.id_empresa', $this->empresa->id());
    }

    private function conexion(): ConnectionInterface
    {
        return DB::connection(config('database.default') === 'sqlite' ? 'vicky_sqlite' : 'vicky_mysql');
    }

    private function patron(string $texto): string
    {
        return '%'.strtr($texto, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';
    }

    private function periodo(array $fechas): string
    {
        return $fechas['desde'].' al '.$fechas['hasta'];
    }

    private function importe(int $centavos, string $moneda = 'USD'): string
    {
        return $moneda.' '.number_format($centavos / 100, 2, '.', ',');
    }

    private function reporte(string $respuesta, string $titulo, array $columnas, array $filas): array
    {
        return ['respuesta' => $respuesta, 'reporte' => compact('titulo', 'columnas', 'filas')];
    }
}
