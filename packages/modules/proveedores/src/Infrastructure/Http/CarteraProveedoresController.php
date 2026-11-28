<?php

namespace Aitrol\Proveedores\Infrastructure\Http;

use Aitrol\Proveedores\Application\UseCases\PagarFacturaCompra;
use Aitrol\Proveedores\Infrastructure\Persistence\CarteraProveedores;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final readonly class CarteraProveedoresController
{
    public function __construct(private CarteraProveedores $cartera) {}

    public function index(Request $request): View|JsonResponse
    {
        $datos = $request->validate(['corte' => $this->reglasCorte(), 'q' => ['nullable', 'string', 'max:160']]);
        $corte = $this->fecha($datos['corte'] ?? null);
        $reporte = $request->route('reporte');
        $consulta = $this->cartera->facturas($corte, $datos['q'] ?? null);
        if ($reporte === 'pagos-vencidos') {
            $consulta->where('vencimiento', '<', $corte->toDateString());
        }
        $totales = (clone $consulta)->selectRaw('COALESCE(SUM(CASE WHEN saldo > 0 THEN saldo ELSE 0 END), 0) AS pendiente,
            COALESCE(SUM(CASE WHEN saldo > 0 AND vencimiento < ? THEN saldo ELSE 0 END), 0) AS vencido,
            COALESCE(SUM(CASE WHEN saldo < 0 THEN -saldo ELSE 0 END), 0) AS a_favor', [$corte->toDateString()])->first();
        $totales->pendiente = (int) $totales->pendiente;
        $totales->vencido = (int) $totales->vencido;
        $totales->a_favor = (int) $totales->a_favor;

        $consulta->where('saldo', '>', 0);
        $agrupado = in_array($reporte, ['saldos-por-proveedor', 'antiguedad-de-saldos'], true);
        $porEdad = $reporte === 'antiguedad-de-saldos';
        if ($agrupado) {
            $consulta->select('id_proveedor', 'tercero')->selectRaw('SUM(saldo) AS pendiente')
                ->groupBy('id_proveedor', 'tercero')->orderByDesc('pendiente')->orderBy('tercero')->orderBy('id_proveedor');
            if ($porEdad) {
                $consulta->selectRaw('SUM(CASE WHEN vencimiento >= ? THEN saldo ELSE 0 END) AS vigente,
                    SUM(CASE WHEN vencimiento < ? AND vencimiento >= ? THEN saldo ELSE 0 END) AS dias_1_30,
                    SUM(CASE WHEN vencimiento < ? AND vencimiento >= ? THEN saldo ELSE 0 END) AS dias_31_60,
                    SUM(CASE WHEN vencimiento < ? AND vencimiento >= ? THEN saldo ELSE 0 END) AS dias_61_90,
                    SUM(CASE WHEN vencimiento < ? THEN saldo ELSE 0 END) AS dias_91', [
                    $corte->toDateString(), $corte->toDateString(), $corte->subDays(30)->toDateString(),
                    $corte->subDays(30)->toDateString(), $corte->subDays(60)->toDateString(),
                    $corte->subDays(60)->toDateString(), $corte->subDays(90)->toDateString(),
                    $corte->subDays(90)->toDateString(),
                ]);
            }
        } else {
            $consulta->orderBy('vencimiento')->orderBy('id');
        }
        $filas = $consulta->paginate(25)->withQueryString()->through(function (object $fila): object {
            foreach (['total', 'ajustes', 'pagado', 'saldo', 'pendiente', 'vigente', 'dias_1_30', 'dias_31_60', 'dias_61_90', 'dias_91'] as $campo) {
                if (isset($fila->$campo)) {
                    $fila->$campo = (int) $fila->$campo;
                }
            }

            return $fila;
        });
        $titulo = config('proveedores.secciones')['proveedores.reportes.'.$reporte];

        return $request->routeIs('api.*')
            ? response()->json(['corte' => $corte->toDateString(), 'totales' => $totales, 'resultados' => $filas])
            : view('proveedores::cartera', compact('titulo', 'corte', 'totales', 'filas', 'agrupado', 'porEdad'));
    }

    public function comprobantes(Request $request): View|JsonResponse
    {
        $datos = $request->validate(['corte' => $this->reglasCorte()]);
        $corte = $this->fecha($datos['corte'] ?? null);
        $filas = $this->cartera->pagos($corte)
            ->join('com_documentos as compra', fn ($join) => $join->on('compra.id', '=', 'pago.id_factura_compra')
                ->on('compra.id_empresa', '=', 'pago.id_empresa'))
            ->orderByDesc('pago.fecha')->orderByDesc('pago.id')
            ->paginate(25, ['pago.id', 'pago.fecha', 'pago.importe', 'pago.referencia', 'pago.id_factura_compra',
                'compra.numero', 'compra.tercero', 'reversa.id as id_reversa'])->withQueryString();

        return $request->routeIs('api.*')
            ? response()->json(['corte' => $corte->toDateString(), 'resultados' => $filas])
            : view('proveedores::comprobantes-pago', compact('filas', 'corte'));
    }

    public function create(int $factura): View
    {
        return view('proveedores::pagar-factura', ['factura' => $this->cartera->factura($factura, $this->fecha())]);
    }

    public function store(Request $request, int $factura, PagarFacturaCompra $pagos): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'id_cuenta' => ['required', 'integer', 'min:1'],
            'fecha' => ['required', ...$this->reglasCorte()],
            'importe' => ['required', 'numeric', 'decimal:0,2', 'between:0.01,100000000'],
            'referencia' => ['required', 'string', 'max:120'],
            'clave_operacion' => ['required', 'uuid'],
        ]);
        [$pago, $repetido] = $pagos->registrar($factura, $datos);

        if ($request->routeIs('api.*') || $request->expectsJson()) {
            return response()->json(['message' => 'Pago registrado en Caja y bancos.', 'data' => [
                'id' => $pago->id, 'id_factura_compra' => $factura, 'fecha' => $pago->fecha,
                'importe_centavos' => $pago->importe, 'referencia' => $pago->referencia,
                'id_reversion' => $pago->id_reversion,
            ]], $repetido ? 200 : 201);
        }

        return to_route('proveedores.reportes.cuentas-por-pagar')->with('status', 'Pago registrado en Caja y bancos.');
    }

    private function fecha(?string $fecha = null): CarbonImmutable
    {
        return $fecha === null ? CarbonImmutable::today('America/Guayaquil') : CarbonImmutable::parse($fecha, 'America/Guayaquil');
    }

    private function reglasCorte(): array
    {
        return ['date_format:Y-m-d', 'before_or_equal:'.$this->fecha()->toDateString()];
    }
}
