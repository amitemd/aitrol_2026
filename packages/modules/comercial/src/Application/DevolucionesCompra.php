<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class DevolucionesCompra
{
    public function __construct(
        private EmpresaActiva $empresa,
        private ContextoOperativo $contexto,
        private DocumentosComerciales $documentos,
    ) {}

    public function seguimiento(int $id): array
    {
        $factura = $this->factura($id);
        $mercaderia = $this->mercaderia($factura, $this->devueltas($id));

        return [
            'factura' => ['id' => $factura->id, 'numero' => $factura->numero, 'proveedor' => $factura->tercero, 'anulada' => $factura->anulado_at !== null],
            'lineas' => $mercaderia->map(fn (stdClass $linea): array => [
                'id_linea_recepcion' => $linea->id, 'id_recepcion' => $linea->id_documento, 'descripcion' => $linea->descripcion,
                'bodega' => $linea->bodega, 'cantidad_facturada' => Cantidad::decimal($linea->cantidad, 3),
                'cantidad_disponible' => Cantidad::decimal($factura->anulado_at === null ? $linea->disponible : 0, 3),
            ])->values(),
            'notas' => $this->notas($id)->orderByDesc('nota.id')->paginate(25, ['nota.id', 'nota.numero', 'nota.fecha', 'nota.observacion', 'nota.total', 'nota.anulado_at'])
                ->withQueryString()->through(fn (stdClass $nota): array => [
                    'id' => $nota->id, 'numero' => $nota->numero, 'fecha' => $nota->fecha, 'motivo' => $nota->observacion,
                    'total' => Cantidad::decimal($nota->total, 2), 'anulada' => $nota->anulado_at !== null,
                ]),
        ];
    }

    public function registrar(int $id, array $datos): array
    {
        try {
            return $this->empresa->transaccion(function () use ($id, $datos): array {
                $factura = $this->factura($id);
                abort_if($factura->anulado_at !== null, 409, 'La factura fue anulada.');
                $fecha = now('America/Guayaquil')->toDateString();
                abort_if($factura->fecha > $fecha, 409, 'La fecha de la devolución debe ser igual o posterior a la factura.');
                $devueltas = $this->devueltas($id);
                $recibidas = $this->mercaderia($factura, $devueltas)->keyBy('id');
                $facturadas = DB::table('com_lineas')->where(['id_empresa' => $this->empresa->id(), 'id_documento' => $id])
                    ->whereNotNull('id_linea_orden')->get(['id_linea_orden', 'cantidad', 'precio', 'tasa', 'subtotal', 'impuesto'])->keyBy('id_linea_orden');
                $acumuladas = $devueltas->groupBy('id_linea_orden');
                $lineas = [];

                foreach ($datos['lineas'] as $indice => $fila) {
                    $recibida = $recibidas->get((int) $fila['id_linea_recepcion']);
                    $cantidad = Cantidad::unidades($fila['cantidad'], 3);
                    if ($recibida === null || $cantidad > $recibida->disponible) {
                        throw ValidationException::withMessages(['lineas.'.$indice.'.cantidad' => 'La cantidad excede lo facturado y aún no devuelto para esta recepción.']);
                    }
                    $facturada = $facturadas->get($recibida->id_linea_orden);
                    $disponible = $facturada ?? $recibida;
                    $consumidas = $facturada === null ? $devueltas->where('id_linea_recepcion', $recibida->id) : $acumuladas->get($recibida->id_linea_orden, collect());
                    $saldo = (int) $disponible->cantidad - (int) $consumidas->sum('cantidad');
                    $base = Cantidad::proporcionar((int) $disponible->subtotal - (int) $consumidas->sum('subtotal'), $cantidad, $saldo);
                    $iva = Cantidad::proporcionar((int) $disponible->impuesto - (int) $consumidas->sum('impuesto'), $cantidad, $saldo);
                    $linea = ['id_linea_recepcion' => $recibida->id, 'id_linea_orden' => $recibida->id_linea_orden, 'id_bodega' => $recibida->id_bodega,
                        'id_producto' => $recibida->id_producto, 'descripcion' => $recibida->descripcion, 'precio' => (int) $disponible->precio,
                        'tasa' => (int) $disponible->tasa, 'cantidad' => $cantidad, 'subtotal' => $base, 'impuesto' => $iva, 'total' => $base + $iva];
                    $lineas[] = $linea;
                    $acumuladas->put($recibida->id_linea_orden, $consumidas->concat([(object) $linea]));
                }
                if (array_sum(array_column($lineas, 'total')) === 0) {
                    throw ValidationException::withMessages(['lineas' => 'La nota de crédito debe tener un importe mayor a cero.']);
                }

                $nota = $this->documentos->registrarDevolucionCompra($factura, $datos['numero'], $fecha, $datos['motivo'], $lineas);

                return ['id' => $nota, 'id_factura' => $id, 'fecha' => $fecha, 'total' => Cantidad::decimal(array_sum(array_column($lineas, 'total')), 2)];
            });
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El número de nota de crédito ya existe para este proveedor.');
        }
    }

    private function factura(int $id): stdClass
    {
        $factura = DB::table('com_documentos as factura')->where([
            'factura.id' => $id, 'factura.id_empresa' => $this->empresa->id(), 'factura.id_sucursal' => $this->contexto->sucursal()->id,
            'factura.modulo' => 'compras', 'factura.tipo' => 'documentos',
        ])->whereExists(fn (Builder $q) => $q->from('com_facturas_recepciones as vinculo')->selectRaw('1')
            ->whereColumn('vinculo.id_empresa', 'factura.id_empresa')->whereColumn('vinculo.id_factura', 'factura.id'))
            ->first();

        return $factura ?? abort(404, 'No se encontró una factura cotejada con recepciones en esta sucursal.');
    }

    private function mercaderia(stdClass $factura, Collection $devueltas): Collection
    {
        $recibidas = DB::table('com_facturas_recepciones as vinculo')
            ->join('com_lineas as linea', fn ($join) => $join->on('linea.id_documento', '=', 'vinculo.id_recepcion')
                ->on('linea.id_empresa', '=', 'vinculo.id_empresa'))
            ->join('inv_bodegas as bodega', fn ($join) => $join->on('bodega.id', '=', 'linea.id_bodega')->on('bodega.id_empresa', '=', 'linea.id_empresa'))
            ->where(['vinculo.id_empresa' => $this->empresa->id(), 'vinculo.id_factura' => $factura->id])
            ->select('linea.id', 'linea.id_documento', 'linea.id_linea_orden', 'linea.id_producto', 'linea.id_bodega',
                'linea.descripcion', 'linea.precio', 'linea.tasa', 'linea.subtotal', 'linea.impuesto', 'bodega.nombre as bodega')
            ->orderBy('linea.id');
        $parcial = isset(json_decode($factura->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR)['cotejo']);
        if ($parcial) {
            $recibidas->join('com_aplicaciones_recepcion as aplica', fn ($join) => $join->on('aplica.id_empresa', '=', 'vinculo.id_empresa')
                ->on('aplica.id_factura', '=', 'vinculo.id_factura')->on('aplica.id_linea_recepcion', '=', 'linea.id'))
                ->addSelect('aplica.cantidad');
        } else {
            $recibidas->addSelect('linea.cantidad');
        }
        $porLinea = $devueltas->keyBy('id_linea_recepcion');

        return $recibidas->get()->map(function (stdClass $fila) use ($porLinea): stdClass {
            $fila->disponible = (int) $fila->cantidad - (int) ($porLinea->get($fila->id)?->cantidad ?? 0);

            return $fila;
        });
    }

    private function devueltas(int $factura): Collection
    {
        return $this->notas($factura)->whereNull('nota.anulado_at')
            ->join('com_lineas as devuelta', fn ($join) => $join->on('devuelta.id_empresa', '=', 'nota.id_empresa')->on('devuelta.id_documento', '=', 'nota.id'))
            ->whereNotNull('devuelta.id_linea_recepcion')->groupBy('devuelta.id_linea_recepcion', 'devuelta.id_linea_orden')
            ->select('devuelta.id_linea_recepcion', 'devuelta.id_linea_orden')
            ->selectRaw('SUM(devuelta.cantidad) AS cantidad, SUM(devuelta.subtotal) AS subtotal, SUM(devuelta.impuesto) AS impuesto')->get();
    }

    private function notas(int $factura): Builder
    {
        return DB::table('com_documentos as nota')->where([
            'nota.id_empresa' => $this->empresa->id(), 'nota.id_documento_origen' => $factura, 'nota.modulo' => 'compras', 'nota.tipo' => 'notas-credito',
        ])->whereExists(fn (Builder $q) => $q->from('com_lineas as partida')->selectRaw('1')
            ->whereColumn('partida.id_documento', 'nota.id')->whereColumn('partida.id_empresa', 'nota.id_empresa')->whereNotNull('partida.id_linea_recepcion'));
    }
}
