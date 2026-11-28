<?php

namespace App\Services;

use Aitrol\CajaBancos\Application\LibroCaja;
use Aitrol\Comercial\Application\DocumentosComerciales;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class CobroFacturaVenta
{
    public function __construct(
        private EmpresaActiva $empresa,
        private ContextoOperativo $contexto,
        private DocumentosComerciales $documentos,
        private LibroCaja $caja,
    ) {}

    public function saldo(stdClass $factura): int
    {
        return $this->saldos(collect([$factura]))->get($factura->id);
    }

    public function saldos(Collection $facturas): Collection
    {
        if ($facturas->isEmpty()) {
            return collect();
        }
        $ids = $facturas->pluck('id');
        $ajustes = $this->ajustesVigentes()->whereIn('id_documento_origen', $ids)
            ->selectRaw("id_documento_origen, SUM(CASE WHEN tipo = 'notas-debito' THEN total ELSE -total END) AS ajuste")
            ->groupBy('id_documento_origen')->pluck('ajuste', 'id_documento_origen');
        $pagos = $this->cobrosVigentes()->whereIn('op.id_factura_venta', $ids)->selectRaw('op.id_factura_venta, SUM(op.importe) AS cobrado')
            ->groupBy('op.id_factura_venta')->pluck('cobrado', 'op.id_factura_venta');

        return $facturas->mapWithKeys(fn (stdClass $factura): array => [
            $factura->id => (int) $factura->total + (int) $ajustes->get($factura->id) - (int) $pagos->get($factura->id),
        ]);
    }

    public function movimientos(int $id): Collection
    {
        return $this->cobrosVigentes()->where('op.id_factura_venta', $id)
            ->join('cb_asientos as partida', fn ($join) => $join->on('partida.id_operacion', '=', 'op.id')->on('partida.id_empresa', '=', 'op.id_empresa'))
            ->join('cb_cuentas as cuenta', fn ($join) => $join->on('cuenta.id', '=', 'partida.id_cuenta')->on('cuenta.id_empresa', '=', 'op.id_empresa'))
            ->leftJoin('cb_lineas_extracto as extracto', fn ($join) => $join->on('extracto.id_asiento', '=', 'partida.id')->on('extracto.id_empresa', '=', 'op.id_empresa'))
            ->orderBy('op.fecha')->orderBy('op.id')
            ->get(['op.id', 'op.fecha', 'op.importe', 'op.referencia', 'cuenta.nombre as cuenta', 'cuenta.tipo', 'extracto.id as id_conciliacion']);
    }

    public function registrar(int $id, array $datos): void
    {
        $this->empresa->transaccion(function () use ($id, $datos): void {
            $factura = $this->documentos->facturaParaCobro($id);
            abort_if($factura->anulado_at !== null, 409, 'La factura está anulada.');
            if (! $factura->id_asiento) {
                throw ValidationException::withMessages(['importe' => 'Genera primero el asiento de la factura.']);
            }
            if ($datos['fecha'] < $factura->fecha) {
                throw ValidationException::withMessages(['fecha' => 'La fecha del cobro debe ser igual o posterior a la factura.']);
            }
            $repetido = DB::table('cb_operaciones')->where('id_empresa', $this->empresa->id())
                ->where('clave_operacion', strtolower($datos['clave_operacion']))->exists();
            if (! $repetido && Cantidad::unidades($datos['importe'], 2) > $this->saldo($factura)) {
                throw ValidationException::withMessages(['importe' => 'El cobro supera el saldo pendiente de la factura.']);
            }
            $this->caja->registrar('ingreso', $datos + [
                'id_factura_venta' => $id, 'concepto' => 'Cobro de factura '.$factura->numero,
            ]);
        });
    }

    public function ajustesVigentes(?CarbonImmutable $corte = null): Builder
    {
        $consulta = DB::table('com_documentos')->where('id_empresa', $this->empresa->id())
            ->where('modulo', 'ventas')->whereIn('tipo', DocumentosComerciales::VINCULADOS)->whereNotNull('id_documento_origen');

        return $corte === null ? $consulta->whereNull('anulado_at') : $consulta->where('fecha', '<=', $corte->toDateString())
            ->where(fn (Builder $q) => $q->whereNull('anulado_at')->orWhere('anulado_at', '>=', $corte->addDay()->startOfDay()->utc()));
    }

    public function cobrosVigentes(?CarbonImmutable $corte = null): Builder
    {
        return $this->consultaCobros()
            ->when($corte !== null, fn (Builder $q) => $q->where('op.fecha', '<=', $corte->toDateString()))
            ->whereNotExists(fn (Builder $q) => $q->from('cb_operaciones as reversa')->selectRaw('1')
                ->whereColumn('reversa.id_empresa', 'op.id_empresa')->whereColumn('reversa.id_reversa_de', 'op.id')
                ->when($corte !== null, fn (Builder $reversas) => $reversas->where('reversa.fecha', '<=', $corte->toDateString())));
    }

    public function comprobantes(?string $busqueda): Paginator
    {
        $consulta = $this->contexto->aplicarPeriodo($this->consultaCobros(), 'op.fecha')
            ->join('com_documentos as factura', fn ($join) => $join->on('factura.id', '=', 'op.id_factura_venta')
                ->on('factura.id_empresa', '=', 'op.id_empresa')->on('factura.id_sucursal', '=', 'op.id_sucursal'))
            ->join('cb_asientos as asiento', fn ($join) => $join->on('asiento.id_operacion', '=', 'op.id')->on('asiento.id_empresa', '=', 'op.id_empresa'))
            ->join('cb_cuentas as cuenta', fn ($join) => $join->on('cuenta.id', '=', 'asiento.id_cuenta')->on('cuenta.id_empresa', '=', 'op.id_empresa'))
            ->leftJoin('cb_operaciones as reversa', fn ($join) => $join->on('reversa.id_reversa_de', '=', 'op.id')->on('reversa.id_empresa', '=', 'op.id_empresa'))
            ->when($busqueda !== null && $busqueda !== '', fn (Builder $q) => $q->where(fn (Builder $filtro) => $filtro
                ->whereLike('factura.numero', '%'.$busqueda.'%')->orWhereLike('factura.tercero', '%'.$busqueda.'%')
                ->orWhereLike('op.referencia', '%'.$busqueda.'%')));

        return $consulta->orderByDesc('op.fecha')->orderByDesc('op.id')->simplePaginate(25, [
            'op.id', 'op.fecha', 'op.referencia', 'op.moneda', 'op.importe', 'factura.numero', 'factura.tercero', 'factura.id_cliente',
            'cuenta.nombre as destino', 'reversa.id as id_reversa',
        ])->appends(['q' => $busqueda]);
    }

    private function consultaCobros(): Builder
    {
        return DB::table('cb_operaciones as op')->where('op.id_empresa', $this->empresa->id())
            ->where('op.id_sucursal', $this->contexto->sucursal()->id)->whereNotNull('op.id_factura_venta')->where('op.tipo', 'ingreso');
    }
}
