<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use App\Services\CobroFacturaVenta;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class CarteraClientes
{
    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto, private CobroFacturaVenta $cobros) {}

    public function facturas(CarbonImmutable $corte, ?int $cliente, ?string $busqueda): Builder
    {
        $empresa = $this->empresa->id();
        $sucursal = $this->contexto->sucursal()->id;
        $tipoEntero = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true) ? 'SIGNED' : 'BIGINT';
        $facturas = DB::table('com_documentos as f')->where('f.id_empresa', $empresa)->where('f.id_sucursal', $sucursal)
            ->where('f.modulo', 'ventas')->where('f.tipo', 'documentos')->where('f.fecha', '<=', $corte->toDateString())
            ->where(fn (Builder $q) => $q->whereNull('f.anulado_at')->orWhere('f.anulado_at', '>=', $corte->addDay()->startOfDay()->utc()))
            ->when($cliente !== null, fn (Builder $q) => $q->where('f.id_cliente', $cliente))
            ->leftJoin('erp_clientes as cliente', fn ($join) => $join->on('cliente.id', '=', 'f.id_cliente')->on('cliente.id_empresa', '=', 'f.id_empresa'))
            ->when($busqueda !== null && $busqueda !== '', fn (Builder $q) => $q->where(fn (Builder $filtro) => $filtro
                ->whereLike('f.numero', '%'.$busqueda.'%')->orWhereLike('f.tercero', '%'.$busqueda.'%')
                ->orWhereLike('cliente.nombre', '%'.$busqueda.'%')->orWhereLike('cliente.identificacion', '%'.$busqueda.'%')));

        $ids = (clone $facturas)->select('f.id');
        $ajustes = $this->cobros->ajustesVigentes($corte)->whereIn('id_documento_origen', clone $ids)
            ->select('id_documento_origen')
            ->selectRaw("SUM(CASE WHEN tipo = 'notas-debito' THEN CAST(total AS $tipoEntero) ELSE 0 END) AS debitos,
                SUM(CASE WHEN tipo = 'notas-credito' THEN CAST(total AS $tipoEntero) ELSE 0 END) AS creditos,
                SUM(CASE WHEN tipo = 'retenciones' THEN CAST(total AS $tipoEntero) ELSE 0 END) AS retenido")
            ->groupBy('id_documento_origen');
        $pagos = $this->cobros->cobrosVigentes($corte)->whereIn('op.id_factura_venta', clone $ids)
            ->select('op.id_factura_venta')->selectRaw("SUM(CAST(op.importe AS $tipoEntero)) AS cobrado")->groupBy('op.id_factura_venta');
        $detalle = $facturas->leftJoinSub($ajustes, 'ajuste', 'ajuste.id_documento_origen', '=', 'f.id')
            ->leftJoinSub($pagos, 'pago', 'pago.id_factura_venta', '=', 'f.id')
            ->select('f.id', 'f.id_cliente', 'f.numero', 'f.fecha', 'f.total', 'cliente.identificacion')
            ->selectRaw('COALESCE(cliente.nombre, f.tercero) AS tercero, '.$this->vencimiento().' AS vencimiento')
            ->selectRaw("COALESCE(ajuste.debitos, 0) AS debitos, COALESCE(ajuste.creditos, 0) AS creditos,
                COALESCE(ajuste.retenido, 0) AS retenido, COALESCE(pago.cobrado, 0) AS cobrado,
                CAST(f.total AS $tipoEntero) + COALESCE(ajuste.debitos, 0) - COALESCE(ajuste.creditos, 0)
                - COALESCE(ajuste.retenido, 0) - COALESCE(pago.cobrado, 0) AS saldo");

        return DB::query()->fromSub($detalle, 'cartera');
    }

    public function totales(Builder $facturas, CarbonImmutable $corte): stdClass
    {
        return (clone $facturas)->selectRaw('COALESCE(SUM(CASE WHEN saldo > 0 THEN saldo ELSE 0 END), 0) AS pendiente,
            COALESCE(SUM(CASE WHEN saldo > 0 AND vencimiento < ? THEN saldo ELSE 0 END), 0) AS vencido,
            COALESCE(SUM(CASE WHEN saldo < 0 THEN -saldo ELSE 0 END), 0) AS a_favor', [$corte->toDateString()])->first();
    }

    public function porCliente(Builder $facturas, CarbonImmutable $corte, bool $porEdad): Builder
    {
        $consulta = (clone $facturas)->where('saldo', $porEdad ? '>' : '<>', 0)
            ->select('id_cliente', 'tercero', 'identificacion')->groupBy('id_cliente', 'tercero', 'identificacion')
            ->selectRaw('SUM(CASE WHEN saldo > 0 THEN saldo ELSE 0 END) AS pendiente,
                SUM(CASE WHEN saldo > 0 AND vencimiento < ? THEN saldo ELSE 0 END) AS vencido,
                SUM(CASE WHEN saldo < 0 THEN -saldo ELSE 0 END) AS a_favor, SUM(saldo) AS saldo', [$corte->toDateString()]);

        if ($porEdad) {
            $consulta->selectRaw('SUM(CASE WHEN vencimiento >= ? THEN saldo ELSE 0 END) AS vigente,
                SUM(CASE WHEN vencimiento < ? AND vencimiento >= ? THEN saldo ELSE 0 END) AS dias_1_30,
                SUM(CASE WHEN vencimiento < ? AND vencimiento >= ? THEN saldo ELSE 0 END) AS dias_31_60,
                SUM(CASE WHEN vencimiento < ? AND vencimiento >= ? THEN saldo ELSE 0 END) AS dias_61_90,
                SUM(CASE WHEN vencimiento < ? THEN saldo ELSE 0 END) AS dias_91', [
                $corte->toDateString(), $corte->toDateString(), $corte->subDays(30)->toDateString(),
                $corte->subDays(30)->toDateString(), $corte->subDays(60)->toDateString(),
                $corte->subDays(60)->toDateString(), $corte->subDays(90)->toDateString(), $corte->subDays(90)->toDateString(),
            ]);
        }

        return $consulta->orderByDesc('pendiente')->orderBy('tercero')->orderBy('id_cliente');
    }

    public function movimientos(CarbonImmutable $corte, Collection $ids): Collection
    {
        if ($ids->isEmpty()) {
            return collect();
        }

        $ajustes = $this->cobros->ajustesVigentes($corte)->whereIn('id_documento_origen', $ids)
            ->selectRaw('id_documento_origen AS factura, fecha, tipo, numero AS referencia, total AS importe');
        $cobros = $this->cobros->cobrosVigentes($corte)->whereIn('op.id_factura_venta', $ids)
            ->selectRaw("op.id_factura_venta AS factura, op.fecha, 'cobro' AS tipo, COALESCE(op.referencia, op.concepto) AS referencia, op.importe");

        return $ajustes->unionAll($cobros)->orderBy('fecha')->orderBy('tipo')->orderBy('referencia')->get()->groupBy('factura');
    }

    private function vencimiento(): string
    {
        $plazo = DB::connection()->getQueryGrammar()->wrap('f.datos_factura->plazo_dias');

        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "DATE_ADD(f.fecha, INTERVAL COALESCE(CAST(NULLIF($plazo, 'null') AS SIGNED), 0) DAY)",
            'pgsql' => "f.fecha + COALESCE(CAST($plazo AS INTEGER), 0)",
            'sqlsrv' => "DATEADD(day, COALESCE(TRY_CAST($plazo AS INT), 0), f.fecha)",
            default => "date(f.fecha, printf('%+d days', COALESCE(CAST($plazo AS INTEGER), 0)))",
        };
    }
}
