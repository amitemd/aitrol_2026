<?php

namespace App\Services;

use Aitrol\CajaBancos\Application\LibroCaja;
use Aitrol\Comercial\Application\DocumentosComerciales;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use Illuminate\Database\Query\Builder;
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
        $ajustes = DB::table('com_documentos')->where('id_empresa', $this->empresa->id())
            ->where('modulo', 'ventas')->whereIn('id_documento_origen', $ids)
            ->whereIn('tipo', DocumentosComerciales::VINCULADOS)->whereNull('anulado_at')
            ->selectRaw("id_documento_origen, SUM(CASE WHEN tipo = 'notas-debito' THEN total ELSE -total END) AS ajuste")
            ->groupBy('id_documento_origen')->pluck('ajuste', 'id_documento_origen');
        $pagos = $this->cobrosActivos($ids)->selectRaw('op.id_factura_venta, SUM(op.importe) AS cobrado')
            ->groupBy('op.id_factura_venta')->pluck('cobrado', 'op.id_factura_venta');

        return $facturas->mapWithKeys(fn (stdClass $factura): array => [
            $factura->id => (int) $factura->total + (int) $ajustes->get($factura->id) - (int) $pagos->get($factura->id),
        ]);
    }

    public function movimientos(int $id): Collection
    {
        return $this->cobrosActivos(collect([$id]))
            ->join('cb_asientos as partida', fn ($join) => $join->on('partida.id_operacion', '=', 'op.id')->on('partida.id_empresa', '=', 'op.id_empresa'))
            ->join('cb_cuentas as cuenta', fn ($join) => $join->on('cuenta.id', '=', 'partida.id_cuenta')->on('cuenta.id_empresa', '=', 'op.id_empresa'))
            ->leftJoin('cb_lineas_extracto as extracto', fn ($join) => $join->on('extracto.id_asiento', '=', 'partida.id')->on('extracto.id_empresa', '=', 'op.id_empresa'))
            ->orderBy('op.fecha')->orderBy('op.id')
            ->get(['op.id', 'op.fecha', 'op.importe', 'op.referencia', 'cuenta.nombre as cuenta', 'cuenta.tipo', 'extracto.id as id_conciliacion']);
    }

    public function registrar(int $id, array $datos): void
    {
        $this->empresa->transaccion(function () use ($id, $datos): void {
            $factura = $this->documentos->consultar('ventas', 'documentos', $id);
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

    private function cobrosActivos(Collection $ids): Builder
    {
        return DB::table('cb_operaciones as op')->where('op.id_empresa', $this->empresa->id())
            ->where('op.id_sucursal', $this->contexto->sucursal()->id)->whereIn('op.id_factura_venta', $ids)
            ->where('op.tipo', 'ingreso')
            ->whereNotExists(fn (Builder $q) => $q->from('cb_operaciones as reversa')->selectRaw('1')
                ->whereColumn('reversa.id_empresa', 'op.id_empresa')->whereColumn('reversa.id_reversa_de', 'op.id'));
    }
}
