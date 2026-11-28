<?php

namespace Aitrol\Proveedores\Infrastructure\Persistence;

use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class CarteraProveedores
{
    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto) {}

    public function facturas(CarbonImmutable $corte, ?string $busqueda = null): Builder
    {
        $fecha = $corte->toDateString();
        $fin = $corte->addDay()->startOfDay()->utc();
        $entero = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true) ? 'SIGNED' : 'BIGINT';
        $facturas = $this->documentos('f', 'documentos')->where('f.fecha', '<=', $fecha)
            ->where(fn (Builder $query) => $query->whereNull('f.anulado_at')->orWhere('f.anulado_at', '>=', $fin))
            ->join('co_asientos as asiento', fn ($join) => $join->on('asiento.id_empresa', '=', 'f.id_empresa')
                ->on('asiento.id_origen', '=', 'f.id')->where('asiento.origen', 'comercial.documento')
                ->where('asiento.fecha', '<=', $fecha))
            ->leftJoin('erp_proveedores as proveedor', fn ($join) => $join->on('proveedor.id_empresa', '=', 'f.id_empresa')
                ->on('proveedor.id', '=', 'f.id_proveedor'))
            ->when(filled($busqueda), fn (Builder $query) => $query->where(fn (Builder $filtro) => $filtro
                ->whereLike('f.tercero', '%'.$busqueda.'%')->orWhereLike('f.numero', '%'.$busqueda.'%')
                ->orWhereLike('proveedor.nombre', '%'.$busqueda.'%')->orWhereLike('proveedor.identificacion', '%'.$busqueda.'%')));

        $ajustes = $this->documentos('aj')->whereIn('aj.tipo', ['notas-credito', 'notas-debito', 'retenciones'])
            ->whereNotNull('aj.id_documento_origen')->where('aj.fecha', '<=', $fecha)
            ->where(fn (Builder $query) => $query->whereNull('aj.anulado_at')->orWhere('aj.anulado_at', '>=', $fin))
            ->join('co_asientos as asiento_ajuste', fn ($join) => $join->on('asiento_ajuste.id_empresa', '=', 'aj.id_empresa')
                ->on('asiento_ajuste.id_origen', '=', 'aj.id')->where('asiento_ajuste.origen', 'comercial.documento')
                ->where('asiento_ajuste.fecha', '<=', $fecha))
            ->select('aj.id_documento_origen')
            ->selectRaw("SUM(CASE WHEN aj.tipo = 'notas-debito' THEN CAST(aj.total AS $entero) ELSE -CAST(aj.total AS $entero) END) AS valor")
            ->groupBy('aj.id_documento_origen');

        $pagos = $this->pagos($corte)->whereNull('reversa.id')->select('pago.id_factura_compra')
            ->selectRaw("SUM(CAST(pago.importe AS $entero)) AS total")->groupBy('pago.id_factura_compra');

        $detalle = $facturas->leftJoinSub($ajustes, 'ajustes', 'ajustes.id_documento_origen', '=', 'f.id')
            ->leftJoinSub($pagos, 'pagos', 'pagos.id_factura_compra', '=', 'f.id')
            ->select('f.id', 'f.id_proveedor', 'f.numero', 'f.fecha', 'f.total', 'asiento.id as id_asiento')
            ->selectRaw('COALESCE(proveedor.nombre, f.tercero) AS tercero')
            ->selectRaw($this->vencimiento().' AS vencimiento')
            ->selectRaw('COALESCE(ajustes.valor, 0) AS ajustes, COALESCE(pagos.total, 0) AS pagado')
            ->selectRaw("CAST(f.total AS $entero) + COALESCE(ajustes.valor, 0) - COALESCE(pagos.total, 0) AS saldo");

        return DB::query()->fromSub($detalle, 'cartera');
    }

    public function factura(int $id, CarbonImmutable $corte): stdClass
    {
        return $this->facturas($corte)->where('id', $id)->first()
            ?? abort(404, 'No se encontró una factura de compra contabilizada y vigente para esta sucursal.');
    }

    public function pagos(CarbonImmutable $corte): Builder
    {
        return DB::table('cb_operaciones as pago')->where('pago.id_empresa', $this->empresa->id())
            ->where('pago.id_sucursal', $this->contexto->sucursal()->id)
            ->where('pago.tipo', 'egreso')->whereNotNull('pago.id_factura_compra')
            ->where('pago.fecha', '<=', $corte->toDateString())
            ->leftJoin('cb_operaciones as reversa', fn ($join) => $join->on('reversa.id_empresa', '=', 'pago.id_empresa')
                ->on('reversa.id_reversa_de', '=', 'pago.id')->where('reversa.fecha', '<=', $corte->toDateString()));
    }

    private function documentos(string $alias, ?string $tipo = null): Builder
    {
        return DB::table('com_documentos as '.$alias)->where($alias.'.id_empresa', $this->empresa->id())
            ->where($alias.'.id_sucursal', $this->contexto->sucursal()->id)->where($alias.'.modulo', 'compras')
            ->when($tipo !== null, fn (Builder $query) => $query->where($alias.'.tipo', $tipo));
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
